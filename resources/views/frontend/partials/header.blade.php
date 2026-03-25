@php($cartCount = app(\App\Services\Cart\CartService::class)->count())
<div class="fc-topbar">
    <div class="container d-flex justify-content-between align-items-center py-2">
        <div class="fc-topbar-text">{{ config('store.topbar_shipping_text') }}</div>
        <div class="fc-topbar-text d-none d-md-block">{{ config('store.topbar_promo_text') }}</div>
    </div>
</div>

<nav class="navbar navbar-expand-lg fc-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset(config('store.logo_primary_path')) }}" alt="{{ config('store.logo_alt') }}" class="fc-brand-logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('shop')) active @endif" href="{{ route('shop') }}" @if(request()->routeIs('shop')) aria-current="page" @endif>Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('shop.men')) active @endif" href="{{ route('shop.men') }}" @if(request()->routeIs('shop.men')) aria-current="page" @endif>Men</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('shop.women')) active @endif" href="{{ route('shop.women') }}" @if(request()->routeIs('shop.women')) aria-current="page" @endif>Women</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('shop.new')) active @endif" href="{{ route('shop.new') }}" @if(request()->routeIs('shop.new')) aria-current="page" @endif>New Arrivals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('offers')) active @endif" href="{{ route('offers') }}" @if(request()->routeIs('offers')) aria-current="page" @endif>Offers</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2 fc-nav-actions">
                <form class="d-flex align-items-center gap-2" role="search" method="GET" action="{{ route('search') }}">
                    <input class="form-control form-control-sm fc-search-input" type="search" name="q" value="{{ request('q') }}" placeholder="Search...">
                    <button class="btn btn-sm btn-dark fc-search-btn" type="submit">Go</button>
                </form>
                <a class="btn btn-sm btn-outline-dark position-relative fc-cart-btn" href="{{ route('cart.index') }}">
                    Cart
                    @if($cartCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</nav>
