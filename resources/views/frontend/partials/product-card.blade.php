@php
    $discount = $product->pricing['discount'] ?? null;
    $isNew = (bool) ($product->is_new_arrival ?? false);
@endphp

<a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
    <div class="fc-product-card fc-hover-zoom h-100">
        <div class="fc-media">
            <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}">
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
        <div class="p-3 d-flex flex-column gap-2">
            <div class="d-flex flex-column">
                <h3 class="fc-product-title">{{ $product->name }}</h3>
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
            <div class="pt-1">
                <span class="fc-link-underline small">View</span>
            </div>
        </div>
    </div>
</a>
