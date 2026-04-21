<?php

namespace App\Console\Commands;

use App\Models\Catalog\Product;
use App\Models\Catalog\ProductImage;
use App\Services\Images\ImageVariantsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackfillImageVariants extends Command
{
    protected $signature = 'fitcaretta:images:backfill {--dry-run} {--limit=0} {--force}';
    protected $description = 'Generate thumb/medium WebP variants for existing product images.';

    public function handle(ImageVariantsService $images): int
    {
        $dry = (bool) $this->option('dry-run');
        $limit = (int) $this->option('limit');
        $force = (bool) $this->option('force');
        $disk = Storage::disk('public');

        $this->info('Backfilling product main images...');
        $products = Product::query()
            ->whereNotNull('main_image_path')
            ->when($limit > 0, fn ($q) => $q->limit($limit))
            ->get();

        foreach ($products as $p) {
            if (! $force && $p->main_image_thumb_path && $p->main_image_medium_path && $p->main_image_original_path) {
                continue;
            }

            $src = (string) ($p->main_image_original_path ?: $p->main_image_path);
            if ($src === '' || ! $disk->exists($src)) {
                continue;
            }

            $uuid = (string) Str::uuid();
            $thumb = $p->main_image_thumb_path ?: ('products/thumb/' . $uuid . '.webp');
            $medium = $p->main_image_medium_path ?: ('products/medium/' . $uuid . '.webp');

            if (! $dry) {
                if ($force) {
                    $disk->delete(array_filter([$thumb, $medium]));
                }
                $images->generateVariantsFromExistingPublicPath($src, $thumb, $medium, 600, 1200);
                $p->forceFill([
                    'main_image_original_path' => $p->main_image_original_path ?: $src,
                    'main_image_thumb_path' => $thumb,
                    'main_image_medium_path' => $medium,
                    'main_image_path' => $medium, // ensure listing uses optimized by default
                ])->save();
            }
        }

        $this->info('Backfilling gallery images...');
        $imgs = ProductImage::query()
            ->whereNotNull('image_path')
            ->when($limit > 0, fn ($q) => $q->limit($limit))
            ->get();

        foreach ($imgs as $img) {
            if (! $force && $img->image_thumb_path && $img->image_medium_path && $img->image_original_path) {
                continue;
            }

            $src = (string) ($img->image_original_path ?: $img->image_path);
            if ($src === '' || ! $disk->exists($src)) {
                continue;
            }

            $uuid = (string) Str::uuid();
            $thumb = $img->image_thumb_path ?: ('products/gallery/thumb/' . $uuid . '.webp');
            $medium = $img->image_medium_path ?: ('products/gallery/medium/' . $uuid . '.webp');

            if (! $dry) {
                if ($force) {
                    $disk->delete(array_filter([$thumb, $medium]));
                }
                $images->generateVariantsFromExistingPublicPath($src, $thumb, $medium, 600, 1200);
                $img->forceFill([
                    'image_original_path' => $img->image_original_path ?: $src,
                    'image_thumb_path' => $thumb,
                    'image_medium_path' => $medium,
                    'image_path' => $medium, // ensure defaults use optimized
                ])->save();
            }
        }

        $this->info($dry ? 'Dry-run complete.' : 'Backfill complete.');
        return self::SUCCESS;
    }
}

