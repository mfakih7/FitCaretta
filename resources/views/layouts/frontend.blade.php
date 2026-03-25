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
            --fc-soft-bg: #f6f5f2;
            --fc-accent: #72aec8;
            --fc-accent-dark: #4585a1;
            --fc-border: #ececec;
            --fc-border-strong: #dedede;
            --fc-ink: #111111;
        }
        body {
            font-family: 'Jost', sans-serif;
            background: var(--fc-bg);
            color: #2a2a2a;
        }
        a { color: inherit; }
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
            position: relative;
        }
        .fc-navbar .nav-link::after {
            content: '';
            position: absolute;
            left: .75rem;
            right: .75rem;
            bottom: .35rem;
            height: 1px;
            background: currentColor;
            opacity: .45;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform .18s ease, opacity .18s ease;
        }
        .fc-navbar .nav-link:hover::after,
        .fc-navbar .nav-link:focus::after {
            transform: scaleX(1);
            opacity: .45;
        }
        .fc-navbar .nav-link.active::after,
        .fc-navbar .nav-link[aria-current="page"]::after {
            transform: scaleX(1);
            opacity: .9;
        }
        .fc-navbar .nav-link:hover,
        .fc-navbar .nav-link:focus { color: var(--fc-accent-dark); }
        .fc-navbar .nav-link.active,
        .fc-navbar .nav-link[aria-current="page"] { color: var(--fc-ink); }
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
        /* Premium minimal system (scoped to fc-* only) */
        .fc-section { padding-top: 1.25rem; padding-bottom: 1.25rem; }
        .fc-soft-surface { background: var(--fc-soft-bg); border: 1px solid var(--fc-border); }
        .fc-eyebrow {
            font-size: .72rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--fc-muted);
        }
        .fc-link-underline {
            text-decoration: none;
            border-bottom: 1px solid currentColor;
            padding-bottom: 2px;
        }
        .fc-media {
            position: relative;
            overflow: hidden;
            background: #f2f2f2;
        }
        .fc-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1);
            transition: transform .35s ease;
        }
        .fc-hover-zoom:hover .fc-media img { transform: scale(1.045); }

        /* Category tiles */
        .fc-category-tile {
            border: 1px solid var(--fc-border);
            background: #fff;
            display: block;
            text-decoration: none;
            color: var(--fc-ink);
        }
        .fc-category-tile .fc-media {
            aspect-ratio: 4 / 5;
            background: var(--fc-soft-bg);
        }
        /* Categories often have mixed image dimensions; contain looks cleaner than aggressive cropping. */
        .fc-category-tile .fc-media img {
            object-fit: contain;
            object-position: center;
            padding: 14px;
        }
        .fc-category-tile.fc-hover-zoom:hover .fc-media img { transform: scale(1.02); }
        .fc-category-tile-label {
            position: absolute;
            top: 14px;
            left: 14px;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .55rem;
            border: 1px solid rgba(255,255,255,.55);
            background: rgba(17,17,17,.35);
            color: #fff;
            font-size: .72rem;
            letter-spacing: .11em;
            text-transform: uppercase;
            backdrop-filter: blur(6px);
        }
        .fc-category-tile-body { padding: 14px; }
        .fc-category-tile-title {
            font-size: 1.02rem;
            letter-spacing: .2px;
            margin: 0;
            color: var(--fc-ink);
        }
        .fc-category-tile-meta { color: var(--fc-muted); font-size: .88rem; }

        /* Product tiles */
        .fc-product-card {
            border: 1px solid var(--fc-border);
            border-radius: 0;
            background: #fff;
            transition: border-color .2s ease, transform .2s ease;
        }
        .fc-product-card:hover { transform: translateY(-1px); border-color: var(--fc-border-strong); }
        .fc-product-card .fc-media {
            aspect-ratio: 4 / 5;
            background: var(--fc-soft-bg);
        }
        /* Product images: keep a consistent frame, avoid awkward crops for mixed assets */
        .fc-product-card .fc-media img {
            object-fit: contain;
            object-position: center;
            padding: 10px;
        }
        .fc-product-card.fc-hover-zoom:hover .fc-media img { transform: scale(1.02); }
        .fc-product-title {
            font-size: .95rem;
            font-weight: 500;
            line-height: 1.2;
            margin: 0;
            color: var(--fc-ink);
        }
        .fc-product-sub { font-size: .82rem; color: var(--fc-muted); }
        .fc-badge {
            display: inline-flex;
            align-items: center;
            padding: .25rem .5rem;
            border: 1px solid var(--fc-border);
            background: #fff;
            font-size: .7rem;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--fc-ink);
        }
        .fc-badge-dark { border-color: rgba(255,255,255,.55); background: rgba(17,17,17,.35); color: #fff; backdrop-filter: blur(6px); }
        .fc-badges {
            position: absolute;
            top: 14px;
            right: 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            align-items: flex-end;
        }

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

        /* Product details gallery */
        .fc-product-gallery-main {
            width: 100%;
            aspect-ratio: 4 / 5;
            background: var(--fc-soft-bg);
            border: 1px solid var(--fc-border);
            object-fit: contain;
            object-position: center;
            padding: 14px;
        }
        .fc-product-thumbs { gap: .6rem; }
        .fc-product-thumb {
            width: 74px;
            height: 74px;
            border: 1px solid var(--fc-border);
            background: var(--fc-soft-bg);
            padding: 6px;
        }
        .fc-product-thumb img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
        }
        .product-thumb-image.border-primary.border-2 { border-color: var(--fc-ink) !important; }

        /* Pagination */
        .pagination { gap: .35rem; }
        .page-link {
            border-radius: 0 !important;
            border: 1px solid var(--fc-border);
            color: var(--fc-ink);
            padding: .5rem .75rem;
        }
        .page-link:hover { border-color: var(--fc-border-strong); color: var(--fc-ink); background: #fff; }
        .page-item.active .page-link {
            background: var(--fc-ink);
            border-color: var(--fc-ink);
            color: #fff;
        }
        .page-item.disabled .page-link { color: #9b9b9b; background: #fff; }
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
