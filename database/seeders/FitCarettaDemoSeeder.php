<?php

namespace Database\Seeders;

use App\Enums\ProductGender;
use App\Models\Catalog\Category;
use App\Models\Catalog\Color;
use App\Models\Catalog\Coupon;
use App\Models\Catalog\Discount;
use App\Models\Catalog\Product;
use App\Models\Catalog\ProductImage;
use App\Models\Catalog\ProductType;
use App\Models\Catalog\ProductVariant;
use App\Models\Catalog\Size;
use App\Models\Setting;
use App\Models\Sales\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FitCarettaDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedTaxonomies();
            $products = $this->seedProducts();
            $this->seedDiscountsAndCoupons($products);
            $this->seedDemoOrders($products);
            $this->seedSettingsIfTableExists();
        });
    }

    private function seedTaxonomies(): void
    {
        $categories = [
            ['name' => 'Men Performance', 'slug' => 'men-performance'],
            ['name' => 'Women Performance', 'slug' => 'women-performance'],
            ['name' => 'Hoodies & Jackets', 'slug' => 'hoodies-jackets'],
            ['name' => 'Shorts & Bottoms', 'slug' => 'shorts-bottoms'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
        ];

        foreach ($categories as $index => $category) {
            Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => $category['name'] . ' for active lifestyle in ' . config('store.country', 'Lebanon') . '.',
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        $types = [
            ['name' => 'Performance T-Shirt', 'slug' => 'performance-tshirt'],
            ['name' => 'Leggings', 'slug' => 'leggings'],
            ['name' => 'Hoodie', 'slug' => 'hoodie'],
            ['name' => 'Joggers', 'slug' => 'joggers'],
            ['name' => 'Shorts', 'slug' => 'shorts'],
            ['name' => 'Jacket', 'slug' => 'jacket'],
            ['name' => 'Sports Bra', 'slug' => 'sports-bra'],
            ['name' => 'Crop Top', 'slug' => 'crop-top'],
            ['name' => 'Accessory', 'slug' => 'accessory'],
        ];
        foreach ($types as $type) {
            ProductType::query()->updateOrCreate(
                ['slug' => $type['slug']],
                ['name' => $type['name'], 'is_active' => true]
            );
        }

        $sizes = ['XS', 'S', 'M', 'L', 'XL'];
        foreach ($sizes as $idx => $size) {
            Size::query()->updateOrCreate(
                ['code' => $size],
                ['name' => $size, 'sort_order' => $idx + 1, 'is_active' => true]
            );
        }

        $colors = [
            ['name' => 'Black', 'code' => 'BLACK', 'hex_code' => '#111111'],
            ['name' => 'White', 'code' => 'WHITE', 'hex_code' => '#F8F8F8'],
            ['name' => 'Navy', 'code' => 'NAVY', 'hex_code' => '#1C2E4A'],
            ['name' => 'Olive', 'code' => 'OLIVE', 'hex_code' => '#596241'],
            ['name' => 'Burgundy', 'code' => 'BURGUNDY', 'hex_code' => '#7A1F2A'],
            ['name' => 'Sand', 'code' => 'SAND', 'hex_code' => '#C8B79B'],
        ];
        foreach ($colors as $color) {
            Color::query()->updateOrCreate(
                ['code' => $color['code']],
                ['name' => $color['name'], 'hex_code' => $color['hex_code'], 'is_active' => true]
            );
        }
    }

    private function seedProducts(): \Illuminate\Support\Collection
    {
        $catalog = [
            // Men
            ['name' => 'AeroFlex Performance Tee', 'gender' => ProductGender::MEN, 'type' => 'performance-tshirt', 'category' => 'men-performance', 'price' => 28.00],
            ['name' => 'Core Motion Hoodie', 'gender' => ProductGender::MEN, 'type' => 'hoodie', 'category' => 'hoodies-jackets', 'price' => 58.00],
            ['name' => 'Velocity Mesh Shorts', 'gender' => ProductGender::MEN, 'type' => 'shorts', 'category' => 'shorts-bottoms', 'price' => 34.00],
            ['name' => 'DriveFit Joggers', 'gender' => ProductGender::MEN, 'type' => 'joggers', 'category' => 'shorts-bottoms', 'price' => 46.00],
            ['name' => 'StormGuard Training Jacket', 'gender' => ProductGender::MEN, 'type' => 'jacket', 'category' => 'hoodies-jackets', 'price' => 72.00],
            ['name' => 'Stride Seamless Tee', 'gender' => ProductGender::MEN, 'type' => 'performance-tshirt', 'category' => 'men-performance', 'price' => 30.00],
            ['name' => 'Urban Lift Hoodie', 'gender' => ProductGender::MEN, 'type' => 'hoodie', 'category' => 'hoodies-jackets', 'price' => 62.00],
            ['name' => 'SprintLite Shorts', 'gender' => ProductGender::MEN, 'type' => 'shorts', 'category' => 'shorts-bottoms', 'price' => 32.00],
            ['name' => 'PeakForm Jacket', 'gender' => ProductGender::MEN, 'type' => 'jacket', 'category' => 'hoodies-jackets', 'price' => 74.00],
            ['name' => 'RunClub Joggers', 'gender' => ProductGender::MEN, 'type' => 'joggers', 'category' => 'shorts-bottoms', 'price' => 48.00],

            // Women
            ['name' => 'SculptFlow Leggings', 'gender' => ProductGender::WOMEN, 'type' => 'leggings', 'category' => 'women-performance', 'price' => 44.00],
            ['name' => 'Lift Support Sports Bra', 'gender' => ProductGender::WOMEN, 'type' => 'sports-bra', 'category' => 'women-performance', 'price' => 29.00],
            ['name' => 'Pulse Crop Top', 'gender' => ProductGender::WOMEN, 'type' => 'crop-top', 'category' => 'women-performance', 'price' => 27.00],
            ['name' => 'AirMove Training Shorts', 'gender' => ProductGender::WOMEN, 'type' => 'shorts', 'category' => 'shorts-bottoms', 'price' => 31.00],
            ['name' => 'BalanceFit Hoodie', 'gender' => ProductGender::WOMEN, 'type' => 'hoodie', 'category' => 'hoodies-jackets', 'price' => 57.00],
            ['name' => 'VibeFlex Leggings', 'gender' => ProductGender::WOMEN, 'type' => 'leggings', 'category' => 'women-performance', 'price' => 46.00],
            ['name' => 'Motion Core Sports Bra', 'gender' => ProductGender::WOMEN, 'type' => 'sports-bra', 'category' => 'women-performance', 'price' => 30.00],
            ['name' => 'Focus Crop Top', 'gender' => ProductGender::WOMEN, 'type' => 'crop-top', 'category' => 'women-performance', 'price' => 26.00],
            ['name' => 'Flowline Jacket', 'gender' => ProductGender::WOMEN, 'type' => 'jacket', 'category' => 'hoodies-jackets', 'price' => 69.00],
            ['name' => 'EdgeRunner Shorts', 'gender' => ProductGender::WOMEN, 'type' => 'shorts', 'category' => 'shorts-bottoms', 'price' => 33.00],

            // Unisex / Accessories
            ['name' => 'FitCaretta Training Cap', 'gender' => ProductGender::UNISEX, 'type' => 'accessory', 'category' => 'accessories', 'price' => 18.00],
            ['name' => 'Pro Grip Socks (2-Pack)', 'gender' => ProductGender::UNISEX, 'type' => 'accessory', 'category' => 'accessories', 'price' => 14.00],
            ['name' => 'Urban Gym Bag', 'gender' => ProductGender::UNISEX, 'type' => 'accessory', 'category' => 'accessories', 'price' => 39.00],
            ['name' => 'Hydro Sport Bottle', 'gender' => ProductGender::UNISEX, 'type' => 'accessory', 'category' => 'accessories', 'price' => 16.00],
            ['name' => 'MoveLight Wind Jacket', 'gender' => ProductGender::UNISEX, 'type' => 'jacket', 'category' => 'hoodies-jackets', 'price' => 64.00],
            ['name' => 'Core Stretch Joggers', 'gender' => ProductGender::UNISEX, 'type' => 'joggers', 'category' => 'shorts-bottoms', 'price' => 49.00],
        ];

        $sizePool = Size::query()->where('is_active', true)->orderBy('sort_order')->get();
        $colorPool = Color::query()->where('is_active', true)->get();
        $products = collect();

        foreach ($catalog as $index => $item) {
            $category = Category::query()->where('slug', $item['category'])->firstOrFail();
            $type = ProductType::query()->where('slug', $item['type'])->firstOrFail();
            $slug = Str::slug($item['name']);
            $sku = 'FC-' . strtoupper(Str::substr(Str::slug($item['name'], ''), 0, 4)) . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);
            $mainPath = $this->ensureDemoImage($slug, $item['name'], false);

            $product = Product::query()->withTrashed()->updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'product_type_id' => $type->id,
                    'name' => $item['name'],
                    'sku' => $sku,
                    'short_description' => 'Premium sportswear designed for daily training and movement.',
                    'description' => $item['name'] . ' combines comfort, breathability, and modern fit for ' . config('store.country', 'Lebanon') . ' active lifestyle.',
                    'gender_target' => $item['gender']->value,
                    'base_price' => $item['price'],
                    'sale_price' => null,
                    'is_active' => true,
                    'is_featured' => $index < 10,
                    'is_new_arrival' => $index % 3 === 0,
                    'main_image_path' => $mainPath,
                    'meta_title' => $item['name'] . ' | ' . config('store.name', 'FitCaretta'),
                    'meta_description' => 'Shop ' . $item['name'] . ' at ' . config('store.name', 'FitCaretta') . ' ' . config('store.country', 'Lebanon') . '.',
                ]
            );

            if (method_exists($product, 'trashed') && $product->trashed()) {
                $product->restore();
            }

            $product->images()->delete();
            ProductImage::query()->create([
                'product_id' => $product->id,
                'image_path' => $mainPath,
                'alt_text' => $item['name'],
                'sort_order' => 1,
            ]);
            ProductImage::query()->create([
                'product_id' => $product->id,
                'image_path' => $this->ensureDemoImage($slug . '-alt', $item['name'] . ' Alt', true),
                'alt_text' => $item['name'] . ' alternate',
                'sort_order' => 2,
            ]);

            $product->variants()->delete();
            $chosenSizes = $sizePool->take($item['type'] === 'accessory' ? 1 : 4);
            $chosenColors = $colorPool->shuffle()->take(3);
            foreach ($chosenSizes as $size) {
                foreach ($chosenColors as $color) {
                    ProductVariant::query()->create([
                        'product_id' => $product->id,
                        'size_id' => $item['type'] === 'accessory' ? null : $size->id,
                        'color_id' => $color->id,
                        'variant_sku' => $sku . '-' . ($size->code ?? 'OS') . '-' . $color->code,
                        'price_override' => null,
                        'stock_qty' => random_int(5, 40),
                        'low_stock_threshold' => 5,
                        'is_active' => true,
                    ]);
                }
            }

            $products->push($product);
        }

        return $products;
    }

    private function seedDiscountsAndCoupons(\Illuminate\Support\Collection $products): void
    {
        $global = Discount::query()->updateOrCreate(
            ['name' => 'Weekend Global Offer'],
            [
                'code' => null,
                'type' => Discount::TYPE_PERCENTAGE,
                'value' => 10,
                'scope' => Discount::SCOPE_GLOBAL,
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(20),
                'is_active' => true,
                'priority' => 1,
            ]
        );
        $global->products()->sync([]);
        $global->categories()->sync([]);

        $menCategory = Category::query()->where('slug', 'men-performance')->first();
        if ($menCategory) {
            $catDiscount = Discount::query()->updateOrCreate(
                ['name' => 'Men Performance Boost'],
                [
                    'code' => 'MENBOOST',
                    'type' => Discount::TYPE_PERCENTAGE,
                    'value' => 15,
                    'scope' => Discount::SCOPE_CATEGORY,
                    'start_date' => now()->subDays(2),
                    'end_date' => now()->addDays(25),
                    'is_active' => true,
                    'priority' => 2,
                ]
            );
            $catDiscount->categories()->sync([$menCategory->id]);
            $catDiscount->products()->sync([]);
        }

        $productDiscount = Discount::query()->updateOrCreate(
            ['name' => 'Selected Product Flash Deal'],
            [
                'code' => 'FLASHFIT',
                'type' => Discount::TYPE_FIXED,
                'value' => 8,
                'scope' => Discount::SCOPE_PRODUCT,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(10),
                'is_active' => true,
                'priority' => 3,
            ]
        );
        $productDiscount->products()->sync($products->take(6)->pluck('id')->all());
        $productDiscount->categories()->sync([]);

        Discount::query()->updateOrCreate(
            ['name' => 'Old Campaign (Inactive)'],
            [
                'code' => 'OLD2024',
                'type' => Discount::TYPE_PERCENTAGE,
                'value' => 20,
                'scope' => Discount::SCOPE_GLOBAL,
                'start_date' => now()->subMonths(4),
                'end_date' => now()->subMonths(3),
                'is_active' => false,
                'priority' => 0,
            ]
        );

        $coupons = [
            ['code' => 'WELCOME10', 'type' => Coupon::TYPE_PERCENTAGE, 'value' => 10, 'min' => 50, 'active' => true],
            ['code' => 'FIT5', 'type' => Coupon::TYPE_FIXED, 'value' => 5, 'min' => 30, 'active' => true],
            ['code' => 'SPRING15', 'type' => Coupon::TYPE_PERCENTAGE, 'value' => 15, 'min' => 70, 'active' => false],
        ];
        foreach ($coupons as $coupon) {
            Coupon::query()->updateOrCreate(
                ['code' => $coupon['code']],
                [
                    'type' => $coupon['type'],
                    'value' => $coupon['value'],
                    'minimum_order_amount' => $coupon['min'],
                    'usage_limit' => 200,
                    'usage_per_customer' => 2,
                    'used_count' => 0,
                    'start_date' => now()->subDays(2),
                    'end_date' => now()->addDays(60),
                    'is_active' => $coupon['active'],
                ]
            );
        }
    }

    private function seedDemoOrders(\Illuminate\Support\Collection $products): void
    {
        $demoOrders = [
            ['number' => 'FC-000901', 'name' => 'Maya Khoury', 'email' => 'maya.demo@example.com', 'status' => Order::STATUS_CONFIRMED],
            ['number' => 'FC-000902', 'name' => 'Rami Haddad', 'email' => 'rami.demo@example.com', 'status' => Order::STATUS_PENDING],
        ];

        foreach ($demoOrders as $idx => $demo) {
            $order = Order::query()->updateOrCreate(
                ['order_number' => $demo['number']],
                [
                    'public_token' => hash('sha256', $demo['number'] . '-demo-token'),
                    'full_name' => $demo['name'],
                    'email' => $demo['email'],
                    'phone' => '+96170000' . ($idx + 10),
                    'city' => 'Beirut',
                    'address' => 'Demo street, Beirut',
                    'notes' => 'Seeded demo order',
                    'subtotal' => 0,
                    'discount_total' => 0,
                    'total' => 0,
                    'currency' => (string) config('store.currency_code', 'USD'),
                    'order_status' => $demo['status'],
                    'placed_at' => now()->subDays($idx + 1),
                ]
            );

            $order->items()->delete();
            $chosen = $products->slice($idx * 2, 2)->values();
            $subtotal = 0.0;
            $total = 0.0;

            foreach ($chosen as $product) {
                $variant = $product->variants()->first();
                if (! $variant) {
                    continue;
                }
                $qty = random_int(1, 2);
                $base = (float) $product->base_price;
                $discounted = max(0, $base - 4);
                $lineSubtotal = $base * $qty;
                $lineTotal = $discounted * $qty;
                $subtotal += $lineSubtotal;
                $total += $lineTotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'product_name' => $product->name,
                    'product_slug' => $product->slug,
                    'variant_sku' => $variant->variant_sku,
                    'size_name' => $variant->size?->name,
                    'color_name' => $variant->color?->name,
                    'quantity' => $qty,
                    'base_price' => $base,
                    'discounted_price' => $discounted,
                    'line_subtotal' => $lineSubtotal,
                    'line_total' => $lineTotal,
                ]);
            }

            $order->update([
                'subtotal' => $subtotal,
                'discount_total' => max(0, $subtotal - $total),
                'total' => $total,
            ]);
        }
    }

    private function seedSettingsIfTableExists(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        // Seed a small set of store.* keys (idempotent) for the Admin Settings feature.
        // Values are stored as overrides; delete the row to fallback to config defaults.
        $settings = [
            'store.name' => (string) config('store.name', 'FitCaretta'),
            'store.tagline' => (string) config('store.tagline', ''),
            'store.contact_email' => (string) config('store.contact_email', ''),
            'store.support_email' => (string) config('store.support_email', ''),
            'store.phone' => (string) config('store.phone', ''),
            'store.whatsapp_number' => (string) config('store.whatsapp_number', ''),
            'store.address' => (string) config('store.address', ''),
            'store.country' => (string) config('store.country', ''),
            'store.city' => (string) config('store.city', ''),
            'store.currency_code' => (string) config('store.currency_code', 'USD'),
            'store.currency_symbol' => (string) config('store.currency_symbol', '$'),
            'store.brand.logo_primary_path' => (string) config('store.brand.logo_primary_path', ''),
            'store.brand.logo_mark_path' => (string) config('store.brand.logo_mark_path', ''),
            'store.brand.favicon_path' => (string) config('store.brand.favicon_path', ''),
        ];

        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

    private function ensureDemoImage(string $slug, string $label, bool $isAlt): string
    {
        $relativePath = 'images/demo/products/' . Str::slug($slug) . '.svg';
        $absolutePath = public_path($relativePath);
        if (! File::exists($absolutePath)) {
            File::ensureDirectoryExists(dirname($absolutePath));
            $safeLabel = htmlspecialchars(Str::limit($label, 32), ENT_QUOTES, 'UTF-8');
            $storeName = htmlspecialchars((string) config('store.name', 'FitCaretta'), ENT_QUOTES, 'UTF-8');
            $hash = crc32(Str::lower($slug));
            $palette = [
                ['#0F172A', '#1D4ED8', '#38BDF8'],
                ['#111827', '#059669', '#34D399'],
                ['#1F2937', '#7C3AED', '#A78BFA'],
                ['#312E81', '#DB2777', '#F472B6'],
                ['#0B3A53', '#F97316', '#FDBA74'],
                ['#3F3F46', '#DC2626', '#F87171'],
            ];
            $tones = $palette[$hash % count($palette)];
            $start = $tones[0];
            $mid = $tones[1];
            $end = $tones[2];
            $overlayOpacity = $isAlt ? '0.42' : '0.30';
            $badgeText = $isAlt ? 'ALT VIEW' : 'FITCARETTA';
            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="1000" viewBox="0 0 800 1000">
  <defs>
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$start}" />
      <stop offset="55%" stop-color="{$mid}" />
      <stop offset="100%" stop-color="{$end}" />
    </linearGradient>
  </defs>
  <rect width="100%" height="100%" fill="url(#bg)"/>
  <rect x="56" y="76" width="688" height="848" rx="28" fill="#ffffff" fill-opacity="{$overlayOpacity}"/>
  <circle cx="650" cy="150" r="92" fill="#ffffff" fill-opacity="0.10"/>
  <circle cx="180" cy="860" r="130" fill="#ffffff" fill-opacity="0.08"/>
  <rect x="118" y="130" width="210" height="48" rx="24" fill="#ffffff" fill-opacity="0.24"/>
  <text x="223" y="162" font-family="Arial, sans-serif" font-size="20" fill="#ffffff" text-anchor="middle">{$badgeText}</text>
  <path d="M305 635 C305 545 380 468 474 468 C568 468 643 545 643 635 L643 726 L305 726 Z" fill="#ffffff" fill-opacity="0.25"/>
  <rect x="350" y="318" width="250" height="315" rx="64" fill="#ffffff" fill-opacity="0.36"/>
  <text x="400" y="798" font-family="Arial, sans-serif" font-size="46" font-weight="700" fill="#ffffff" text-anchor="middle">{$storeName}</text>
  <text x="400" y="846" font-family="Arial, sans-serif" font-size="26" fill="#F9FAFB" text-anchor="middle">{$safeLabel}</text>
</svg>
SVG;
            File::put($absolutePath, $svg);
        }

        return $relativePath;
    }
}
