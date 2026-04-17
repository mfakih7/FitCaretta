@extends('layouts.frontend')

@section('title', 'Search - ' . config('store.name'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Search Results</h1>
        <span class="text-muted">{{ $products->total() }} results for "{{ $term }}"</span>
    </div>

    <div class="row g-3 g-lg-4">
        <div class="col-lg-3 d-none d-lg-block">
            @include('frontend.partials.filters')
        </div>
        <div class="col-lg-9">
            <div class="d-lg-none mb-3">
                <div class="fc-mobile-controls">
                    <div class="fc-mobile-controls-left">
                        <div class="small text-muted">{{ $products->total() }} results</div>
                    </div>
                    <div class="fc-mobile-controls-right">
                        @php
                            $sortVal = (string) request('sort', 'latest');
                            $qs = request()->except(['sort', 'page']);
                        @endphp
                        <form method="GET" action="{{ url()->current() }}" class="fc-mobile-sort">
                            @foreach($qs as $k => $v)
                                @if(is_array($v))
                                    @foreach($v as $vv)
                                        <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                @endif
                            @endforeach
                            <select name="sort" class="form-select form-select-sm fc-mobile-sort-select" onchange="this.form.submit()">
                                <option value="latest" @selected($sortVal==='latest')>Latest</option>
                                <option value="price_asc" @selected($sortVal==='price_asc')>Price ↑</option>
                                <option value="price_desc" @selected($sortVal==='price_desc')>Price ↓</option>
                            </select>
                        </form>
                        <button class="btn btn-outline-dark btn-sm fc-mobile-filter-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#fcFiltersCanvas" aria-controls="fcFiltersCanvas">
                            Filters
                        </button>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                @forelse($products as $product)
                    <div class="col-6 col-md-4 col-xl-4">
                        @include('frontend.partials.product-card', ['product' => $product])
                    </div>
                @empty
                    <div class="col-12"><div class="alert alert-light border">No matching products found.</div></div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end fc-filters-canvas d-lg-none" tabindex="-1" id="fcFiltersCanvas" aria-labelledby="fcFiltersCanvasLabel">
        <div class="offcanvas-header border-bottom">
            <div>
                <div class="small text-muted">Filters</div>
                <h5 class="offcanvas-title mb-0" id="fcFiltersCanvasLabel">Search</h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body pt-3">
            @include('frontend.partials.filters')
        </div>
    </div>

    <div class="mt-3">{{ $products->links() }}</div>
@endsection
