@php
    $discount = $product->pricing['discount'] ?? null;
    $currencySymbol = config('store.currency_symbol', '$');
@endphp

<div class="card fc-product-card h-100 shadow-sm border-0">
    <img src="{{ $product->main_image_url }}" class="card-img-top" alt="{{ $product->name }}">
    <div class="card-body d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-1">
            <h6 class="card-title mb-0">{{ $product->name }}</h6>
            @if($discount)
                <span class="badge bg-danger">
                    {{ $discount->type === 'percentage' ? ((float)$discount->value).'%' : $currencySymbol . number_format($discount->value, 2) }} OFF
                </span>
            @endif
        </div>
        <div class="text-muted small mb-2 text-capitalize">{{ $product->gender_target->value }} / {{ $product->category?->name }}</div>
        @if($discount)
            <div class="fc-price-old">{{ $currencySymbol }}{{ number_format($product->pricing['base_price'], 2) }}</div>
            <div class="fc-price-new mb-2">{{ $currencySymbol }}{{ number_format($product->pricing['effective_price'], 2) }}</div>
        @else
            <div class="fc-price-new mb-2">{{ $currencySymbol }}{{ number_format($product->pricing['base_price'], 2) }}</div>
        @endif

        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-primary btn-sm mt-auto">View Details</a>
    </div>
</div>
