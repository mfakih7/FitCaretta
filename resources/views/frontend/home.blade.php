@extends('layouts.frontend')

@section('title', config('store.name') . ' - Home')

@section('content')
    @if(($slides ?? collect())->isNotEmpty())
        <section class="fc-hero mb-4 mb-lg-5 p-0">
            <div id="fcHomeHeroCarousel" class="carousel slide fc-hero-carousel" data-bs-ride="carousel" data-bs-interval="5000" data-bs-pause="hover">
                <div class="carousel-indicators">
                    @foreach($slides as $i => $slide)
                        <button type="button"
                                data-bs-target="#fcHomeHeroCarousel"
                                data-bs-slide-to="{{ $i }}"
                                class="{{ $i === 0 ? 'active' : '' }}"
                                aria-current="{{ $i === 0 ? 'true' : 'false' }}"
                                aria-label="Slide {{ $i + 1 }}"></button>
                    @endforeach
                </div>

                <div class="carousel-inner">
                    @foreach($slides as $i => $slide)
                        @php
                            $bg = $slide->image_url ? ("background-image:url('" . e($slide->image_url) . "');") : '';
                            $hasBtn1 = filled($slide->button_one_text) && filled($slide->button_one_link);
                            $hasBtn2 = filled($slide->button_two_text) && filled($slide->button_two_link);
                        @endphp
                        <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                            <div class="fc-hero-slide p-4 p-md-5" style="{{ $bg }}">
                                <div class="container fc-hero-slide-inner">
                                    <div class="row align-items-center gy-4">
                                        <div class="col-lg-7">
                                            @if(filled($slide->badge))
                                                <span class="fc-pill mb-3">{{ $slide->badge }}</span>
                                            @endif
                                            <h1 class="fc-home-hero-title fw-semibold mb-3">{{ $slide->title ?: config('store.hero_title') }}</h1>
                                            @if(filled($slide->subtitle))
                                                <p class="fc-home-hero-sub mb-4">{{ $slide->subtitle }}</p>
                                            @endif

                                            @if($hasBtn1 || $hasBtn2)
                                                <div class="d-flex gap-2 flex-wrap">
                                                    @if($hasBtn1)
                                                        <a href="{{ $slide->button_one_link }}" class="btn btn-light px-4">{{ $slide->button_one_text }}</a>
                                                    @endif
                                                    @if($hasBtn2)
                                                        <a href="{{ $slide->button_two_link }}" class="btn btn-outline-light px-4">{{ $slide->button_two_text }}</a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-5 d-none d-lg-block">
                                            <div class="fc-kaira-banner fc-kaira-banner-dark p-4 p-lg-5 h-100">
                                                <h5 class="mb-2 text-uppercase small" style="letter-spacing:.8px;">{{ config('store.tagline') }}</h5>
                                                <p class="mb-3 text-white-50">
                                                    {{ config('store.short_description') }}
                                                </p>
                                                <a href="{{ route('shop') }}" class="btn btn-sm btn-light">Shop</a>
                                                <a href="{{ route('offers') }}" class="btn btn-sm btn-outline-light ms-1">Offers</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#fcHomeHeroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#fcHomeHeroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </section>
    @else
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
    @endif

    <section class="mb-4 mb-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-3 fc-home-heading-row">
            <h2 class="fc-section-title h4 mb-0">Featured Categories</h2>
            <a href="{{ route('shop') }}">View All Products</a>
        </div>
        <div class="row g-3">
            @forelse($featuredCategories as $category)
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('shop.category', $category->slug) }}" class="fc-category-tile fc-hover-zoom h-100">
                        <div class="fc-media">
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}">
                            <div class="fc-category-tile-label">Category</div>
                        </div>
                        <div class="fc-category-tile-body">
                            <h3 class="fc-category-tile-title">{{ $category->name }}</h3>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <div class="fc-category-tile-meta">{{ $category->products_count }} products</div>
                                <span class="fc-link-underline small">Shop</span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-muted">No categories available.</div>
            @endforelse
        </div>
    </section>

    <section class="mb-4 mb-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-3 fc-home-heading-row">
            <h2 class="fc-section-title h4 mb-0">Featured Products</h2>
            <a href="{{ route('shop') }}">View All Products</a>
        </div>
        <div class="row g-3">
            @forelse($featuredProducts as $product)
                <div class="col-12 col-md-6 col-lg-3">
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
                <div class="col-12 col-md-6 col-lg-3">
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
