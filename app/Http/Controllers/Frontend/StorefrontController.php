<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Category;
use App\Models\Catalog\Color;
use App\Models\Catalog\Discount;
use App\Models\Catalog\Product;
use App\Models\Catalog\ProductType;
use App\Models\Catalog\Size;
use App\Services\Pricing\DiscountResolverService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function __construct(private readonly DiscountResolverService $discountResolver)
    {
    }

    public function home(): View
    {
        $featuredProducts = $this->attachPricing(
            Product::query()
                ->with(['category:id,name', 'images:id,product_id,image_path,sort_order'])
                ->where('is_active', true)
                ->where('is_featured', true)
                ->latest('id')
                ->limit(8)
                ->get()
        );

        $newArrivals = $this->attachPricing(
            Product::query()
                ->with(['category:id,name', 'images:id,product_id,image_path,sort_order'])
                ->where('is_active', true)
                ->where('is_new_arrival', true)
                ->latest('id')
                ->limit(8)
                ->get()
        );

        $featuredCategories = Category::query()
            ->where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return view('frontend.home', compact('featuredProducts', 'newArrivals', 'featuredCategories'));
    }

    public function shop(Request $request): View
    {
        $products = $this->buildCatalogQuery($request)->paginate(12)->withQueryString();
        $products->setCollection($this->applyPricingAndSort($products->getCollection(), (string) $request->query('sort', 'latest')));

        return view('frontend.shop.index', [
            'title' => 'Shop',
            'products' => $products,
            ...$this->filtersData(),
        ]);
    }

    public function newArrivals(Request $request): View
    {
        $products = $this->buildCatalogQuery($request)
            ->where('is_new_arrival', true)
            ->paginate(12)
            ->withQueryString();

        $products->setCollection(
            $this->applyPricingAndSort($products->getCollection(), (string) $request->query('sort', 'latest'))
        );

        return view('frontend.shop.index', [
            'title' => 'New Arrivals',
            'products' => $products,
            ...$this->filtersData(),
        ]);
    }

    public function men(Request $request): View
    {
        $request->merge(['gender' => 'men']);
        $products = $this->buildCatalogQuery($request)->paginate(12)->withQueryString();
        $products->setCollection($this->applyPricingAndSort($products->getCollection(), (string) $request->query('sort', 'latest')));

        return view('frontend.shop.index', [
            'title' => 'Men',
            'products' => $products,
            ...$this->filtersData(),
        ]);
    }

    public function women(Request $request): View
    {
        $request->merge(['gender' => 'women']);
        $products = $this->buildCatalogQuery($request)->paginate(12)->withQueryString();
        $products->setCollection($this->applyPricingAndSort($products->getCollection(), (string) $request->query('sort', 'latest')));

        return view('frontend.shop.index', [
            'title' => 'Women',
            'products' => $products,
            ...$this->filtersData(),
        ]);
    }

    public function category(Request $request, string $slug): View
    {
        $category = Category::query()->where('is_active', true)->where('slug', $slug)->firstOrFail();
        $request->merge(['category_id' => $category->id]);

        $products = $this->buildCatalogQuery($request)->paginate(12)->withQueryString();
        $products->setCollection($this->applyPricingAndSort($products->getCollection(), (string) $request->query('sort', 'latest')));

        return view('frontend.shop.index', [
            'title' => 'Category: ' . $category->name,
            'products' => $products,
            'currentCategory' => $category,
            ...$this->filtersData(),
        ]);
    }

    public function productDetails(string $slug): View
    {
        $product = Product::query()
            ->with([
                'category:id,name,slug',
                'productType:id,name',
                'images:id,product_id,image_path,sort_order',
                'variants' => fn ($q) => $q->where('is_active', true)->with(['size:id,name', 'color:id,name,hex_code']),
            ])
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        $pricing = $this->discountResolver->calculateEffectivePrice($product);

        $relatedProducts = $this->attachPricing(
            Product::query()
                ->with(['category:id,name', 'images:id,product_id,image_path,sort_order'])
                ->where('is_active', true)
                ->where('id', '!=', $product->id)
                ->where('category_id', $product->category_id)
                ->latest('id')
                ->limit(4)
                ->get()
        );

        return view('frontend.products.show', compact('product', 'pricing', 'relatedProducts'));
    }

    public function search(Request $request): View
    {
        $term = (string) $request->query('q', '');

        $products = $this->buildCatalogQuery($request)
            ->when($term !== '', function (Builder $query) use ($term) {
                $query->where(function (Builder $q) use ($term) {
                    $q->where('name', 'like', '%' . $term . '%')
                        ->orWhere('sku', 'like', '%' . $term . '%')
                        ->orWhere('short_description', 'like', '%' . $term . '%')
                        ->orWhere('description', 'like', '%' . $term . '%');
                });
            })
            ->paginate(12)
            ->withQueryString();

        $products->setCollection($this->applyPricingAndSort($products->getCollection(), (string) $request->query('sort', 'latest')));

        return view('frontend.shop.search', [
            'products' => $products,
            'term' => $term,
            ...$this->filtersData(),
        ]);
    }

    public function offers(Request $request): View
    {
        $hasGlobalDiscount = Discount::query()
            ->where('is_active', true)
            ->where('scope', Discount::SCOPE_GLOBAL)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
            ->exists();

        $query = $this->buildCatalogQuery($request);
        if (! $hasGlobalDiscount) {
            $query->where(function (Builder $q) {
                $q->whereHas('discounts', fn ($d) => $this->applyActiveDiscountConstraint($d))
                    ->orWhereHas('category.discounts', fn ($d) => $this->applyActiveDiscountConstraint($d));
            });
        }

        $products = $query->paginate(12)->withQueryString();
        $products->setCollection(
            $this->applyPricingAndSort($products->getCollection(), (string) $request->query('sort', 'latest'))
                ->filter(fn ($product) => $product->pricing['discount'] !== null)
                ->values()
        );

        return view('frontend.shop.offers', [
            'products' => $products,
            ...$this->filtersData(),
        ]);
    }

    private function buildCatalogQuery(Request $request): Builder
    {
        $query = Product::query()
            ->with(['category:id,name,slug', 'productType:id,name', 'images:id,product_id,image_path,sort_order'])
            ->where('is_active', true);

        if ($gender = $request->query('gender')) {
            $query->where('gender_target', $gender);
        }

        if ($categoryId = $request->query('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($typeId = $request->query('product_type_id')) {
            $query->where('product_type_id', $typeId);
        }

        if ($sizeId = $request->query('size_id')) {
            $query->whereHas('variants', fn ($q) => $q->where('size_id', $sizeId)->where('stock_qty', '>', 0));
        }

        if ($colorId = $request->query('color_id')) {
            $query->whereHas('variants', fn ($q) => $q->where('color_id', $colorId)->where('stock_qty', '>', 0));
        }

        $sort = (string) $request->query('sort', 'latest');
        if ($sort === 'price_asc' || $sort === 'discounted_price_asc') {
            $query->orderBy('base_price');
        } elseif ($sort === 'price_desc' || $sort === 'discounted_price_desc') {
            $query->orderByDesc('base_price');
        } else {
            $query->latest('id');
        }

        return $query;
    }

    private function filtersData(): array
    {
        return [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug']),
            'productTypes' => ProductType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'sizes' => Size::query()->where('is_active', true)->orderBy('sort_order')->get(['id', 'name']),
            'colors' => Color::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'hex_code']),
        ];
    }

    private function attachPricing($products)
    {
        return $products->map(function (Product $product) {
            $product->pricing = $this->discountResolver->calculateEffectivePrice($product);
            return $product;
        });
    }

    private function applyPricingAndSort($products, string $sort)
    {
        $withPricing = $this->attachPricing($products);

        if ($sort === 'discounted_price_asc') {
            return $withPricing->sortBy(fn (Product $p) => $p->pricing['effective_price'])->values();
        }
        if ($sort === 'discounted_price_desc') {
            return $withPricing->sortByDesc(fn (Product $p) => $p->pricing['effective_price'])->values();
        }

        return $withPricing;
    }

    private function applyActiveDiscountConstraint(Builder $query): void
    {
        $now = Carbon::now();
        $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            });
    }
}
