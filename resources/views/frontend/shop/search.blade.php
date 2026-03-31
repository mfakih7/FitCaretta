@extends('layouts.frontend')

@section('title', 'Search - ' . config('store.name'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Search Results</h1>
        <span class="text-muted">{{ $products->total() }} results for "{{ $term }}"</span>
    </div>

    <div class="row g-3 g-lg-4">
        <div class="col-lg-3">
            @include('frontend.partials.filters')
        </div>
        <div class="col-lg-9">
            <div class="row g-3">
                @forelse($products as $product)
                    <div class="col-12 col-md-6 col-xl-4">
                        @include('frontend.partials.product-card', ['product' => $product])
                    </div>
                @empty
                    <div class="col-12"><div class="alert alert-light border">No matching products found.</div></div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $products->links() }}</div>
@endsection
