<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - {{ config('store.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset(config('store.brand.favicon_path')) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --fc-admin-bg: #f5f7fb;
            --fc-admin-surface: #ffffff;
            --fc-admin-border: #e6e8ee;
            --fc-admin-dark: #111827;
            --fc-admin-muted: #6b7280;
            --fc-admin-accent: #2563eb;
        }
        body {
            font-family: 'Jost', sans-serif;
            background: var(--fc-admin-bg);
            color: var(--fc-admin-dark);
        }
        .fc-admin-topbar {
            background: linear-gradient(90deg, #0f172a, #1f2937);
            border-bottom: 1px solid #101827;
        }
        .fc-admin-brand {
            font-weight: 600;
            letter-spacing: .4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding-top: .15rem;
            padding-bottom: .15rem;
        }
        .fc-admin-brand-logo {
            height: 32px;
            width: auto;
            display: block;
        }
        .fc-admin-layout {
            min-height: calc(100vh - 58px);
        }
        .fc-admin-sidebar {
            background: var(--fc-admin-surface);
            border-right: 1px solid var(--fc-admin-border);
        }
        .sidebar-menu .list-group-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-menu .list-group-item i,
        .sidebar-menu .list-group-item svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            stroke-width: 1.9;
        }
        .fc-admin-sidebar .list-group-item {
            border: 0;
            border-radius: 8px;
            margin-bottom: .2rem;
            color: #1f2937;
            font-weight: 500;
        }
        .fc-admin-sidebar .list-group-item:hover,
        .fc-admin-sidebar .list-group-item:focus {
            background: #eef2ff;
            color: #1d4ed8;
        }
        .fc-admin-sidebar .list-group-item.active {
            background: #e0e7ff;
            color: #1d4ed8;
        }
        .fc-admin-sidebar .sidebar-section-label {
            padding: .85rem .75rem .35rem;
            font-size: .72rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--fc-admin-muted);
            font-weight: 600;
        }
        .fc-admin-sidebar .sidebar-section-label:first-child {
            padding-top: .25rem;
        }
        .fc-admin-content .card {
            border: 1px solid var(--fc-admin-border);
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        }
        .fc-admin-content .table {
            --bs-table-bg: transparent;
        }
        .fc-admin-content .btn-primary {
            background: var(--fc-admin-accent);
            border-color: var(--fc-admin-accent);
        }
        .fc-admin-content .btn-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }
        .fc-admin-content .badge {
            font-weight: 500;
        }

        /* Responsive admin layout */
        html, body {
            overflow-x: hidden;
        }
        .fc-admin-content {
            min-width: 0;
        }
        .fc-admin-content img {
            max-width: 100%;
            height: auto;
        }
        .fc-admin-mobile-toggle {
            border: 1px solid rgba(255, 255, 255, .28);
        }
        .fc-admin-mobile-toggle:focus {
            box-shadow: 0 0 0 .2rem rgba(255, 255, 255, .18);
        }
        /* Make common admin page title bars wrap on small screens */
        @media (max-width: 575.98px) {
            .fc-admin-content > .d-flex.justify-content-between.align-items-center.mb-3 {
                flex-wrap: wrap;
                gap: .5rem;
            }
            .fc-admin-content > .d-flex.justify-content-between.align-items-center.mb-3 > * {
                max-width: 100%;
            }
        }
        /* Branded loader (admin) */
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
            background: radial-gradient(1200px 600px at 50% 30%, rgba(245,247,251,.96), rgba(245,247,251,.84));
            backdrop-filter: blur(10px);
        }
        .fc-loader-stage{
            position: relative;
            width: 148px;
            height: 148px;
            display:flex;
            align-items:center;
            justify-content:center;
        }
        .fc-loader-glow{
            position:absolute;
            inset: -18px;
            border-radius: 999px;
            background:
                radial-gradient(circle at 50% 50%, rgba(37, 99, 235, .18), rgba(37, 99, 235, 0) 62%),
                radial-gradient(circle at 50% 60%, rgba(0,0,0,.08), rgba(0,0,0,0) 62%);
            animation: fcGlow 1.7s ease-in-out infinite;
        }
        .fc-loader-walk{
            position: relative;
            width: 96px;
            height: 96px;
            display:flex;
            align-items:center;
            justify-content:center;
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
        }
        .fc-loader-mark .fc-turtle-shell{ fill: var(--fc-admin-dark); }
        .fc-loader-mark .fc-turtle-head{ fill: var(--fc-admin-dark); opacity: .96; }
        .fc-loader-mark .fc-turtle-flipper{ fill: var(--fc-admin-dark); opacity: .96; }
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

        @media (prefers-reduced-motion: reduce){
            .fc-loader-glow, .fc-loader-mark, .fc-loader-dots span, .fc-loader-shadow { animation: none !important; }
        }
    </style>
</head>
<body>
@include('partials.loader')
<nav class="navbar navbar-expand-lg navbar-dark fc-admin-topbar">
    <div class="container-fluid">
        <a class="navbar-brand fc-admin-brand" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset(config('store.brand.logo_mark_path')) }}" alt="{{ config('store.brand.logo_alt') }}" class="fc-admin-brand-logo">
        </a>

        <button class="btn btn-outline-light btn-sm d-lg-none fc-admin-mobile-toggle"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#fcAdminSidebar"
                aria-controls="fcAdminSidebar"
                aria-label="Open menu">
            <span class="me-1">Menu</span>
            <span class="navbar-toggler-icon" style="width: 1.05em; height: 1.05em;"></span>
        </button>
        
        <form method="POST" action="{{ route('admin.logout') }}" class="ms-auto">
            @csrf
            <button class="btn btn-outline-light btn-sm" type="submit">Logout</button>
        </form>
    </div>
</nav>

<div class="container-fluid fc-admin-layout">
    <div class="row">
        <aside class="col-lg-2 fc-admin-sidebar min-vh-100 p-3 d-none d-lg-block">
            @include('admin.partials.sidebar-menu')
        </aside>

        <main class="col-lg-10 p-3 p-lg-4 fc-admin-content">
            @include('admin.partials.alerts')
            @yield('content')
        </main>
    </div>
</div>

<div class="offcanvas offcanvas-start fc-admin-sidebar" tabindex="-1" id="fcAdminSidebar" aria-labelledby="fcAdminSidebarLabel">
    <div class="offcanvas-header">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset(config('store.brand.logo_mark_path')) }}" alt="{{ config('store.brand.logo_alt') }}" style="height:28px;width:auto;">
            <h5 class="offcanvas-title mb-0" id="fcAdminSidebarLabel">Admin menu</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
        @include('admin.partials.sidebar-menu')
        <div class="mt-3 pt-3 border-top">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="btn btn-outline-dark w-100" type="submit">Logout</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
<script>
    (() => {
        const offcanvasEl = document.getElementById('fcAdminSidebar');
        if (!offcanvasEl) return;

        offcanvasEl.addEventListener('click', (e) => {
            const link = e.target?.closest?.('a');
            if (!link) return;
            if (link.getAttribute('href')?.startsWith('#')) return;

            const instance = window.bootstrap?.Offcanvas?.getInstance(offcanvasEl);
            instance?.hide();
        });
    })();
</script>
<script>
    (() => {
        const AUTO_DISMISS_MS = 5000;
        const alerts = Array.from(document.querySelectorAll('.alert'));
        if (!alerts.length) return;

        alerts.forEach((el) => {
            if (!document.body.contains(el)) return;

            setTimeout(() => {
                if (!document.body.contains(el)) return;

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

        const failsafe = setTimeout(hide, 6500);
        window.addEventListener('load', () => {
            clearTimeout(failsafe);
            setTimeout(hide, 120);
        }, { once: true });
    })();
</script>
</body>
</html>
