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
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark fc-admin-topbar">
    <div class="container-fluid">
        <a class="navbar-brand fc-admin-brand" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset(config('store.brand.logo_mark_path')) }}" alt="{{ config('store.brand.logo_alt') }}" class="fc-admin-brand-logo">
        </a>
        
        <form method="POST" action="{{ route('admin.logout') }}" class="ms-auto">
            @csrf
            <button class="btn btn-outline-light btn-sm" type="submit">Logout</button>
        </form>
    </div>
</nav>

<div class="container-fluid fc-admin-layout">
    <div class="row">
        <aside class="col-lg-2 fc-admin-sidebar min-vh-100 p-3">
            <div class="list-group list-group-flush">
                <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action">Categories</a>
                <a href="{{ route('admin.product-types.index') }}" class="list-group-item list-group-item-action">Product Types</a>
                <a href="{{ route('admin.sizes.index') }}" class="list-group-item list-group-item-action">Sizes</a>
                <a href="{{ route('admin.colors.index') }}" class="list-group-item list-group-item-action">Colors</a>
                <a href="{{ route('admin.customers.index') }}" class="list-group-item list-group-item-action">Customers</a>
                <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action">Products</a>
                <a href="{{ route('admin.discounts.index') }}" class="list-group-item list-group-item-action">Discounts</a>
                <a href="{{ route('admin.coupons.index') }}" class="list-group-item list-group-item-action">Coupons</a>
                <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action">Orders</a>
                <a href="{{ route('admin.reports.orders.index') }}" class="list-group-item list-group-item-action">Reports</a>
                <a href="{{ route('admin.settings.edit') }}" class="list-group-item list-group-item-action">Settings</a>
            </div>
        </aside>
        <main class="col-lg-10 p-4 fc-admin-content">
            @include('admin.partials.alerts')
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
