<?php

namespace App\Services\Pricing;

use App\Models\Catalog\Discount;
use App\Models\Catalog\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class DiscountResolverService
{
    /**
     * Fetch active discounts once (optionally cached briefly).
     *
     * Note: We cache per-minute because start/end windows are time-based.
     */
    public function activeDiscounts(?Carbon $at = null): Collection
    {
        $at = $at ?: now();
        $cacheKey = 'discounts.active.v1.' . $at->format('YmdHi');

        // Short TTL keeps pricing accurate while killing repeat queries on grids.
        return Cache::remember($cacheKey, 90, function () use ($at) {
            return $this->activeDiscountsQuery($at)
                ->orderByDesc('priority')
                ->orderByDesc('id')
                ->get();
        });
    }

    public function resolveForProduct(Product $product, ?Carbon $at = null): ?Discount
    {
        $at = $at ?: now();
        $activeDiscounts = $this->activeDiscounts($at);

        // If relations are loaded, resolve in-memory (no queries in loops).
        if ($product->relationLoaded('discounts') && $product->relationLoaded('category')) {
            return $this->resolveForProductWithActiveDiscounts($product, $activeDiscounts);
        }

        // Fallback for any call sites that didn't preload relationships yet.
        return $this->resolveForProductQueryFallback($product, $activeDiscounts, $at);
    }

    public function resolveForProductWithActiveDiscounts(Product $product, Collection $activeDiscounts): ?Discount
    {
        $activeIds = $activeDiscounts->pluck('id')->flip();

        $productDiscounts = $product->relationLoaded('discounts')
            ? $product->discounts->filter(fn ($d) => isset($activeIds[$d->id]))
            : collect();

        $bestProduct = $this->pickBestDiscount($productDiscounts);
        if ($bestProduct) {
            return $bestProduct;
        }

        $categoryDiscounts = ($product->category && $product->category->relationLoaded('discounts'))
            ? $product->category->discounts->filter(fn ($d) => isset($activeIds[$d->id]))
            : collect();

        $bestCategory = $this->pickBestDiscount($categoryDiscounts);
        if ($bestCategory) {
            return $bestCategory;
        }

        $globals = $activeDiscounts->where('scope', Discount::SCOPE_GLOBAL);
        return $this->pickBestDiscount($globals);
    }

    public function calculateEffectivePrice(Product $product, ?Carbon $at = null): array
    {
        $basePrice = (float) $product->base_price;
        $discount = $this->resolveForProduct($product, $at);

        if (! $discount) {
            return [
                'base_price' => round($basePrice, 2),
                'discount_amount' => 0.0,
                'effective_price' => round($basePrice, 2),
                'discount' => null,
            ];
        }

        $discountAmount = $this->calculateDiscountAmount($basePrice, $discount->type, (float) $discount->value);
        $effectivePrice = max(0, $basePrice - $discountAmount);

        return [
            'base_price' => round($basePrice, 2),
            'discount_amount' => round($discountAmount, 2),
            'effective_price' => round($effectivePrice, 2),
            'discount' => $discount,
        ];
    }

    public function calculateEffectivePriceWithActiveDiscounts(Product $product, Collection $activeDiscounts, ?Carbon $at = null): array
    {
        $basePrice = (float) $product->base_price;
        $discount = $this->resolveForProductWithActiveDiscounts($product, $activeDiscounts);

        if (! $discount) {
            return [
                'base_price' => round($basePrice, 2),
                'discount_amount' => 0.0,
                'effective_price' => round($basePrice, 2),
                'discount' => null,
            ];
        }

        $discountAmount = $this->calculateDiscountAmount($basePrice, $discount->type, (float) $discount->value);
        $effectivePrice = max(0, $basePrice - $discountAmount);

        return [
            'base_price' => round($basePrice, 2),
            'discount_amount' => round($discountAmount, 2),
            'effective_price' => round($effectivePrice, 2),
            'discount' => $discount,
        ];
    }

    public function calculateDiscountAmount(float $amount, string $type, float $value): float
    {
        if ($type === Discount::TYPE_PERCENTAGE) {
            return max(0, min(100, $value)) / 100 * $amount;
        }

        return min($amount, max(0, $value));
    }

    private function pickBestDiscount(Collection $discounts): ?Discount
    {
        if ($discounts->isEmpty()) {
            return null;
        }

        return $discounts
            ->sortByDesc(fn (Discount $d) => [(int) ($d->priority ?? 0), (int) $d->id])
            ->first();
    }

    private function resolveForProductQueryFallback(Product $product, Collection $activeDiscounts, Carbon $at): ?Discount
    {
        $activeIds = $activeDiscounts->pluck('id')->all();

        $productDiscount = $product->discounts()
            ->whereIn('discounts.id', $activeIds)
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->first();
        if ($productDiscount) {
            return $productDiscount;
        }

        $categoryDiscount = $product->category?->discounts()
            ->whereIn('discounts.id', $activeIds)
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->first();
        if ($categoryDiscount) {
            return $categoryDiscount;
        }

        return $this->activeDiscountsQuery($at)
            ->where('scope', Discount::SCOPE_GLOBAL)
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->first();
    }

    private function activeDiscountsQuery(Carbon $at)
    {
        return Discount::query()
            ->where('is_active', true)
            ->where(function ($q) use ($at) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $at);
            })
            ->where(function ($q) use ($at) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $at);
            });
    }
}
