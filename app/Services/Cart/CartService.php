<?php

namespace App\Services\Cart;

use App\Models\Catalog\Product;
use App\Models\Catalog\ProductVariant;
use App\Services\Pricing\DiscountResolverService;
use Illuminate\Validation\ValidationException;

class CartService
{
    private const SESSION_KEY = 'fitcaretta_cart';

    public function __construct(private readonly DiscountResolverService $discountResolver)
    {
    }

    public function add(int $productId, int $sizeId, int $colorId, int $quantity): array
    {
        $product = Product::query()
            ->with(['images:id,product_id,image_path,sort_order', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->findOrFail($productId);

        $variant = $this->resolveVariant($product->id, $sizeId, $colorId);
        $this->assertQuantityAvailable($variant, $quantity);

        $pricing = $this->discountResolver->calculateEffectivePrice($product);

        $items = $this->items();
        $key = $this->lineKey($product->id, $sizeId, $colorId);
        $existingQty = (int) ($items[$key]['quantity'] ?? 0);
        $newQty = $existingQty + $quantity;
        $this->assertQuantityAvailable($variant, $newQty);

        $items[$key] = [
            'key' => $key,
            'product_id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'image_path' => $product->main_image_path ?: $product->images->sortBy('sort_order')->first()?->image_path,
            'size_id' => $variant->size_id,
            'size_name' => $variant->size?->name,
            'color_id' => $variant->color_id,
            'color_name' => $variant->color?->name,
            'variant_id' => $variant->id,
            'variant_sku' => $variant->variant_sku,
            'stock_qty' => (int) $variant->stock_qty,
            'quantity' => $newQty,
            'base_price' => (float) $pricing['base_price'],
            'discounted_price' => (float) $pricing['effective_price'],
            'item_total' => round($newQty * (float) $pricing['effective_price'], 2),
        ];

        session()->put(self::SESSION_KEY, $items);

        return $items[$key];
    }

    public function update(string $key, int $quantity): void
    {
        $items = $this->items();
        if (! isset($items[$key])) {
            return;
        }

        if ($quantity <= 0) {
            unset($items[$key]);
            session()->put(self::SESSION_KEY, $items);
            return;
        }

        $line = $items[$key];
        $variant = ProductVariant::query()->findOrFail($line['variant_id']);
        $this->assertQuantityAvailable($variant, $quantity);

        $line['quantity'] = $quantity;
        $line['stock_qty'] = (int) $variant->stock_qty;
        $line['item_total'] = round($quantity * (float) $line['discounted_price'], 2);
        $items[$key] = $line;

        session()->put(self::SESSION_KEY, $items);
    }

    public function remove(string $key): void
    {
        $items = $this->items();
        unset($items[$key]);
        session()->put(self::SESSION_KEY, $items);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function items(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return collect($this->items())->sum('quantity');
    }

    public function summary(): array
    {
        $items = collect($this->items());
        $subtotal = $items->sum(fn ($item) => ((float) $item['base_price']) * ((int) $item['quantity']));
        $total = $items->sum(fn ($item) => (float) $item['item_total']);
        $discountTotal = $subtotal - $total;

        return [
            'subtotal' => round($subtotal, 2),
            'discount_total' => round(max(0, $discountTotal), 2),
            'total' => round($total, 2),
        ];
    }

    private function resolveVariant(int $productId, int $sizeId, int $colorId): ProductVariant
    {
        $variant = ProductVariant::query()
            ->with(['size:id,name', 'color:id,name'])
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->where('size_id', $sizeId)
            ->where('color_id', $colorId)
            ->first();

        if (! $variant) {
            throw ValidationException::withMessages([
                'variant' => 'The selected size/color combination is not available.',
            ]);
        }

        return $variant;
    }

    private function assertQuantityAvailable(ProductVariant $variant, int $quantity): void
    {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be at least 1.',
            ]);
        }

        if ($variant->stock_qty < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Requested quantity exceeds available stock.',
            ]);
        }
    }

    private function lineKey(int $productId, int $sizeId, int $colorId): string
    {
        return $productId . ':' . $sizeId . ':' . $colorId;
    }
}
