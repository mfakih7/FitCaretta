<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductGalleryImageUpdateRequest;
use App\Http\Requests\Admin\ProductGalleryImagesBatchUpdateRequest;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\Catalog\Category;
use App\Models\Catalog\Color;
use App\Models\Catalog\Product;
use App\Models\Catalog\ProductImage;
use App\Models\Catalog\ProductType;
use App\Models\Catalog\Size;
use App\Services\Images\ImageVariantsService;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly ImageVariantsService $imageVariants)
    {
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $products = Product::query()
            ->withTrashed()
            ->with(['category:id,name', 'productType:id,name', 'variants:id,product_id,stock_qty,low_stock_threshold'])
            ->when($q !== '', fn ($query) => $query->where('name', 'like', '%' . $q . '%'))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'q'));
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

        $createdProductId = null;

        DB::transaction(function () use ($request, $validated, &$createdProductId): void {
            $mainImagePath = null;
            $thumbPath = null;
            $mediumPath = null;
            $originalPath = null;
            if ($request->hasFile('main_image')) {
                $variants = $this->imageVariants->storeProductImageVariants(
                    $request->file('main_image'),
                    'products/original',
                    'products/thumb',
                    'products/medium',
                    600,
                    1200
                );
                $mainImagePath = $variants['medium_path'];
                $thumbPath = $variants['thumb_path'];
                $mediumPath = $variants['medium_path'];
                $originalPath = $variants['original_path'];
            }

            $product = Product::create([
                ...$this->extractProductData($validated),
                'main_image_path' => $mainImagePath,
                'main_image_thumb_path' => $thumbPath,
                'main_image_medium_path' => $mediumPath,
                'main_image_original_path' => $originalPath,
            ]);
            $createdProductId = $product->id;

            $this->syncVariants($product, $validated['variants'] ?? []);
            $this->storeGalleryImages(
                $product,
                $request->file('gallery_images', []),
                $validated['gallery_image_colors'] ?? []
            );
        });

        return redirect()
            ->route('admin.products.edit', ['product' => $createdProductId])
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
                Storage::disk('public')->delete(array_filter([
                    $product->main_image_path,
                    $product->main_image_thumb_path,
                    $product->main_image_medium_path,
                    $product->main_image_original_path,
                ]));

                $variants = $this->imageVariants->storeProductImageVariants(
                    $request->file('main_image'),
                    'products/original',
                    'products/thumb',
                    'products/medium',
                    600,
                    1200
                );
                $payload['main_image_path'] = $variants['medium_path'];
                $payload['main_image_thumb_path'] = $variants['thumb_path'];
                $payload['main_image_medium_path'] = $variants['medium_path'];
                $payload['main_image_original_path'] = $variants['original_path'];
            }

            $product->update($payload);

            $product->variants()->delete();
            $this->syncVariants($product, $validated['variants'] ?? []);
            $this->storeGalleryImages(
                $product,
                $request->file('gallery_images', []),
                $validated['gallery_image_colors'] ?? []
            );
        });

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Product updated successfully.');
    }

    public function destroyGalleryImage(Product $product, ProductImage $image): RedirectResponse
    {
        if ((int) $image->product_id !== (int) $product->id) {
            abort(404);
        }

        Storage::disk('public')->delete(array_filter([
            $image->image_path,
            $image->image_thumb_path,
            $image->image_medium_path,
            $image->image_original_path,
        ]));
        $image->delete();

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Gallery image deleted successfully.');
    }

    public function updateGalleryImage(ProductGalleryImageUpdateRequest $request, Product $product, ProductImage $image): RedirectResponse
    {
        if ((int) $image->product_id !== (int) $product->id) {
            abort(404);
        }

        $colorId = $request->validated()['color_id'] ?? null;

        $allowedColorIds = $product->variants()
            ->whereNotNull('color_id')
            ->pluck('color_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($colorId !== null && $colorId !== '' && ! $allowedColorIds->contains((int) $colorId)) {
            return back()->withErrors([
                'gallery_image_color_id' => 'Selected color is not used by this product variants.',
            ]);
        }

        $image->update([
            'color_id' => $colorId ?: null,
        ]);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Gallery image updated successfully.');
    }

    public function updateGalleryImagesBatch(ProductGalleryImagesBatchUpdateRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        $allowedColorIds = $product->variants()
            ->whereNotNull('color_id')
            ->pluck('color_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $imageColors = collect($validated['image_colors'] ?? [])
            ->mapWithKeys(fn ($value, $key) => [(int) $key => ($value === '' || $value === null) ? null : (int) $value]);

        $invalidColorIds = $imageColors
            ->filter(fn ($v) => $v !== null)
            ->values()
            ->unique()
            ->diff($allowedColorIds);

        if ($invalidColorIds->isNotEmpty()) {
            return back()->withErrors([
                'image_colors' => 'One or more selected colors are not used by this product variants.',
            ]);
        }

        $productImageIds = $product->images()->pluck('id')->map(fn ($id) => (int) $id)->values();
        $unknownImageIds = $imageColors->keys()->diff($productImageIds);

        if ($unknownImageIds->isNotEmpty()) {
            abort(404);
        }

        foreach ($imageColors as $imageId => $colorId) {
            $product->images()
                ->whereKey($imageId)
                ->update(['color_id' => $colorId]);
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Gallery image colors saved successfully.');
    }

    public function restore(int $product): RedirectResponse
    {
        $model = Product::withTrashed()->findOrFail($product);
        $model->restore();

        return redirect()
            ->route('admin.products.edit', $model)
            ->with('success', 'Product restored successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        Storage::disk('public')->delete(array_filter([
            $product->main_image_path,
            $product->main_image_thumb_path,
            $product->main_image_medium_path,
            $product->main_image_original_path,
        ]));

        foreach ($product->images as $image) {
            Storage::disk('public')->delete(array_filter([
                $image->image_path,
                $image->image_thumb_path,
                $image->image_medium_path,
                $image->image_original_path,
            ]));
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

            // Guard against incomplete rows: placeholders submit empty strings.
            // Null is a valid, intentional value representing "No Size"/"No Color" for simple products.
            $sizeId = $variant['size_id'] ?? null;
            $colorId = $variant['color_id'] ?? null;
            if ($sizeId === '' || $colorId === '') {
                continue;
            }

            $size = (is_numeric($sizeId) && (int) $sizeId > 0) ? Size::find((int) $sizeId) : null;
            $color = (is_numeric($colorId) && (int) $colorId > 0) ? Color::find((int) $colorId) : null;
            $generatedVariantSku = $this->buildVariantSku($product->sku, $size?->code ?? $size?->name, $color?->code ?? $color?->name);

            $product->variants()->create([
                'size_id' => (is_numeric($sizeId) && (int) $sizeId > 0) ? (int) $sizeId : null,
                'color_id' => (is_numeric($colorId) && (int) $colorId > 0) ? (int) $colorId : null,
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
        $sizeId = (string) ($variant['size_id'] ?? '');
        $colorId = (string) ($variant['color_id'] ?? '');
        $isDefaultNone = ($sizeId === '__none__') && ($colorId === '__none__');

        // A row with both defaults (No Size + No Color) is only considered "content" if the admin
        // actually set meaningful fields (e.g. stock/SKU/price). This avoids auto-creating duplicates
        // from initial empty rows while still supporting simple products intentionally.
        return (! $isDefaultNone && (filled($variant['size_id'] ?? null) || filled($variant['color_id'] ?? null)))
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

    private function storeGalleryImages(Product $product, array $files, array $colorIds): void
    {
        $nextSort = (int) $product->images()->max('sort_order');

        foreach ($files as $index => $file) {
            if (! $file) {
                continue;
            }

            $nextSort++;
            $variants = $this->imageVariants->storeProductImageVariants(
                $file,
                'products/gallery/original',
                'products/gallery/thumb',
                'products/gallery/medium',
                600,
                1200
            );
            $path = $variants['medium_path'];

            $product->images()->create([
                'image_path' => $path,
                'image_thumb_path' => $variants['thumb_path'],
                'image_medium_path' => $variants['medium_path'],
                'image_original_path' => $variants['original_path'],
                'sort_order' => $nextSort,
                'color_id' => isset($colorIds[$index]) && $colorIds[$index] ? (int) $colorIds[$index] : null,
            ]);
        }
    }
}
