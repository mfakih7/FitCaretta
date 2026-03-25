<?php

namespace App\Services\Pricing;

use App\Models\Catalog\Discount;
use App\Models\Catalog\Product;
use Illuminate\Support\Carbon;

class DiscountResolverService
{
    public function resolveForProduct(Product $product, ?Carbon $at = null): ?Discount
    {
        $at = $at ?: now();
        $activeDiscounts = $this->activeDiscountsQuery($at);

        $productDiscount = $product->discounts()
            ->whereIn('discounts.id', (clone $activeDiscounts)->pluck('discounts.id'))
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->first();
        if ($productDiscount) {
            return $productDiscount;
        }

        $categoryDiscount = $product->category?->discounts()
            ->whereIn('discounts.id', (clone $activeDiscounts)->pluck('discounts.id'))
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->first();
        if ($categoryDiscount) {
            return $categoryDiscount;
        }

        return (clone $activeDiscounts)
            ->where('scope', Discount::SCOPE_GLOBAL)
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->first();
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

    public function calculateDiscountAmount(float $amount, string $type, float $value): float
    {
        if ($type === Discount::TYPE_PERCENTAGE) {
            return max(0, min(100, $value)) / 100 * $amount;
        }

        return min($amount, max(0, $value));
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
