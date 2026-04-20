<?php

namespace App\Models\Catalog;

use App\Models\Catalog\Color;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color_id',
        'image_path',
        'image_thumb_path',
        'image_medium_path',
        'image_original_path',
        'alt_text',
        'sort_order',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function getImageUrlAttribute(): string
    {
        // Prefer optimized medium variant when available.
        $path = $this->image_medium_path ?: $this->image_path;

        if (! $path) {
            return asset(Product::DEFAULT_PLACEHOLDER);
        }

        if (Str::startsWith($path, ['http://', 'https://', 'data:', '/'])) {
            return $path;
        }

        if (Str::startsWith($path, 'images/')) {
            return asset($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . ltrim($path, '/'));
        }

        return asset(Product::DEFAULT_PLACEHOLDER);
    }

    public function getImageThumbUrlAttribute(): string
    {
        $path = $this->image_thumb_path ?: $this->image_medium_path ?: $this->image_path;
        if (! $path) {
            return asset(Product::DEFAULT_PLACEHOLDER);
        }
        if (Str::startsWith($path, ['http://', 'https://', 'data:', '/'])) {
            return $path;
        }
        if (Str::startsWith($path, 'images/')) {
            return asset($path);
        }
        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . ltrim($path, '/'));
        }
        return asset(Product::DEFAULT_PLACEHOLDER);
    }

    public function getImageMediumUrlAttribute(): string
    {
        return $this->image_url;
    }

    public function getImageOriginalUrlAttribute(): string
    {
        $path = $this->image_original_path ?: $this->image_path;
        if (! $path) {
            return asset(Product::DEFAULT_PLACEHOLDER);
        }
        if (Str::startsWith($path, ['http://', 'https://', 'data:', '/'])) {
            return $path;
        }
        if (Str::startsWith($path, 'images/')) {
            return asset($path);
        }
        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . ltrim($path, '/'));
        }
        return asset(Product::DEFAULT_PLACEHOLDER);
    }
}
