<?php

namespace App\Console\Commands;

use App\Models\Catalog\Product;
use App\Models\Catalog\ProductVariant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairSimpleProductVariants extends Command
{
    protected $signature = 'fitcaretta:repair-simple-variants
                            {--dry-run : Show what would change without writing}
                            {--include-inactive-products : Also repair inactive products}';

    protected $description = 'Backfill a simple (No Size/No Color) active variant for products missing active variants.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $includeInactive = (bool) $this->option('include-inactive-products');

        $products = Product::query()
            ->when(! $includeInactive, fn ($q) => $q->where('is_active', true))
            ->whereDoesntHave('variants', fn ($q) => $q->where('is_active', true))
            ->orderBy('id')
            ->get(['id', 'name', 'is_active']);

        if ($products->isEmpty()) {
            $this->info('No products found that are missing active variants.');
            return self::SUCCESS;
        }

        $this->line('Products missing active variants: ' . $products->count());

        $created = 0;
        $skippedHasVariants = 0;
        $skippedAlreadyHasSimple = 0;

        foreach ($products as $product) {
            // Safety: only auto-repair products that have NO variants at all.
            // If variants exist but are all inactive, we don't guess—admin should review intent.
            $totalVariants = ProductVariant::query()->where('product_id', $product->id)->count();
            if ($totalVariants > 0) {
                $skippedHasVariants++;
                continue;
            }

            if ($dryRun) {
                $this->line("DRY-RUN: would create simple variant for #{$product->id} {$product->name}");
                continue;
            }

            DB::transaction(function () use ($product, &$created, &$skippedAlreadyHasSimple): void {
                // Re-check within transaction to avoid races.
                $exists = ProductVariant::query()
                    ->where('product_id', $product->id)
                    ->where('is_active', true)
                    ->exists();
                if ($exists) {
                    $skippedAlreadyHasSimple++;
                    return;
                }

                $product->variants()->create([
                    'size_id' => null,
                    'color_id' => null,
                    'variant_sku' => null,
                    'price_override' => null,
                    'stock_qty' => 0,
                    'low_stock_threshold' => 5,
                    'is_active' => true,
                ]);

                $created++;
            });
        }

        $this->newLine();
        $this->info('Repair summary');
        $this->line('- created: ' . $created);
        $this->line('- skipped (has variants but none active): ' . $skippedHasVariants);
        $this->line('- skipped (became active during run): ' . $skippedAlreadyHasSimple);

        if ($dryRun) {
            $this->newLine();
            $this->comment('Dry-run mode: no changes were written.');
        }

        return self::SUCCESS;
    }
}

