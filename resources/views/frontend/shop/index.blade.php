@extends('layouts.frontend')

@section('title', $title . ' - ' . config('store.name'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $title }}</h1>
        <span class="text-muted">{{ $products->total() }} products</span>
    </div>

    @include('frontend.partials.filters')

    <div class="row g-3">
        @forelse($products as $product)
            <div class="col-12 col-md-6 col-lg-3">
                @include('frontend.partials.product-card', ['product' => $product])
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border">No products found for these filters.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $products->links() }}
    </div>
@endsection
