<?php

namespace App\Services\Images;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageVariantsService
{
    public function __construct(
        private readonly int $webpQuality = 86
    ) {
    }

    /**
     * Store original + generate thumb/medium WebP variants.
     *
     * Returns public-disk relative paths:
     * - original_path: original uploaded file
     * - thumb_path: webp resized to $thumbWidth
     * - medium_path: webp resized to $mediumWidth
     */
    public function storeProductImageVariants(
        UploadedFile $file,
        string $originalDir,
        string $thumbDir,
        string $mediumDir,
        int $thumbWidth = 600,
        int $mediumWidth = 1200
    ): array {
        $disk = Storage::disk('public');

        $uuid = (string) Str::uuid();
        $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');

        $originalPath = $file->storeAs($originalDir, $uuid . '.' . $ext, 'public');
        $absOriginal = $disk->path($originalPath);

        $thumbPath = $thumbDir . '/' . $uuid . '.webp';
        $mediumPath = $mediumDir . '/' . $uuid . '.webp';

        $thumbBytes = $this->toWebpBytes($absOriginal, $thumbWidth);
        $mediumBytes = $this->toWebpBytes($absOriginal, $mediumWidth);

        $disk->put($thumbPath, $thumbBytes, ['visibility' => 'public']);
        $disk->put($mediumPath, $mediumBytes, ['visibility' => 'public']);

        return [
            'original_path' => $originalPath,
            'thumb_path' => $thumbPath,
            'medium_path' => $mediumPath,
        ];
    }

    public function generateVariantsFromExistingPublicPath(
        string $existingPublicPath,
        string $thumbPath,
        string $mediumPath,
        int $thumbWidth = 600,
        int $mediumWidth = 1200
    ): void {
        $disk = Storage::disk('public');
        if (! $disk->exists($existingPublicPath)) {
            return;
        }
        $absOriginal = $disk->path($existingPublicPath);

        if (! $disk->exists($thumbPath)) {
            $disk->put($thumbPath, $this->toWebpBytes($absOriginal, $thumbWidth), ['visibility' => 'public']);
        }
        if (! $disk->exists($mediumPath)) {
            $disk->put($mediumPath, $this->toWebpBytes($absOriginal, $mediumWidth), ['visibility' => 'public']);
        }
    }

    /**
     * Store original + generate thumb/medium/hero WebP variants for homepage slides.
     */
    public function storeHomepageSlideImageVariants(
        UploadedFile $file,
        int $thumbWidth = 480,
        int $mediumWidth = 1280,
        int $heroWidth = 1920
    ): array {
        $disk = Storage::disk('public');

        $uuid = (string) Str::uuid();
        $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');

        $originalDir = 'homepage-slides/original';
        $thumbDir = 'homepage-slides/thumb';
        $mediumDir = 'homepage-slides/medium';
        $heroDir = 'homepage-slides/hero';

        $originalPath = $file->storeAs($originalDir, $uuid . '.' . $ext, 'public');
        $absOriginal = $disk->path($originalPath);

        $thumbPath = $thumbDir . '/' . $uuid . '.webp';
        $mediumPath = $mediumDir . '/' . $uuid . '.webp';
        $heroPath = $heroDir . '/' . $uuid . '.webp';

        $disk->put($thumbPath, $this->toWebpBytes($absOriginal, $thumbWidth), ['visibility' => 'public']);
        $disk->put($mediumPath, $this->toWebpBytes($absOriginal, $mediumWidth), ['visibility' => 'public']);
        $disk->put($heroPath, $this->toWebpBytes($absOriginal, $heroWidth), ['visibility' => 'public']);

        return [
            'original_path' => $originalPath,
            'thumb_path' => $thumbPath,
            'medium_path' => $mediumPath,
            'hero_path' => $heroPath,
        ];
    }

    /**
     * Generate slide variants from an existing public disk path.
     */
    public function generateHomepageSlideVariantsFromExistingPublicPath(
        string $existingPublicPath,
        string $thumbPath,
        string $mediumPath,
        string $heroPath,
        int $thumbWidth = 480,
        int $mediumWidth = 1280,
        int $heroWidth = 1920
    ): void {
        $disk = Storage::disk('public');
        if (! $disk->exists($existingPublicPath)) {
            return;
        }
        $absOriginal = $disk->path($existingPublicPath);

        if (! $disk->exists($thumbPath)) {
            $disk->put($thumbPath, $this->toWebpBytes($absOriginal, $thumbWidth), ['visibility' => 'public']);
        }
        if (! $disk->exists($mediumPath)) {
            $disk->put($mediumPath, $this->toWebpBytes($absOriginal, $mediumWidth), ['visibility' => 'public']);
        }
        if (! $disk->exists($heroPath)) {
            $disk->put($heroPath, $this->toWebpBytes($absOriginal, $heroWidth), ['visibility' => 'public']);
        }
    }

    private function toWebpBytes(string $absPath, int $targetWidth): string
    {
        $raw = @file_get_contents($absPath);
        if ($raw === false) {
            return '';
        }

        $info = @getimagesizefromstring($raw);
        if (! is_array($info) || empty($info[0]) || empty($info[1])) {
            return '';
        }

        [$w, $h] = [(int) $info[0], (int) $info[1]];
        if ($w <= 0 || $h <= 0) {
            return '';
        }

        $src = @imagecreatefromstring($raw);
        if (! $src) {
            return '';
        }

        $newW = min($targetWidth, $w);
        $newH = (int) max(1, round($h * ($newW / $w)));

        $dst = imagecreatetruecolor($newW, $newH);
        if (! $dst) {
            imagedestroy($src);
            return '';
        }

        // Preserve alpha for PNG/WebP inputs.
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);
        imagealphablending($dst, true);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);

        ob_start();
        imagewebp($dst, null, $this->webpQuality);
        $bytes = (string) ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        return $bytes;
    }
}

