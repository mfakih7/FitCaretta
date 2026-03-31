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

<div class="fc-product-card fc-hover-zoom h-100">
    <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none d-block">
        <div class="fc-media">
            <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" class="fc-card-img" data-fc-default="{{ $product->main_image_url }}">
            @if($isNew || $discount)
                <div class="fc-badges">
                    @if($isNew)
                        <span class="fc-badge fc-badge-dark">New</span>
                    @endif
                    @if($discount)
                        <span class="fc-badge fc-badge-dark">
                            @if($discount->type === 'percentage')
                                {{ (float) $discount->value }}% Off
                            @else
                                Save {{ config('store.currency_symbol') }}{{ number_format((float) $discount->value, 2) }}
                            @endif
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </a>

    <div class="p-3 d-flex flex-column gap-2">
        <div class="d-flex flex-column">
            <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
                <h3 class="fc-product-title">{{ $product->name }}</h3>
            </a>
            <div class="fc-product-sub text-capitalize">
                {{ $product->gender_target->value }}@if($product->category?->name) / {{ $product->category?->name }}@endif
            </div>
        </div>

        <div class="d-flex align-items-baseline gap-2">
            @if($discount)
                <div class="fc-price-old">{{ config('store.currency_symbol') }}{{ number_format($product->pricing['base_price'], 2) }}</div>
                <div class="fc-price-new">{{ config('store.currency_symbol') }}{{ number_format($product->pricing['effective_price'], 2) }}</div>
            @else
                <div class="fc-price-new">{{ config('store.currency_symbol') }}{{ number_format($product->pricing['base_price'], 2) }}</div>
            @endif
        </div>

        <div class="fc-card-actions pt-1">
            <div class="fc-card-swatches" aria-label="Available colors">
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

            <button
                type="button"
                class="fc-card-quickadd"
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
    </div>
</div>

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
