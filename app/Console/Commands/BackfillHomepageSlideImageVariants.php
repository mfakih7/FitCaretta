<?php

namespace App\Console\Commands;

use App\Models\HomepageSlide;
use App\Services\Images\ImageVariantsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackfillHomepageSlideImageVariants extends Command
{
    protected $signature = 'fitcaretta:homepage-slides:images:backfill {--dry-run} {--limit=0} {--force}';
    protected $description = 'Generate thumb/medium/hero WebP variants for existing homepage slide images.';

    public function handle(ImageVariantsService $images): int
    {
        $dry = (bool) $this->option('dry-run');
        $limit = (int) $this->option('limit');
        $force = (bool) $this->option('force');
        $disk = Storage::disk('public');

        $slides = HomepageSlide::query()
            ->whereNotNull('image_path')
            ->when($limit > 0, fn ($q) => $q->limit($limit))
            ->get();

        foreach ($slides as $slide) {
            if (! $force && $slide->image_thumb_path && $slide->image_medium_path && $slide->image_hero_path && $slide->image_original_path) {
                continue;
            }

            $src = (string) ($slide->image_original_path ?: $slide->image_hero_path ?: $slide->image_medium_path ?: $slide->image_path);
            if ($src === '' || ! $disk->exists($src)) {
                continue;
            }

            $uuid = (string) Str::uuid();
            $thumb = $slide->image_thumb_path ?: ('homepage-slides/thumb/' . $uuid . '.webp');
            $medium = $slide->image_medium_path ?: ('homepage-slides/medium/' . $uuid . '.webp');
            $hero = $slide->image_hero_path ?: ('homepage-slides/hero/' . $uuid . '.webp');

            if (! $dry) {
                if ($force) {
                    $disk->delete(array_filter([$thumb, $medium, $hero]));
                }
                $images->generateHomepageSlideVariantsFromExistingPublicPath($src, $thumb, $medium, $hero, 480, 1280, 1920);
                $slide->forceFill([
                    'image_original_path' => $slide->image_original_path ?: $src,
                    'image_thumb_path' => $thumb,
                    'image_medium_path' => $medium,
                    'image_hero_path' => $hero,
                    // Keep legacy field pointing to the preferred variant.
                    'image_path' => $hero,
                ])->save();
            }
        }

        $this->info($dry ? 'Dry-run complete.' : 'Backfill complete.');
        return self::SUCCESS;
    }
}

