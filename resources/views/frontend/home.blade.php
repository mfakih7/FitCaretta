@extends('layouts.frontend')

@section('title', config('store.name') . ' - Home')

@section('content')
    <section class="fc-hero p-4 p-md-5 mb-4 mb-lg-5">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7">
                <span class="fc-pill mb-3">New Collections</span>
                <h1 class="fc-home-hero-title fw-semibold mb-3">{{ config('store.hero_title') }}</h1>
                <p class="fc-home-hero-sub mb-4">
                    Premium performance essentials inspired by modern fashion. Shop refined men and women looks with clean cuts and confident comfort.
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('shop') }}" class="btn btn-light px-4">Shop Collection</a>
                    <a href="{{ route('offers') }}" class="btn btn-outline-light px-4">View Offers</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="fc-kaira-banner fc-kaira-banner-dark p-4 p-lg-5 h-100">
                    <h5 class="mb-2 text-uppercase small" style="letter-spacing:.8px;">Classic winter collection</h5>
                    <p class="mb-3 text-white-50">
                        Curated activewear pieces built for training, movement, and everyday style.
                    </p>
                    <a href="{{ route('shop.men') }}" class="btn btn-sm btn-light">Shop Men</a>
                    <a href="{{ route('shop.women') }}" class="btn btn-sm btn-outline-light ms-1">Shop Women</a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-4 mb-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-3 fc-home-heading-row">
            <h2 class="fc-section-title h4 mb-0">Featured Categories</h2>
            <a href="{{ route('shop') }}">View All Products</a>
        </div>
        <div class="row g-3">
            @forelse($featuredCategories as $category)
                <div class="col-6 col-md-3">
                    <a href="{{ route('shop.category', $category->slug) }}" class="text-decoration-none">
                        <div class="fc-category-card h-100 p-3">
                            <div class="small text-uppercase text-muted mb-2" style="letter-spacing:.6px;">Collection</div>
                            <h6 class="mb-1 text-dark">{{ $category->name }}</h6>
                            <small class="text-muted">{{ $category->products_count }} products</small>
                            <div class="mt-2 small text-dark">Discover Now</div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-muted">No categories available.</div>
            @endforelse
        </div>
    </section>

    <section class="mb-4 mb-lg-5">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="fc-kaira-banner p-3 h-100">
                    <h6 class="text-uppercase small mb-1" style="letter-spacing:.7px;">Book an appointment</h6>
                    <small class="text-muted">Need sizing help? Our team can assist with the right fit.</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="fc-kaira-banner p-3 h-100">
                    <h6 class="text-uppercase small mb-1" style="letter-spacing:.7px;">Pickup in store</h6>
                    <small class="text-muted">Fast handover options for local orders in {{ config('store.country') }}.</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="fc-kaira-banner p-3 h-100">
                    <h6 class="text-uppercase small mb-1" style="letter-spacing:.7px;">Special packaging</h6>
                    <small class="text-muted">Careful premium packaging for every order request.</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="fc-kaira-banner p-3 h-100">
                    <h6 class="text-uppercase small mb-1" style="letter-spacing:.7px;">Easy returns</h6>
                    <small class="text-muted">Simple support flow via WhatsApp for quick assistance.</small>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-4 mb-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-3 fc-home-heading-row">
            <h2 class="fc-section-title h4 mb-0">Featured Products</h2>
            <a href="{{ route('shop') }}">View All Products</a>
        </div>
        <div class="row g-3">
            @forelse($featuredProducts as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    @include('frontend.partials.product-card', ['product' => $product])
                </div>
            @empty
                <div class="col-12 text-muted">No featured products yet.</div>
            @endforelse
        </div>
    </section>

    <section class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 fc-home-heading-row">
            <h2 class="fc-section-title h4 mb-0">New Arrivals</h2>
            <a href="{{ route('shop') }}">View All Products</a>
        </div>
        <div class="row g-3">
            @forelse($newArrivals as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    @include('frontend.partials.product-card', ['product' => $product])
                </div>
            @empty
                <div class="col-12 text-muted">No new arrivals yet.</div>
            @endforelse
        </div>
    </section>

    <section class="fc-kaira-banner p-4 p-lg-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="small text-uppercase text-muted mb-2" style="letter-spacing:.7px;">Collection</div>
                <h3 class="mb-2">Premium activewear designed for movement and confidence.</h3>
                <p class="text-muted mb-0">Discover lightweight essentials, fitted silhouettes, and seasonal pieces crafted to perform from gym to street.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('shop') }}" class="btn btn-dark px-4">Shop Collection</a>
                            </div>
        </div>
    </section>
@endsection
