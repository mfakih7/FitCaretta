@extends('layouts.frontend')

@section('title', $title . ' - ' . config('store.name'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $title }}</h1>
        <span class="text-muted">{{ $products->total() }} products</span>
    </div>

    <div class="row g-3 g-lg-4">
        <div class="col-lg-3 d-none d-lg-block">
            @include('frontend.partials.filters')
        </div>
        <div class="col-lg-9">
            <div class="d-lg-none mt-2 mb-4">
                @php
                    $sortVal = (string) request('sort', 'latest');
                    $qs = request()->except(['sort', 'page']);
                @endphp

                <div class="">
                    <div class="fc-mobile-toolbar" role="group" aria-label="Listing actions">
                        <button class="fc-mobile-action"
                                type="button"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#fcFiltersCanvas"
                                aria-controls="fcFiltersCanvas"
                                aria-label="Filters">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 5h18"></path>
                                <path d="M7 12h10"></path>
                                <path d="M10 19h4"></path>
                            </svg>
                            <span>Filter</span>
                        </button>

                        <div class="dropdown flex-grow-1">
                            <button class="fc-mobile-action w-100 @if($sortVal !== 'latest') is-active @endif"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    aria-label="Sort">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M10 6h10"></path>
                                    <path d="M10 12h7"></path>
                                    <path d="M10 18h4"></path>
                                    <path d="M4 7l2-2 2 2"></path>
                                    <path d="M6 5v14"></path>
                                </svg>
                                <span>Sort</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end fc-sort-menu fc-sort-menu-mobile">
                                <li>
                                    <a class="dropdown-item @if($sortVal==='latest') active @endif"
                                       href="{{ url()->current() . '?' . http_build_query(array_merge($qs, ['sort' => 'latest'])) }}">
                                        Latest
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item @if($sortVal==='price_asc') active @endif"
                                       href="{{ url()->current() . '?' . http_build_query(array_merge($qs, ['sort' => 'price_asc'])) }}">
                                        Price ↑
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item @if($sortVal==='price_desc') active @endif"
                                       href="{{ url()->current() . '?' . http_build_query(array_merge($qs, ['sort' => 'price_desc'])) }}">
                                        Price ↓
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                @forelse($products as $product)
                    <div class="col-6 col-md-4 col-xl-4">
                        @include('frontend.partials.product-card', ['product' => $product, 'eagerImage' => $loop->index < 4])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light border">No products found for these filters.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end fc-filters-canvas d-lg-none" tabindex="-1" id="fcFiltersCanvas" aria-labelledby="fcFiltersCanvasLabel">
        <div class="offcanvas-header border-bottom">
            <div>
                <div class="small text-muted">Filters</div>
                <h5 class="offcanvas-title mb-0" id="fcFiltersCanvasLabel">{{ $title }}</h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body pt-3">
            @include('frontend.partials.filters')
        </div>
    </div>

    <div class="mt-3">
        {{ $products->links() }}
    </div>
@endsection
