@php
    $discount = $product->pricing['discount'] ?? null;
    $isNew = (bool) ($product->is_new_arrival ?? false);
    $colors = $product->variants
        ? $product->variants->pluck('color')->filter()->unique('id')->values()
        : collect();
    $sizes = $product->variants
        ? $product->variants->pluck('size')->filter()->unique('id')->values()
        : collect();
    $imagesByColor = $product->images
        ? $product->images
            ->whereNotNull('color_id')
            ->groupBy('color_id')
            ->map(fn ($imgs) => [
                ['url' => $imgs->sortBy('sort_order')->first()?->image_url, 'alt' => $product->name],
            ])
            ->filter(fn ($arr) => !empty($arr[0]['url']))
            ->toArray()
        : [];

    $variantData = $product->variants
        ? $product->variants
            ->map(fn ($v) => [
                'size_id' => $v->size_id,
                'color_id' => $v->color_id,
                'stock_qty' => (int) $v->stock_qty,
            ])
            ->values()
            ->toArray()
        : [];

    $priceHtml = $discount
        ? '<span class="fc-price-old">' . e(config('store.currency_symbol')) . e(number_format($product->pricing['base_price'], 2)) . '</span> ' .
          '<span class="fc-price-new fw-bold">' . e(config('store.currency_symbol')) . e(number_format($product->pricing['effective_price'], 2)) . '</span>'
        : '<span class="fc-price-new fw-bold">' . e(config('store.currency_symbol')) . e(number_format($product->pricing['base_price'], 2)) . '</span>';

    $quickAddPayload = [
        'id' => $product->id,
        'name' => $product->name,
        'meta' => trim(($product->gender_target?->value ?? '') . ($product->category?->name ? ' / ' . $product->category->name : '')),
        'url' => route('products.show', $product->slug),
        'defaultImageUrl' => $product->main_image_url,
        'defaultColorId' => $colors->first()?->id,
        'priceHtml' => $priceHtml,
        'colors' => $colors->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'hex' => $c->hex_code])->values()->toArray(),
        'sizes' => $sizes->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values()->toArray(),
        'variants' => $variantData,
        'imagesByColor' => $imagesByColor,
    ];
@endphp

<article class="fc-product-card fc-pcard h-100">
    <div class="fc-pcard-media">
        <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none d-block">
            <div class="fc-media">
                <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" class="fc-card-img" data-fc-default="{{ $product->main_image_url }}">
            </div>
        </a>

        @if($isNew || $discount)
            <div class="fc-badges">
                @if($isNew)
                    <span class="fc-badge fc-badge-dark">NEW</span>
                @endif
                @if($discount)
                    <span class="fc-badge fc-badge-sale">
                        @if($discount->type === 'percentage')
                            {{ (float) $discount->value }}% OFF
                        @else
                            SAVE {{ config('store.currency_symbol') }}{{ number_format((float) $discount->value, 2) }}
                        @endif
                    </span>
                @endif
            </div>
        @endif

        <button
            type="button"
            class="fc-card-quickadd fc-pcard-quickadd"
            data-fc-quickadd
            data-fc-product='@json($quickAddPayload)'
            aria-label="Add to cart"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M6 6h15l-1.5 9h-13z"></path>
                <path d="M6 6l-2-2H2"></path>
                <circle cx="9" cy="20" r="1.25"></circle>
                <circle cx="18" cy="20" r="1.25"></circle>
            </svg>
        </button>
    </div>

    <div class="fc-pcard-body">
        <div class="fc-pcard-kicker text-capitalize">
            {{ $product->category?->name ?: $product->gender_target->value }}
        </div>

        <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
            <h3 class="fc-pcard-title">{{ $product->name }}</h3>
        </a>

        <div class="fc-pcard-price">
            @if($discount)
                <span class="fc-price-old">{{ config('store.currency_symbol') }}{{ number_format($product->pricing['base_price'], 2) }}</span>
                <span class="fc-price-new">{{ config('store.currency_symbol') }}{{ number_format($product->pricing['effective_price'], 2) }}</span>
            @else
                <span class="fc-price-new">{{ config('store.currency_symbol') }}{{ number_format($product->pricing['base_price'], 2) }}</span>
            @endif
        </div>

        @if($sizes->isNotEmpty())
            <div class="fc-pcard-sizes" aria-label="Available sizes">
                @foreach($sizes->take(6) as $size)
                    <span class="fc-pcard-size">{{ $size->name }}</span>
                @endforeach
            </div>
        @endif

        @if($colors->isNotEmpty())
            <div class="fc-card-swatches fc-pcard-swatches" aria-label="Available colors">
                @foreach($colors as $color)
                    <button
                        type="button"
                        class="fc-card-swatch-btn"
                        data-fc-swatch
                        data-color-id="{{ $color->id }}"
                        data-image="{{ data_get($imagesByColor, $color->id . '.0.url') }}"
                        title="{{ $color->name }}"
                        aria-label="{{ $color->name }}"
                    >
                        <span class="fc-card-swatch" style="background: {{ $color->hex_code ?: '#111' }}"></span>
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</article>

<script>
    (() => {
        const card = document.currentScript?.previousElementSibling;
        if (!card) return;
        const img = card.querySelector('.fc-media img');
        const swatches = card.querySelectorAll('[data-fc-swatch]');
        if (!img || swatches.length === 0) return;

        swatches.forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const next = btn.getAttribute('data-image') || '';
                if (!next) return;
                img.style.opacity = '0.2';
                img.src = next;
                setTimeout(() => (img.style.opacity = '1'), 80);
                swatches.forEach((b) => b.classList.remove('is-active'));
                btn.classList.add('is-active');
            });
        });
    })();
</script>
