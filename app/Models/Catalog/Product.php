<?php

namespace App\Models\Catalog;

use App\Enums\ProductGender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const DEFAULT_PLACEHOLDER = 'images/placeholders/product-default.svg';

    protected $fillable = [
        'category_id',
        'product_type_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'gender_target',
        'base_price',
        'sale_price',
        'is_active',
        'is_featured',
        'is_new_arrival',
        'main_image_path',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'gender_target' => ProductGender::class,
        'base_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_new_arrival' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function discounts(): MorphToMany
    {
        return $this->morphToMany(Discount::class, 'discountable');
    }

    public function getMainImageUrlAttribute(): string
    {
        if ($this->main_image_path) {
            return $this->resolveImageUrl($this->main_image_path);
        }

        $galleryFirstPath = $this->relationLoaded('images')
            ? $this->images->sortBy('sort_order')->first()?->image_path
            : $this->images()->orderBy('sort_order')->value('image_path');

        return $this->resolveImageUrl($galleryFirstPath);
    }

    public function resolveImageUrl(?string $path): string
    {
        if (! $path) {
            return asset(self::DEFAULT_PLACEHOLDER);
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

        return asset(self::DEFAULT_PLACEHOLDER);
    }

    public function getTotalStockAttribute(): int
    {
        if ($this->relationLoaded('variants')) {
            return (int) $this->variants->sum('stock_qty');
        }

        return (int) $this->variants()->sum('stock_qty');
    }

    public function getHasLowStockAttribute(): bool
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->contains(
                fn ($variant) => (int) $variant->stock_qty <= (int) $variant->low_stock_threshold
            );
        }

        return $this->variants()
            ->whereColumn('stock_qty', '<=', 'low_stock_threshold')
            ->exists();
    }
}
