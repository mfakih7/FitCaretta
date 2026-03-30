@php($cartCount = app(\App\Services\Cart\CartService::class)->count())
<div class="fc-topbar">
    <div class="container d-flex justify-content-between align-items-center py-2">
        <div class="fc-topbar-text">{{ config('store.topbar_shipping_text') }}</div>
        <div class="fc-topbar-text d-none d-md-block">{{ config('store.topbar_promo_text') }}</div>
    </div>
</div>

<nav class="navbar navbar-expand-lg fc-navbar sticky-top">
    <div class="container">
        <div class="fc-header-grid">
            <div class="fc-header-left">
                <button class="navbar-toggler fc-header-burger" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse fc-header-collapse" id="mainNav">
                    <ul class="navbar-nav fc-header-menu">
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
                        @if(!empty($showAboutNav))
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('about.show')) active @endif" href="{{ route('about.show') }}" @if(request()->routeIs('about.show')) aria-current="page" @endif>About</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="fc-header-center">
                <a class="navbar-brand fc-header-logo" href="{{ route('home') }}" aria-label="{{ config('store.name') }}">
                    <img src="{{ asset(config('store.logo_primary_path')) }}" alt="{{ config('store.logo_alt') }}" class="fc-brand-logo">
                </a>
            </div>

            <div class="fc-header-right">
                <div class="fc-header-search" data-open="0">
                    <button type="button" class="fc-icon-btn" id="fcSearchToggle" aria-label="Search" aria-expanded="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="M21 21l-4.3-4.3"></path>
                        </svg>
                    </button>
                    <form class="fc-header-search-form" role="search" method="GET" action="{{ route('search') }}">
                        <input class="form-control form-control-sm fc-search-input" type="search" name="q" value="{{ request('q') }}" placeholder="Search...">
                    </form>
                </div>

                <a class="fc-icon-btn position-relative" href="{{ route('cart.index') }}" aria-label="Cart">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 6h15l-1.5 9h-13z"></path>
                        <path d="M6 6l-2-2H2"></path>
                        <circle cx="9" cy="20" r="1.25"></circle>
                        <circle cx="18" cy="20" r="1.25"></circle>
                    </svg>
                    @if($cartCount > 0)
                        <span class="fc-cart-badge">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    (() => {
        const wrap = document.querySelector('.fc-header-search');
        const toggle = document.getElementById('fcSearchToggle');
        const input = wrap?.querySelector('input[name="q"]');
        if (!wrap || !toggle || !input) return;

        const close = () => {
            wrap.dataset.open = '0';
            toggle.setAttribute('aria-expanded', 'false');
        };
        const open = () => {
            wrap.dataset.open = '1';
            toggle.setAttribute('aria-expanded', 'true');
            setTimeout(() => input.focus(), 0);
        };

        toggle.addEventListener('click', () => {
            wrap.dataset.open === '1' ? close() : open();
        });

        document.addEventListener('click', (e) => {
            if (!wrap.contains(e.target)) close();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') close();
        });
    })();
</script>
