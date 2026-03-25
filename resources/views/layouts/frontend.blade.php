<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $brandName = (string) config('store.name');
        $rawTitle = trim($__env->yieldContent('title'));
        $escapedBrand = preg_quote($brandName, '/');
        $pageTitle = (string) preg_replace('/^\s*' . $escapedBrand . '\s*-\s*/i', '', $rawTitle);
        $pageTitle = (string) preg_replace('/\s*-\s*' . $escapedBrand . '\s*$/i', '', $pageTitle);
        $pageTitle = trim($pageTitle);
        $fullTitle = ($pageTitle === '' || strcasecmp($pageTitle, 'home') === 0)
            ? $brandName
            : ($brandName . ' - ' . $pageTitle);
    @endphp
    <title>{{ $fullTitle }}</title>
    <link rel="icon" type="image/png" href="{{ asset(config('store.favicon_path')) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --fc-dark: #1f1f1f;
            --fc-muted: #737373;
            --fc-bg: #ffffff;
            --fc-soft-bg: #f7f7f7;
            --fc-accent: #72aec8;
            --fc-accent-dark: #4585a1;
            --fc-border: #ececec;
        }
        body {
            font-family: 'Jost', sans-serif;
            background: var(--fc-bg);
            color: #2a2a2a;
        }
        .fc-topbar {
            background: var(--fc-soft-bg);
            border-bottom: 1px solid var(--fc-border);
        }
        .fc-topbar-text {
            font-size: .82rem;
            color: var(--fc-muted);
            letter-spacing: .2px;
        }
        .fc-navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--fc-border);
            box-shadow: 0 2px 14px rgba(0, 0, 0, .03);
        }
        .fc-navbar .container {
            min-height: 82px;
            align-items: center;
        }
        .fc-navbar .navbar-brand {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .15rem 0;
            margin-right: 1.25rem;
        }
        .fc-brand-logo {
            height: 38px;
            width: auto;
            display: block;
            max-width: 100%;
        }
        .fc-navbar .navbar-collapse {
            align-items: center;
        }
        .fc-navbar .navbar-nav {
            align-items: center;
            gap: .15rem;
        }
        .fc-navbar .nav-link {
            color: #212529;
            font-weight: 400;
            text-transform: uppercase;
            font-size: .82rem;
            letter-spacing: .7px;
            padding-top: .55rem;
            padding-bottom: .55rem;
            padding-left: .75rem;
            padding-right: .75rem;
        }
        .fc-navbar .nav-link:hover,
        .fc-navbar .nav-link:focus { color: var(--fc-accent-dark); }
        .fc-nav-actions {
            min-width: 280px;
            justify-content: flex-end;
        }
        .fc-search-input {
            border-radius: 2rem;
            border: 1px solid #dfdfdf;
            padding-left: .8rem;
            padding-right: .8rem;
            min-width: 150px;
        }
        .fc-search-input:focus {
            border-color: var(--fc-accent);
            box-shadow: 0 0 0 .2rem rgba(114, 174, 200, .18);
        }
        .fc-search-btn {
            border-radius: 2rem;
            padding-left: 1rem;
            padding-right: 1rem;
            background: var(--fc-dark);
            border-color: var(--fc-dark);
        }
        .fc-cart-btn {
            border-radius: 2rem;
            padding-left: .9rem;
            padding-right: .9rem;
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            border-color: #d8d8d8;
        }
        .fc-cart-btn:hover {
            border-color: var(--fc-accent);
            color: var(--fc-accent-dark);
        }
        .fc-main { min-height: 70vh; }
        .fc-hero {
            background: linear-gradient(130deg, #1b1b1b, #3b3b3b);
            color: #fff;
            border-radius: 0;
        }
        .fc-section-title {
            font-weight: 600;
            letter-spacing: .2px;
            text-transform: uppercase;
            font-size: 1.25rem;
        }
        .fc-product-card {
            border: 1px solid var(--fc-border);
            border-radius: 0;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .fc-product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,.08);
        }
        .fc-product-card img { height: 260px; object-fit: cover; border-radius: 0; }
        .fc-price-old { text-decoration: line-through; color: #9a9a9a; font-size: .9rem; }
        .fc-price-new { font-weight: 700; color: #111; }
        .fc-pill {
            display: inline-block;
            border: 1px solid #ffffff66;
            color: #fff;
            font-size: .78rem;
            letter-spacing: .6px;
            text-transform: uppercase;
            padding: .25rem .6rem;
        }
        .fc-home-hero-title {
            font-size: clamp(2rem, 5vw, 3.4rem);
            line-height: 1.05;
            letter-spacing: .4px;
        }
        .fc-home-hero-sub {
            color: #e5e7eb;
            max-width: 560px;
            font-size: 1.02rem;
        }
        .fc-kaira-banner {
            border: 1px solid var(--fc-border);
            background: #fff;
        }
        .fc-kaira-banner-dark {
            background: #161616;
            color: #fff;
            border: 0;
        }
        .fc-category-card {
            border: 1px solid var(--fc-border);
            background: #fff;
            transition: all .2s ease;
        }
        .fc-category-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(0,0,0,.06);
        }
        .fc-home-heading-row a {
            font-size: .88rem;
            letter-spacing: .4px;
            text-transform: uppercase;
            color: #111;
            text-decoration: none;
            border-bottom: 1px solid #111;
        }
        .product-thumb-image { transition: all .2s ease; }
        .product-thumb-btn:hover .product-thumb-image { transform: scale(1.04); }
        .fc-footer {
            background: #ffffff;
            border-top: 1px solid var(--fc-border);
        }
        .fc-footer h6 {
            text-transform: uppercase;
            font-size: .82rem;
            letter-spacing: .8px;
            margin-bottom: .9rem;
            color: #111;
        }
        .fc-footer a {
            color: #646464;
            text-decoration: none;
            font-size: .9rem;
        }
        .fc-footer a:hover { color: var(--fc-accent-dark); }
        .fc-footer-desc { max-width: 360px; }
        .fc-footer-meta {
            border-top: 1px solid var(--fc-border);
            padding-top: 1.2rem;
        }
        .fc-pill-soft {
            display: inline-block;
            background: #eef7fb;
            color: #4f8da6;
            font-size: .72rem;
            letter-spacing: .6px;
            text-transform: uppercase;
            padding: .3rem .55rem;
            border-radius: .3rem;
        }
        @media (max-width: 991.98px) {
            .fc-navbar .container { min-height: 74px; }
            .fc-navbar .nav-link { padding-left: 0; padding-right: 0; }
            .fc-brand-logo { height: 34px; }
            .fc-nav-actions {
                min-width: 100%;
                justify-content: flex-start;
                margin-top: .75rem;
            }
            .fc-search-input { min-width: 0; width: 170px; }
        }
    </style>
</head>
<body>
@include('frontend.partials.header')

<main class="py-4 fc-main">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>
</main>

@include('frontend.partials.footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
