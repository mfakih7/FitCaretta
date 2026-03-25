<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\Catalog\Category;
use App\Models\Catalog\Color;
use App\Models\Catalog\Product;
use App\Models\Catalog\ProductImage;
use App\Models\Catalog\ProductType;
use App\Models\Catalog\Size;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with(['category:id,name', 'productType:id,name', 'variants:id,product_id,stock_qty,low_stock_threshold'])
            ->latest('id')
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create', $this->formData());
    }

    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = $this->generateUniqueSlug($validated['slug'] ?? null, $validated['name']);
        $this->assertNoDuplicateVariantCombinations($validated['variants'] ?? []);

        DB::transaction(function () use ($request, $validated): void {
            $mainImagePath = null;
            if ($request->hasFile('main_image')) {
                $mainImagePath = $request->file('main_image')->store('products/main', 'public');
            }

            $product = Product::create([
                ...$this->extractProductData($validated),
                'main_image_path' => $mainImagePath,
            ]);

            $this->syncVariants($product, $validated['variants'] ?? []);
            $this->storeGalleryImages($product, $request->file('gallery_images', []));
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $product->load(['variants', 'images']);

        return view('admin.products.edit', [
            ...$this->formData(),
            'product' => $product,
        ]);
    }

    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = $this->generateUniqueSlug($validated['slug'] ?? null, $validated['name'], $product->id);
        $this->assertNoDuplicateVariantCombinations($validated['variants'] ?? []);

        DB::transaction(function () use ($request, $product, $validated): void {
            $payload = $this->extractProductData($validated);

            if ($request->hasFile('main_image')) {
                if ($product->main_image_path) {
                    Storage::disk('public')->delete($product->main_image_path);
                }
                $payload['main_image_path'] = $request->file('main_image')->store('products/main', 'public');
            }

            $product->update($payload);

            $product->variants()->delete();
            $this->syncVariants($product, $validated['variants'] ?? []);
            $this->storeGalleryImages($product, $request->file('gallery_images', []));
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroyGalleryImage(Product $product, ProductImage $image): RedirectResponse
    {
        if ((int) $image->product_id !== (int) $product->id) {
            abort(404);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Gallery image deleted successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->main_image_path) {
            Storage::disk('public')->delete($product->main_image_path);
        }

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'productTypes' => ProductType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'sizes' => Size::query()->where('is_active', true)->orderBy('sort_order')->get(['id', 'name']),
            'colors' => Color::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ];
    }

    private function extractProductData(array $validated): array
    {
        return [
            'category_id' => $validated['category_id'],
            'product_type_id' => $validated['product_type_id'] ?? null,
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'sku' => $validated['sku'],
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'gender_target' => $validated['gender_target'],
            'base_price' => $validated['base_price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'is_active' => $validated['is_active'],
            'is_featured' => $validated['is_featured'] ?? false,
            'is_new_arrival' => $validated['is_new_arrival'] ?? false,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
        ];
    }

    private function syncVariants(Product $product, array $variants): void
    {
        foreach ($variants as $variant) {
            if (! $this->variantHasContent($variant)) {
                continue;
            }

            $size = isset($variant['size_id']) && $variant['size_id'] ? Size::find($variant['size_id']) : null;
            $color = isset($variant['color_id']) && $variant['color_id'] ? Color::find($variant['color_id']) : null;
            $generatedVariantSku = $this->buildVariantSku($product->sku, $size?->code ?? $size?->name, $color?->code ?? $color?->name);

            $product->variants()->create([
                'size_id' => $variant['size_id'] ?: null,
                'color_id' => $variant['color_id'] ?: null,
                'variant_sku' => $variant['variant_sku'] ?: $generatedVariantSku,
                'price_override' => $variant['price_override'] ?: null,
                'stock_qty' => $variant['stock_qty'] ?? 0,
                'low_stock_threshold' => $variant['low_stock_threshold'] ?? 5,
                'is_active' => (bool) ($variant['is_active'] ?? true),
            ]);
        }
    }

    private function variantHasContent(array $variant): bool
    {
        return filled($variant['size_id'] ?? null)
            || filled($variant['color_id'] ?? null)
            || filled($variant['variant_sku'] ?? null)
            || filled($variant['price_override'] ?? null)
            || (int) ($variant['stock_qty'] ?? 0) > 0;
    }

    private function buildVariantSku(string $productSku, ?string $sizeCodeOrName, ?string $colorCodeOrName): ?string
    {
        if (! $sizeCodeOrName && ! $colorCodeOrName) {
            return null;
        }

        $parts = [strtoupper(trim($productSku))];
        if ($sizeCodeOrName) {
            $parts[] = strtoupper(str_replace(' ', '', trim($sizeCodeOrName)));
        }
        if ($colorCodeOrName) {
            $parts[] = strtoupper(str_replace(' ', '', trim($colorCodeOrName)));
        }

        return implode('-', $parts);
    }

    private function generateUniqueSlug(?string $slugInput, string $name, ?int $ignoreProductId = null): string
    {
        $base = Str::slug($slugInput ?: $name);
        $base = $base !== '' ? $base : Str::slug('product-' . now()->timestamp);

        $slug = $base;
        $counter = 1;

        while (Product::query()
            ->when($ignoreProductId, fn ($q) => $q->where('id', '!=', $ignoreProductId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    private function assertNoDuplicateVariantCombinations(array $variants): void
    {
        $seen = [];

        foreach ($variants as $index => $variant) {
            if (! $this->variantHasContent($variant)) {
                continue;
            }

            $size = $variant['size_id'] ?? 'null';
            $color = $variant['color_id'] ?? 'null';
            $key = $size . '-' . $color;

            if (isset($seen[$key])) {
                throw new HttpResponseException(
                    back()
                        ->withInput()
                        ->withErrors([
                            "variants.$index.size_id" => 'Duplicate size/color combination is not allowed.',
                        ])
                );
            }

            $seen[$key] = true;
        }
    }

    private function storeGalleryImages(Product $product, array $files): void
    {
        $nextSort = (int) $product->images()->max('sort_order');

        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $nextSort++;
            $path = $file->store('products/gallery', 'public');

            $product->images()->create([
                'image_path' => $path,
                'sort_order' => $nextSort,
            ]);
        }
    }
}
