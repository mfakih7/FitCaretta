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
            --fc-shadow-xs: 0 1px 2px rgba(17,17,17,.04);
            --fc-shadow-sm: 0 10px 24px rgba(17,17,17,.07);

            /* Card media sizing + framing (consistency across grids) */
            /* Heights kept as soft caps; aspect-ratio drives actual sizing */
            --fc-product-media-max-h: 320px;
            --fc-category-media-max-h: 520px;
            --fc-card-media-bg: linear-gradient(180deg, #f7f7f7, #f2f2f2);
            /* Media padding defaults (keep for future tuning) */
            --fc-product-media-pad: 12px;
            --fc-category-media-pad: 0px;
        }
        body {
            font-family: 'Jost', sans-serif;
            background: var(--fc-bg);
            color: #2a2a2a;
        }
        /* Prevent horizontal scroll from full-bleed sections */
        html, body { overflow-x: hidden; }
        a { color: inherit; }
        .fc-topbar {
            background: var(--fc-ink);
            border-bottom: 1px solid rgba(255,255,255,.12);
        }
        .fc-topbar-text {
            font-size: .82rem;
            color: rgba(255,255,255,.92);
            font-weight: 500;
            letter-spacing: .25px;
        }
        /* Branded loader */
        .fc-loader{
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .fc-loader.is-active{ display:flex; }
        .fc-loader-backdrop{
            position:absolute;
            inset:0;
            background: radial-gradient(1200px 600px at 50% 30%, rgba(255,255,255,.92), rgba(255,255,255,.78));
            backdrop-filter: blur(10px);
        }
        .fc-loader-stage{
            position: relative;
            width: 148px;
            height: 148px;
            display:flex;
            align-items:center;
            justify-content:center;
            transform: translateZ(0);
        }
        .fc-loader-glow{
            position:absolute;
            inset: -18px;
            border-radius: 999px;
            background:
                radial-gradient(circle at 50% 50%, rgba(114, 174, 200, .20), rgba(114, 174, 200, 0) 62%),
                radial-gradient(circle at 50% 60%, rgba(0,0,0,.08), rgba(0,0,0,0) 62%);
            filter: blur(2px);
            animation: fcGlow 1.7s ease-in-out infinite;
        }
        .fc-loader-walk{
            position: relative;
            width: 96px;
            height: 96px;
            display:flex;
            align-items:center;
            justify-content:center;
            transform: translateZ(0);
        }
        .fc-loader-shadow{
            position: absolute;
            bottom: 10px;
            left: 50%;
            width: 58px;
            height: 14px;
            transform: translateX(-50%);
            border-radius: 999px;
            background: radial-gradient(circle at 50% 50%, rgba(0,0,0,.22), rgba(0,0,0,0) 68%);
            filter: blur(.2px);
            animation: fcShadowWalk 1.1s ease-in-out infinite;
        }
        .fc-loader-mark{
            width: 64px;
            height: 64px;
            filter: drop-shadow(0 16px 30px rgba(0,0,0,.08));
            animation: fcCarettaWalk 1.1s cubic-bezier(.45,0,.2,1) infinite;
            transform-origin: 55% 65%;
            user-select:none;
            -webkit-user-drag:none;
            overflow: visible;
        }
        .fc-loader-mark .fc-turtle-shell{ fill: var(--fc-ink); }
        .fc-loader-mark .fc-turtle-head{ fill: var(--fc-ink); opacity: .96; }
        .fc-loader-mark .fc-turtle-flipper{ fill: var(--fc-ink); opacity: .96; }
        .fc-loader-dots{
            position:absolute;
            bottom: -18px;
            left: 50%;
            transform: translateX(-50%);
            display:flex;
            gap: 6px;
        }
        .fc-loader-dots span{
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: rgba(17,17,17,.45);
            animation: fcDot 1s ease-in-out infinite;
        }
        .fc-loader-dots span:nth-child(2){ animation-delay: .12s; opacity:.78; }
        .fc-loader-dots span:nth-child(3){ animation-delay: .24s; opacity:.55; }
        .fc-loader.is-hidden{
            display:flex;
            pointer-events:none;
            opacity: 0;
            transition: opacity .34s ease;
        }
        .fc-loader.is-active{ opacity: 1; }

        @keyframes fcCarettaWalk{
            0%   { transform: translateX(-3px) translateY(0) rotate(-1.2deg) scale(1); }
            20%  { transform: translateX(-1px) translateY(-1.6px) rotate(0.6deg) scale(1.01); }
            50%  { transform: translateX(3px) translateY(0) rotate(1.2deg) scale(1); }
            80%  { transform: translateX(1px) translateY(-1.6px) rotate(-0.6deg) scale(1.01); }
            100% { transform: translateX(-3px) translateY(0) rotate(-1.2deg) scale(1); }
        }
        @keyframes fcShadowWalk{
            0%   { transform: translateX(-50%) scaleX(.92); opacity: .42; }
            20%  { transform: translateX(-48%) scaleX(.80); opacity: .28; }
            50%  { transform: translateX(-50%) scaleX(.92); opacity: .42; }
            80%  { transform: translateX(-52%) scaleX(.80); opacity: .28; }
            100% { transform: translateX(-50%) scaleX(.92); opacity: .42; }
        }
        @keyframes fcGlow {
            0%,100% { opacity: .65; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.02); }
        }
        @keyframes fcDot {
            0%,100% { transform: translateY(0); opacity: .35; }
            50% { transform: translateY(-3px); opacity: .95; }
        }

        .fc-inline-loader{
            position: relative;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width: 28px;
            height: 28px;
            margin-left: .5rem;
            vertical-align: middle;
        }
        .fc-inline-shadow{
            position:absolute;
            bottom: 4px;
            left: 50%;
            width: 18px;
            height: 7px;
            transform: translateX(-50%);
            border-radius: 999px;
            background: radial-gradient(circle at 50% 50%, rgba(0,0,0,.22), rgba(0,0,0,0) 70%);
            animation: fcShadowWalk 1.1s ease-in-out infinite;
            opacity: .55;
        }
        .fc-inline-mark{
            width: 16px;
            height: 16px;
            opacity: .95;
            transform-origin: 55% 65%;
            animation: fcCarettaWalk 1.1s cubic-bezier(.45,0,.2,1) infinite;
        }
        .fc-inline-mark .fc-turtle-shell{ fill: var(--fc-ink); }
        .fc-inline-mark .fc-turtle-head{ fill: var(--fc-ink); opacity: .96; }
        .fc-inline-mark .fc-turtle-flipper{ fill: var(--fc-ink); opacity: .96; }

        @media (prefers-reduced-motion: reduce){
            .fc-loader-glow, .fc-loader-mark, .fc-loader-dots span, .fc-loader-shadow, .fc-inline-shadow, .fc-inline-mark { animation: none !important; }
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
        .fc-header-grid{
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 14px;
            width: 100%;
        }
        .fc-header-left{ display:flex; align-items:center; }
        .fc-header-center{ display:flex; justify-content:center; }
        .fc-header-right{ display:flex; align-items:center; justify-content:flex-end; gap: 10px; }
        .fc-header-logo{ display:inline-flex; align-items:center; justify-content:center; padding:.15rem 0; }
        .fc-header-burger{ border:0; padding:.35rem .4rem; }
        .fc-header-collapse{ }
        .fc-header-menu{ align-items:center; gap:.15rem; }

        .fc-icon-btn{
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 1px solid var(--fc-border);
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all .14s ease;
            color: var(--fc-ink);
        }
        .fc-icon-btn:hover{
            border-color: var(--fc-border-strong);
            box-shadow: 0 10px 18px rgba(0,0,0,.05);
            transform: translateY(-1px);
            color: var(--fc-accent-dark);
        }
        .fc-icon-btn svg{ width: 20px; height: 20px; }
        .fc-cart-badge{
            position:absolute;
            top: -6px;
            right: -6px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 999px;
            background: var(--fc-ink);
            color:#fff;
            font-size: .72rem;
            line-height: 18px;
            text-align:center;
        }

        .fc-header-search{ display:flex; align-items:center; gap:8px; }
        .fc-header-search-form{
            width: 0;
            overflow: hidden;
            transition: width .18s ease;
        }
        .fc-header-search[data-open="1"] .fc-header-search-form{
            width: 220px;
        }
        .fc-header-search .fc-search-input{
            width: 220px;
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
        /* Full-bleed hero inside a .container page layout */
        .fc-hero-fullbleed {
            width: 100vw;
            max-width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
        }
        .fc-hero-carousel .carousel-item {
            min-height: clamp(360px, 46vh, 520px);
        }
        .fc-hero-slide {
            position: relative;
            min-height: inherit;
            background-size: cover;
            background-position: center;
        }
        .fc-hero-slide::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(0,0,0,.58), rgba(0,0,0,.25) 55%, rgba(0,0,0,.15));
        }
        .fc-hero-slide-inner {
            position: relative;
            z-index: 1;
        }
        .fc-hero-carousel .carousel-indicators [data-bs-target] {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            opacity: .55;
        }
        .fc-hero-carousel .carousel-indicators .active { opacity: 1; }
        .fc-hero-carousel .carousel-control-prev,
        .fc-hero-carousel .carousel-control-next {
            width: 7%;
            min-width: 52px;
            opacity: .85;
        }
        .fc-hero-carousel .carousel-control-prev:hover,
        .fc-hero-carousel .carousel-control-next:hover { opacity: 1; }
        .fc-section-title {
            font-weight: 600;
            letter-spacing: .2px;
            text-transform: uppercase;
            font-size: 1.25rem;
        }

        /* ----------------------------
           Global button design system
           ---------------------------- */
        .btn {
            border-radius: 999px;
            font-weight: 600;
            letter-spacing: .4px;
            text-transform: uppercase;
            padding: .68rem 1.15rem;
            line-height: 1.05;
        }
        .btn-sm {
            padding: .5rem .9rem;
            font-size: .82rem;
        }
        .btn:focus-visible {
            outline: none;
            box-shadow: 0 0 0 .22rem rgba(114, 174, 200, .18);
        }

        /* Primary */
        .btn-primary,
        .btn-dark {
            background: var(--fc-ink);
            border-color: var(--fc-ink);
            color: #fff;
        }
        .btn-primary:hover,
        .btn-dark:hover {
            background: #000;
            border-color: #000;
            color: #fff;
        }

        /* Secondary */
        .btn-outline-dark,
        .btn-outline-light {
            border-width: 1px;
        }
        .btn-outline-dark {
            border-color: var(--fc-ink);
            color: var(--fc-ink);
            background: transparent;
        }
        .btn-outline-dark:hover {
            background: var(--fc-ink);
            border-color: var(--fc-ink);
            color: #fff;
        }
        .btn-light {
            background: #fff;
            border-color: #fff;
            color: var(--fc-ink);
        }
        .btn-light:hover {
            background: #f3f4f6;
            border-color: #f3f4f6;
            color: var(--fc-ink);
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
        /* Collection filters */
        .fc-filters-card{
            background:#fff;
            border:1px solid var(--fc-border);
            box-shadow: 0 14px 34px rgba(0,0,0,.04);
            border-radius: 16px;
            padding: 16px;
        }
        .fc-filters-head{
            display:flex;
            align-items:flex-end;
            justify-content:space-between;
            gap: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--fc-border);
            margin-bottom: 12px;
        }
        .fc-filters-title{
            font-weight: 700;
            letter-spacing: .2px;
            font-size: 1.05rem;
            color: var(--fc-ink);
        }
        .fc-filters-sub{
            font-size: .88rem;
            color: var(--fc-muted);
            margin-top: 2px;
        }
        .fc-filters-chip{
            display:inline-flex;
            align-items:center;
            padding: .35rem .6rem;
            border:1px solid var(--fc-border);
            border-radius: 999px;
            font-size: .78rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--fc-ink);
            background: var(--fc-soft-bg);
        }
        .fc-filters-form{ margin: 0; }
        .fc-filters-grid{
            display:grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 12px;
        }
        .fc-field-label{
            display:block;
            font-size: .72rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--fc-muted);
            margin-bottom: 6px;
        }
        .fc-select{
            border-radius: 999px;
            border: 1px solid var(--fc-border);
            padding-left: 14px;
            padding-right: 36px;
            min-height: 44px;
            font-size: .92rem;
            background-color: #fff;
        }
        .fc-select:focus{
            border-color: var(--fc-accent);
            box-shadow: 0 0 0 .2rem rgba(114, 174, 200, .18);
        }
        .fc-filters-footer{
            display:flex;
            justify-content:flex-end;
            gap: 10px;
            margin-top: 14px;
        }
        @media (max-width: 1199.98px){
            .fc-filters-grid{ grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (max-width: 767.98px){
            .fc-filters-card{ padding: 14px; border-radius: 14px; }
            .fc-filters-grid{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .fc-filters-footer{ justify-content: stretch; }
            .fc-filters-footer .btn{ width: 100%; }
        }
        .fc-media {
            position: relative;
            overflow: hidden;
            background: #f2f2f2;
            display: block;
        }
        .fc-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1);
            transition: transform .35s ease;
            display: block;
        }
        .fc-hover-zoom:hover .fc-media img { transform: scale(1.045); }

        /* Category tiles */
        .fc-category-tile {
            border: 1px solid var(--fc-border);
            background: #fff;
            display: block;
            text-decoration: none;
            color: var(--fc-ink);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--fc-shadow-xs);
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }
        .fc-category-tile:hover{
            transform: translateY(-4px);
            border-color: var(--fc-border-strong);
            box-shadow: var(--fc-shadow-sm);
        }
        .fc-category-tile .fc-media {
            aspect-ratio: 3 / 4;
            height: auto;
       
            background: #f4f4f4;
            overflow: hidden;
        }

        .fc-category-tile .fc-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            padding: var(--fc-category-media-pad);
            display: block;
        }
        .fc-category-tile:hover .fc-media img{ transform: scale(1.02); }
        .fc-category-tile .fc-media::after{
            content:'';
            position:absolute;
            inset:0;
            background: linear-gradient(180deg, rgba(0,0,0,.00) 58%, rgba(0,0,0,.22));
            pointer-events:none;
            opacity: .7;
            transition: opacity .22s ease;
        }
        .fc-category-tile:hover .fc-media::after{ opacity: .85; }
        .fc-category-tile-label {
            position: absolute;
            top: 12px;
            left: 12px;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .28rem .55rem;
            border: 1px solid rgba(255,255,255,.30);
            background: rgba(17,17,17,.55);
            color: #fff;
            font-size: .72rem;
            letter-spacing: .10em;
            text-transform: uppercase;
            backdrop-filter: blur(10px);
        }
        .fc-category-tile-body { padding: 16px 18px 18px; }
        .fc-category-tile-title {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: .1px;
            margin: 0;
            color: var(--fc-ink);
        }
        .fc-category-tile-meta { color: rgba(17,17,17,.55); font-size: .9rem; }
        .fc-category-tile .fc-link-underline{
            color: var(--fc-ink);
            border-bottom-color: rgba(17,17,17,.35);
            transition: color .18s ease, border-color .18s ease, transform .18s ease;
        }
        .fc-category-tile:hover .fc-link-underline{
            color: var(--fc-accent-dark);
            border-bottom-color: var(--fc-accent-dark);
            transform: translateX(2px);
        }

        /* Product tiles */
        .fc-product-card {
            border: 1px solid var(--fc-border);
            border-radius: 16px;
            background: #fff;
            overflow: hidden;
            box-shadow: var(--fc-shadow-xs);
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }
        .fc-product-card:hover{
            transform: translateY(-4px);
            border-color: var(--fc-border-strong);
            box-shadow: var(--fc-shadow-sm);
        }
        .fc-product-card .fc-media {
            aspect-ratio: 4 / 5;
            height: auto;
           
            background: var(--fc-card-media-bg);
            overflow: hidden;
            display: grid;
            place-items: center;
        }

        .fc-product-card .fc-media img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            padding: var(--fc-product-media-pad);
            display: block;
        }
        .fc-product-card:hover .fc-media img{ transform: scale(1.01); }
        .fc-product-title {
            font-size: .95rem;
            font-weight: 500;
            line-height: 1.2;
            margin: 0;
            color: var(--fc-ink);
        }
        .fc-product-sub { font-size: .82rem; color: var(--fc-muted); }

        /* Product card (modern premium layout) */
        .fc-pcard-media{ position: relative; }
        .fc-pcard-media::after{
            content:'';
            position:absolute;
            inset:0;
            background: linear-gradient(180deg, rgba(0,0,0,.00) 58%, rgba(0,0,0,.18));
            pointer-events:none;
            opacity: .55;
            transition: opacity .22s ease;
            z-index: 1;
        }
        .fc-product-card:hover .fc-pcard-media::after{ opacity: .75; }
        .fc-pcard-media .fc-media{ position: relative; z-index: 0; }
        .fc-pcard-media .fc-badges{ z-index: 3; }
        .fc-pcard-quickadd{
            position:absolute;
            right: 12px;
            bottom: 12px;
            z-index: 4;
            opacity: 0;
            transform: translateY(8px);
            pointer-events: none;
        }
        .fc-product-card:hover .fc-pcard-quickadd{
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        /* Fix quick-add icon alignment + reliable centering */
        .fc-card-quickadd{
            width: 44px;
            height: 44px;
            border-radius: 999px;
            border: 1px solid rgba(17,17,17,.14);
            background: rgba(255,255,255,.92);
            display: grid;
            place-items: center;
            color: var(--fc-ink);
            box-shadow: 0 10px 22px rgba(0,0,0,.10);
            transition: transform .26s ease, box-shadow .26s ease, background-color .26s ease, border-color .26s ease, color .26s ease;
        }
        .fc-card-quickadd svg{
            width: 18px;
            height: 18px;
            display:block;
        }
        .fc-card-quickadd:hover{
            background: var(--fc-ink);
            border-color: var(--fc-ink);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(0,0,0,.18);
        }
        .fc-card-quickadd:active{ transform: translateY(0); box-shadow: 0 10px 22px rgba(0,0,0,.10); }
        .fc-card-quickadd:focus-visible{ outline: 0; box-shadow: 0 0 0 .2rem rgba(17,17,17,.18), 0 14px 30px rgba(0,0,0,.14); }
        .fc-pcard-body{
            padding: 16px 18px 18px;
            display:flex;
            flex-direction:column;
            gap: 10px;
        }
        .fc-pcard-kicker{
            font-size: .78rem;
            color: rgba(17,17,17,.55);
            letter-spacing: .10em;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .fc-pcard-title{
            margin: 0;
            font-size: 1.02rem;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: .1px;
            color: var(--fc-ink);
        }
        .fc-pcard-price{
            display:flex;
            align-items: baseline;
            gap: 10px;
        }
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
        .fc-badge-dark { border-color: rgba(255,255,255,.35); background: rgba(17,17,17,.55); color: #fff; backdrop-filter: blur(10px); }
        .fc-badge-sale{
            border-color: rgba(255,255,255,.25);
            background: linear-gradient(135deg, rgba(255,72,72,.85), rgba(255,0,92,.70));
            color:#fff;
            backdrop-filter: blur(10px);
        }
        .fc-badges {
            position: absolute;
            top: 14px;
            left: 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            align-items: flex-start;
        }

        .fc-price-old { text-decoration: line-through; color: #9a9a9a; font-size: .9rem; }
        .fc-price-new { font-weight: 700; color: #111; }

        /* Swatches */
        .fc-card-swatch{
            width: 20px;
            height: 20px;
            border-radius: 999px;
            border: 1px solid rgba(0,0,0,.14);
            display:inline-block;
            transition: transform .16s ease;
        }
        .fc-card-swatches{ display:flex; gap:8px; flex-wrap:wrap; }
        .fc-card-swatches button{ border:0; background:transparent; padding:0; line-height:0; }
        .fc-card-swatches button:hover .fc-card-swatch{ transform: scale(1.08); }
        .fc-card-swatches button.is-active .fc-card-swatch{ box-shadow:0 0 0 2px rgba(17,17,17,.16); border-color: var(--fc-ink); }

        /* Size pills */
        .fc-pcard-sizes{
            display:flex;
            flex-wrap:wrap;
            gap: 8px;
        }
        .fc-pcard-size{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            height: 30px;
            padding: 0 10px;
            border: 1px solid rgba(17,17,17,.12);
            border-radius: 999px;
            font-size: .82rem;
            color: rgba(17,17,17,.72);
            background: #fff;
            transition: all .18s ease;
        }
        .fc-pcard-size:hover{
            border-color: rgba(17,17,17,.22);
            color: var(--fc-ink);
        }
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
            .fc-navbar .container { min-height: 66px; }
            .fc-navbar .nav-link { padding-left: 0; padding-right: 0; }
            .fc-brand-logo { height: 32px; }
            .fc-header-grid{ grid-template-columns: auto 1fr auto; }
            .fc-header-left{ justify-content:flex-start; }
            .fc-header-center{ justify-content:center; }
            .fc-header-right{ justify-content:flex-end; }
            .fc-header-search[data-open="1"] .fc-header-search-form{ width: 160px; }
            .fc-header-search .fc-search-input{ width: 160px; }
            .fc-icon-btn{ width: 40px; height: 40px; }

            /* Mobile/tablet: compact topbar */
            .fc-topbar .container{ padding-top: .45rem !important; padding-bottom: .45rem !important; }
            .fc-topbar-text{ font-size: .78rem; }

            /* Mobile/tablet offcanvas menu */
            .fc-mobile-nav .offcanvas-header{ padding: 16px 16px 10px; }
            .fc-mobile-nav .offcanvas-body{ padding: 8px 16px 16px; }
            .fc-mobile-nav-list .nav-link{
                padding: .75rem .25rem;
                font-weight: 500;
                letter-spacing: .04em;
                text-transform: none;
                border-bottom: 1px solid var(--fc-border);
            }
            .fc-mobile-nav-list .nav-link.active{ color: var(--fc-ink); }
            .fc-mobile-nav-list .nav-link:last-child{ border-bottom: 0; }

            /* Hero: reduce height and tighten typography on smaller screens */
            .fc-hero-carousel .carousel-item{ min-height: 62vh; }
            .fc-hero-content{ padding-top: 2.25rem; padding-bottom: 2.25rem; }
            .fc-home-hero-title{ font-size: clamp(1.8rem, 7vw, 2.6rem); }
            .fc-hero .fc-home-hero-sub{ font-size: 1rem; max-width: 520px; }

            /* Listing controls (mobile/tablet) */
            .fc-mobile-controls{
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap: 10px;
                padding: 10px 12px;
                border: 1px solid var(--fc-border);
                border-radius: 14px;
                background: #fff;
                box-shadow: var(--fc-shadow-xs);
            }
            .fc-mobile-controls-right{
                display:flex;
                align-items:center;
                gap: 8px;
            }
            .fc-mobile-sort-select{
                min-width: 128px;
                border-radius: 999px;
                border-color: var(--fc-border);
            }
            .fc-mobile-filter-btn{
                border-radius: 999px;
                white-space: nowrap;
            }
            .fc-filters-canvas{ width: min(420px, 92vw); }
            .fc-filters-canvas .offcanvas-body{ padding-bottom: 20px; }

            /* Footer (mobile/tablet) – compact + accordion */
            .fc-footer.mt-5{ margin-top: 2.5rem !important; }
            .fc-footer-mobile .py-4{ padding-bottom: 1.1rem !important; }
            .fc-footer-mobile-trust{ padding: 10px 0 16px; border-top: 1px solid var(--fc-border); border-bottom: 1px solid var(--fc-border); }
            .fc-footer-mobile-trust-item{
                text-align:center;
                font-size: .78rem;
                color: rgba(17,17,17,.65);
                padding: .55rem .4rem;
                border: 1px solid var(--fc-border);
                border-radius: 12px;
                background: #fff;
            }
            .fc-footer-acc .accordion-item{
                border: 1px solid var(--fc-border);
                border-radius: 14px;
                overflow: hidden;
                background: #fff;
                box-shadow: var(--fc-shadow-xs);
            }
            .fc-footer-acc .accordion-item + .accordion-item{ margin-top: 10px; }
            .fc-footer-acc .accordion-button{
                padding: .9rem 1rem;
                font-weight: 600;
                letter-spacing: .02em;
                background: #fff;
            }
            .fc-footer-acc .accordion-button:not(.collapsed){
                color: var(--fc-ink);
                background: #fff;
                box-shadow: none;
            }
            .fc-footer-acc .accordion-body{ padding: .85rem 1rem 1rem; }
        }

        @media (max-width: 767.98px){
            /* Reduce overall vertical padding so shopping content appears sooner */
            main.fc-main{ padding-top: 1rem !important; padding-bottom: 1.25rem !important; }

            /* Cards: slightly smaller media heights on phones */
            :root{
                --fc-category-media-max-h: 320px;
                --fc-product-media-max-h: 260px;
                --fc-category-media-pad: 0px;
                --fc-product-media-pad: 10px;
            }
        }

        @media (max-width: 575.98px){
            .fc-navbar .container{ min-height: 62px; }
            .fc-icon-btn{ width: 36px; height: 36px; }
            .fc-icon-btn svg{ width: 18px; height: 18px; }
            .fc-header-search[data-open="1"] .fc-header-search-form{ width: 140px; }
            .fc-header-search .fc-search-input{ width: 140px; }

            .fc-hero-carousel .carousel-item{ min-height: 54vh; }
            .fc-hero-content{ padding-top: 1.85rem; padding-bottom: 1.85rem; }
            .fc-home-hero-title{ font-size: clamp(1.65rem, 7.6vw, 2.25rem); }
            .fc-hero .fc-home-hero-sub{ font-size: .98rem; }
        }

        /* Ultra-small edge case: keep cards readable */
        @media (max-width: 360px){
            .row > [class*="col-6"]{ flex: 0 0 auto; width: 100%; }
        }
    </style>
</head>
<body>
@include('partials.loader')
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
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @yield('content')
    </div>
</main>

@include('frontend.partials.footer')

<!-- Quick Add Drawer (global) -->
<div class="offcanvas offcanvas-end fc-quickadd" tabindex="-1" id="fcQuickAdd" aria-labelledby="fcQuickAddLabel">
    <div class="offcanvas-header border-bottom">
        <div>
            <div class="small text-muted">Quick Add</div>
            <h5 class="offcanvas-title mb-0" id="fcQuickAddLabel">Add to Cart</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="d-flex gap-3 align-items-start">
            <img id="fcQuickAddImage" src="{{ asset(\App\Models\Catalog\Product::DEFAULT_PLACEHOLDER) }}" alt="" class="rounded border" style="width:96px;height:120px;object-fit:contain;background:var(--fc-soft-bg);">
            <div class="flex-grow-1">
                <div class="fw-semibold" id="fcQuickAddName">—</div>
                <div class="text-muted small" id="fcQuickAddMeta">—</div>
                <div class="mt-1" id="fcQuickAddPrice"></div>
            </div>
        </div>

        <form class="mt-3" method="POST" action="{{ route('cart.store') }}" id="fcQuickAddForm">
            @csrf
            <input type="hidden" name="product_id" id="fcQuickAddProductId">
            <input type="hidden" name="color_id" id="fcQuickAddColorId">
            <input type="hidden" name="size_id" id="fcQuickAddSizeId">

            <div class="mt-3">
                <div class="small text-muted mb-1" style="letter-spacing:.08em;text-transform:uppercase;">Color</div>
                <div class="d-flex flex-wrap gap-2" id="fcQuickAddColors"></div>
                <div class="text-danger small mt-2 d-none" id="fcQuickAddColorError"></div>
            </div>

            <div class="mt-3">
                <div class="small text-muted mb-1" style="letter-spacing:.08em;text-transform:uppercase;">Size</div>
                <div class="d-flex flex-wrap gap-2" id="fcQuickAddSizes"></div>
                <div class="text-danger small mt-2 d-none" id="fcQuickAddSizeError"></div>
            </div>

            <div class="mt-3 d-flex align-items-end justify-content-between gap-2">
                <div>
                    <div class="small text-muted mb-1" style="letter-spacing:.08em;text-transform:uppercase;">Quantity</div>
                    <div class="d-flex align-items-center border rounded-pill p-1" style="width:132px;">
                        <button type="button" class="btn btn-light btn-sm rounded-pill" id="fcQuickAddQtyMinus" style="width:34px;height:34px;">-</button>
                        <input type="number" min="1" name="quantity" id="fcQuickAddQty" value="1" class="form-control form-control-sm border-0 text-center" style="width:54px;">
                        <button type="button" class="btn btn-light btn-sm rounded-pill" id="fcQuickAddQtyPlus" style="width:34px;height:34px;">+</button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-dark w-100 mt-3 rounded-pill py-2 fw-semibold" id="fcQuickAddSubmit">
                Add to Cart
            </button>

            <a href="#" class="fc-qa-view mt-2" id="fcQuickAddViewLink">
                View product
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h12"></path>
                    <path d="M13 6l6 6-6 6"></path>
                </svg>
            </a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (() => {
        const AUTO_DISMISS_MS = 5000;
        const alerts = Array.from(document.querySelectorAll('.alert'));
        if (!alerts.length) return;

        alerts.forEach((el) => {
            // Skip if already dismissed.
            if (!document.body.contains(el)) return;

            setTimeout(() => {
                if (!document.body.contains(el)) return;

                // Prefer Bootstrap's native close animation/cleanup when available.
                if (window.bootstrap?.Alert) {
                    window.bootstrap.Alert.getOrCreateInstance(el).close();
                    return;
                }

                el.classList.add('fade');
                el.classList.remove('show');
                setTimeout(() => el.remove(), 600);
            }, AUTO_DISMISS_MS);
        });
    })();
</script>
<script>
    (() => {
        const loader = document.getElementById('fcGlobalLoader');
        if (!loader) return;

        const hide = () => {
            loader.classList.remove('is-active');
            loader.classList.add('is-hidden');
            setTimeout(() => loader.remove(), 600);
        };

        // Avoid “stuck loader” in worst-case scenarios
        const failsafe = setTimeout(hide, 6500);

        window.addEventListener('load', () => {
            clearTimeout(failsafe);
            // Small delay prevents flicker on fast loads
            setTimeout(hide, 120);
        }, { once: true });

        // Minimal reusable API (for inline/AJAX use)
        window.FitCarettaLoader = window.FitCarettaLoader || {};
        window.FitCarettaLoader.show = () => {
            if (document.body.contains(loader)) {
                loader.classList.add('is-active');
                loader.classList.remove('is-hidden');
            }
        };
        window.FitCarettaLoader.hide = hide;

        window.FitCarettaLoader.inline = (target, on = true) => {
            if (!target) return;
            const tpl = document.getElementById('fcInlineLoaderTpl');
            if (!tpl) return;

            if (!on) {
                target.querySelector('.fc-inline-loader')?.remove();
                target.removeAttribute('aria-busy');
                return;
            }

            if (target.querySelector('.fc-inline-loader')) return;
            target.setAttribute('aria-busy', 'true');
            target.appendChild(tpl.content.cloneNode(true));
        };
    })();
</script>
<script>
    (() => {
        const offcanvasEl = document.getElementById('fcQuickAdd');
        if (!offcanvasEl) return;

        const instance = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);

        const els = {
            img: document.getElementById('fcQuickAddImage'),
            name: document.getElementById('fcQuickAddName'),
            meta: document.getElementById('fcQuickAddMeta'),
            price: document.getElementById('fcQuickAddPrice'),
            form: document.getElementById('fcQuickAddForm'),
            productId: document.getElementById('fcQuickAddProductId'),
            colorId: document.getElementById('fcQuickAddColorId'),
            sizeId: document.getElementById('fcQuickAddSizeId'),
            colors: document.getElementById('fcQuickAddColors'),
            sizes: document.getElementById('fcQuickAddSizes'),
            colorErr: document.getElementById('fcQuickAddColorError'),
            sizeErr: document.getElementById('fcQuickAddSizeError'),
            qty: document.getElementById('fcQuickAddQty'),
            qtyMinus: document.getElementById('fcQuickAddQtyMinus'),
            qtyPlus: document.getElementById('fcQuickAddQtyPlus'),
            view: document.getElementById('fcQuickAddViewLink'),
        };

        let state = { product: null, selectedColorId: '', selectedSizeId: '' };

        const showErr = (el, msg) => {
            if (!el) return;
            el.textContent = msg || '';
            el.classList.toggle('d-none', !msg);
        };

        const hasVariant = (sizeId, colorId) => {
            const v = state.product?.variants || [];
            return v.some(x => String(x.size_id) === String(sizeId) && String(x.color_id) === String(colorId) && (x.stock_qty ?? 0) > 0);
        };

        const setMainImageForColor = (colorId) => {
            const byColor = state.product?.imagesByColor || {};
            const key = String(colorId || '');
            const imgs = byColor[key] || [];
            const url = imgs?.[0]?.url || state.product?.defaultImageUrl;
            if (url && els.img) els.img.src = url;
        };

        const setActiveBtn = (wrap, attr, value) => {
            wrap?.querySelectorAll(`[${attr}]`).forEach(btn => {
                btn.classList.toggle('is-active', String(btn.getAttribute(attr)) === String(value));
            });
        };

        const render = () => {
            const p = state.product;
            if (!p) return;

            els.name.textContent = p.name || '—';
            els.meta.textContent = p.meta || '';
            els.price.innerHTML = p.priceHtml || '';
            els.productId.value = p.id;
            els.view.href = p.url;
            els.qty.value = '1';

            // colors
            els.colors.innerHTML = '';
            (p.colors || []).forEach(c => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'fc-qa-swatch';
                btn.setAttribute('data-color-id', c.id);
                btn.title = c.name;
                btn.innerHTML = `<span class="fc-qa-swatch-dot" style="background:${c.hex || '#111'}"></span>`;
                btn.addEventListener('click', () => {
                    state.selectedColorId = String(c.id);
                    els.colorId.value = state.selectedColorId;
                    setMainImageForColor(state.selectedColorId);
                    setActiveBtn(els.colors, 'data-color-id', state.selectedColorId);
                    showErr(els.colorErr, '');
                });
                els.colors.appendChild(btn);
            });

            // sizes
            els.sizes.innerHTML = '';
            (p.sizes || []).forEach(s => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'fc-qa-size';
                btn.setAttribute('data-size-id', s.id);
                btn.textContent = s.name;
                btn.addEventListener('click', () => {
                    state.selectedSizeId = String(s.id);
                    els.sizeId.value = state.selectedSizeId;
                    setActiveBtn(els.sizes, 'data-size-id', state.selectedSizeId);
                    showErr(els.sizeErr, '');
                });
                els.sizes.appendChild(btn);
            });

            // defaults
            state.selectedColorId = String(p.defaultColorId || '');
            state.selectedSizeId = '';
            els.colorId.value = state.selectedColorId;
            els.sizeId.value = '';
            setActiveBtn(els.colors, 'data-color-id', state.selectedColorId);
            setMainImageForColor(state.selectedColorId);
        };

        els.qtyMinus?.addEventListener('click', () => {
            const v = Math.max(1, parseInt(els.qty.value || '1', 10) - 1);
            els.qty.value = String(v);
        });
        els.qtyPlus?.addEventListener('click', () => {
            const v = Math.max(1, parseInt(els.qty.value || '1', 10) + 1);
            els.qty.value = String(v);
        });

        els.form?.addEventListener('submit', (e) => {
            const p = state.product;
            if (!p) return;
            const colorId = els.colorId.value || '';
            const sizeId = els.sizeId.value || '';
            const hasColors = (p.colors || []).length > 0;
            const hasSizes = (p.sizes || []).length > 0;

            if (hasColors && !colorId) {
                e.preventDefault();
                showErr(els.colorErr, 'Please select a color.');
                return;
            }
            if (hasSizes && !sizeId) {
                e.preventDefault();
                showErr(els.sizeErr, 'Please select a size.');
                return;
            }
            if (hasColors && hasSizes && !hasVariant(sizeId, colorId)) {
                e.preventDefault();
                showErr(els.sizeErr, 'This combination is not available.');
            }
        });

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-fc-quickadd]');
            if (!btn) return;
            e.preventDefault();
            const json = btn.getAttribute('data-fc-product');
            if (!json) return;
            try {
                state.product = JSON.parse(json);
            } catch (_) {
                state.product = null;
            }
            render();
            instance.show();
        });
    })();
</script>
<style>
    .fc-qa-swatch{
        width: 32px;height:32px;border-radius:999px;border:1px solid var(--fc-border);
        background:#fff;display:inline-flex;align-items:center;justify-content:center;
        transition: all .12s ease;
    }
    .fc-qa-swatch:hover{ transform: translateY(-1px); box-shadow:0 8px 18px rgba(0,0,0,.06); }
    .fc-qa-swatch.is-active{ border-color: var(--fc-ink); box-shadow:0 0 0 2px rgba(17,17,17,.12); }
    .fc-qa-swatch-dot{ width:18px;height:18px;border-radius:999px;border:1px solid rgba(0,0,0,.12); }
    .fc-qa-size{
        border:1px solid var(--fc-border); background:#fff; border-radius:999px;
        padding:.35rem .7rem; font-size:.9rem; line-height:1; transition: all .12s ease;
    }
    .fc-qa-size:hover{ border-color: var(--fc-border-strong); transform: translateY(-1px); }
    .fc-qa-size.is-active{ background: var(--fc-ink); color:#fff; border-color: var(--fc-ink); }
    .fc-quickadd{ width: min(420px, 92vw); }
    .fc-quickadd .offcanvas-body{ padding-top: 1rem; }
    .fc-qa-view{
        display:flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        padding:.55rem .85rem;
        border-radius:999px;
        border:1px solid var(--fc-border);
        color: var(--fc-ink);
        text-decoration:none;
        font-size:.88rem;
        letter-spacing:.02em;
        transition: all .14s ease;
    }
    .fc-qa-view svg{ width:16px; height:16px; transition: transform .14s ease; }
    .fc-qa-view:hover{
        border-color: var(--fc-border-strong);
        background: #fff;
        box-shadow: 0 10px 18px rgba(0,0,0,.04);
        transform: translateY(-1px);
        color: var(--fc-accent-dark);
    }
    .fc-qa-view:hover svg{ transform: translateX(2px); }
    .fc-qa-view:active{ transform: translateY(0); box-shadow:none; }
    .fc-qa-view:focus-visible{
        outline: 0;
        box-shadow: 0 0 0 .2rem rgba(17,17,17,.18);
    }
        /* Sidebar filters */
        .fc-sidebar{
            position: sticky;
            --fc-sidebar-top: 190px;
            top: var(--fc-sidebar-top);
            align-self: flex-start;
        }
        .fc-sidebar-inner{
            background:#fff;
            border: 1px solid var(--fc-border);
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 14px 34px rgba(0,0,0,.04);
            max-height: calc(100dvh - var(--fc-sidebar-top) - 16px);
            overflow: auto;
            overscroll-behavior-y: contain;
            -webkit-overflow-scrolling: touch;
            scrollbar-gutter: stable;
            touch-action: pan-y;
        }
        .fc-sidebar-inner::-webkit-scrollbar{ width: 10px; }
        .fc-sidebar-inner::-webkit-scrollbar-track{ background: transparent; }
        .fc-sidebar-inner::-webkit-scrollbar-thumb{
            background: rgba(17,17,17,.18);
            border-radius: 999px;
            border: 3px solid transparent;
            background-clip: content-box;
        }
        .fc-sidebar-inner:hover::-webkit-scrollbar-thumb{ background: rgba(17,17,17,.28); background-clip: content-box; }
        .fc-sidebar-reset{
            font-size: .86rem;
            text-decoration: none;
            border-bottom: 1px solid currentColor;
            padding-bottom: 1px;
            color: var(--fc-muted);
        }
        .fc-sidebar-reset:hover{ color: var(--fc-accent-dark); }
        .fc-filter-section{ padding: 14px 0; border-top: 1px solid var(--fc-border); }
        .fc-filter-section:first-of-type{ border-top: 0; padding-top: 0; }
        .fc-filter-title{
            font-size: .72rem;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--fc-muted);
            margin-bottom: 10px;
        }
        .fc-pill-row{ display:flex; flex-wrap:wrap; gap:8px; }
        .fc-pill{
            border: 1px solid var(--fc-border);
            background: var(--fc-soft-bg);
            border-radius: 999px;
            padding: .42rem .72rem;
            font-size: .9rem;
            line-height: 1;
            transition: all .12s ease;
            color: var(--fc-ink);
            min-height: 38px;
        }
        .fc-pill:hover{ border-color: var(--fc-border-strong); background:#fff; transform: translateY(-1px); }
        .fc-pill.is-active{ background: var(--fc-ink); color:#fff; border-color: var(--fc-ink); }
        .fc-swatch-row{ display:flex; flex-wrap:wrap; gap:10px; }
        .fc-swatch{
            width: 34px; height: 34px; border-radius: 999px;
            border: 1px solid var(--fc-border-strong); background:#fff;
            display:inline-flex; align-items:center; justify-content:center;
            transition: all .12s ease;
            color: var(--fc-ink);
        }
        .fc-swatch:hover{ transform: translateY(-1px); box-shadow:0 10px 18px rgba(0,0,0,.05); border-color: var(--fc-border-strong); }
        .fc-swatch.is-active{ border-color: var(--fc-ink); box-shadow:0 0 0 2px rgba(17,17,17,.12); }
        .fc-swatch-dot{
            width: 18px;
            height: 18px;
            border-radius: 999px;
            border: 1px solid rgba(0,0,0,.18);
            display:block;
        }
        .fc-swatch.is-active .fc-swatch-dot{ border-color: rgba(255,255,255,.55); }
        .fc-price-box .form-range{ accent-color: var(--fc-ink); }
        .fc-checklist{ display:flex; flex-direction:column; gap:10px; }
        .fc-check{ display:flex; align-items:center; gap:10px; font-size:.92rem; color: var(--fc-ink); }
        .fc-check input{ width: 16px; height: 16px; }
        @media (max-width: 991.98px){
            .fc-sidebar{ position: static; top:auto; }
            .fc-sidebar-inner{ max-height: none; overflow: visible; }
        }
    .fc-card-actions{ display:flex; align-items:center; justify-content:space-between; gap:10px; }
    /* (deduped styles) card swatches/quickadd are defined above for the new card system */
    .fc-card-img{ transition: opacity .12s ease; }
</style>
</body>
</html>
