<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomepageSlide extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'badge',
        'button_one_text',
        'button_one_link',
        'button_two_text',
        'button_two_link',
        'image_path',
        'image_original_path',
        'image_thumb_path',
        'image_medium_path',
        'image_hero_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getImageUrlAttribute(): ?string
    {
        // Prefer optimized hero variant when available.
        $path = (string) ($this->image_hero_path ?: $this->image_medium_path ?: $this->image_path ?: '');
        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', 'data:', '/'])) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . ltrim($path, '/'));
        }

        return null;
    }

    public function getImageThumbUrlAttribute(): ?string
    {
        return $this->resolvePublicImageUrl((string) ($this->image_thumb_path ?: $this->image_medium_path ?: $this->image_path ?: ''));
    }

    public function getImageMediumUrlAttribute(): ?string
    {
        return $this->resolvePublicImageUrl((string) ($this->image_medium_path ?: $this->image_hero_path ?: $this->image_path ?: ''));
    }

    public function getImageHeroUrlAttribute(): ?string
    {
        return $this->resolvePublicImageUrl((string) ($this->image_hero_path ?: $this->image_medium_path ?: $this->image_path ?: ''));
    }

    public function getImageOriginalUrlAttribute(): ?string
    {
        return $this->resolvePublicImageUrl((string) ($this->image_original_path ?: $this->image_path ?: ''));
    }

    private function resolvePublicImageUrl(string $path): ?string
    {
        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', 'data:', '/'])) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . ltrim($path, '/'));
        }

        return null;
    }
}

