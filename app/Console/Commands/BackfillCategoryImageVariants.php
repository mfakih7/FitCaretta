<?php

namespace App\Console\Commands;

use App\Models\Catalog\Category;
use App\Services\Images\ImageVariantsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackfillCategoryImageVariants extends Command
{
    protected $signature = 'fitcaretta:categories:images:backfill {--dry-run} {--limit=0} {--force}';
    protected $description = 'Generate thumb/medium WebP variants for existing category images.';

    public function handle(ImageVariantsService $images): int
    {
        $dry = (bool) $this->option('dry-run');
        $limit = (int) $this->option('limit');
        $force = (bool) $this->option('force');
        $disk = Storage::disk('public');

        $cats = Category::query()
            ->whereNotNull('image_path')
            ->when($limit > 0, fn ($q) => $q->limit($limit))
            ->get();

        foreach ($cats as $c) {
            if (! $force && $c->image_thumb_path && $c->image_medium_path && $c->image_original_path) {
                continue;
            }

            $src = (string) ($c->image_original_path ?: $c->image_medium_path ?: $c->image_path);
            if ($src === '' || ! $disk->exists($src)) {
                continue;
            }

            $uuid = (string) Str::uuid();
            $thumb = $c->image_thumb_path ?: ('categories/thumb/' . $uuid . '.webp');
            $medium = $c->image_medium_path ?: ('categories/medium/' . $uuid . '.webp');

            if (! $dry) {
                if ($force) {
                    $disk->delete(array_filter([$thumb, $medium]));
                }
                $images->generateVariantsFromExistingPublicPath($src, $thumb, $medium, 700, 1400);
                $c->forceFill([
                    'image_original_path' => $c->image_original_path ?: $src,
                    'image_thumb_path' => $thumb,
                    'image_medium_path' => $medium,
                    'image_path' => $medium, // ensure legacy field points to optimized by default
                ])->save();
            }
        }

        $this->info($dry ? 'Dry-run complete.' : 'Backfill complete.');
        return self::SUCCESS;
    }
}

