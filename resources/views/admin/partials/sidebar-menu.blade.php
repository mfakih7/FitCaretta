<div class="list-group list-group-flush sidebar-menu">
    <div class="sidebar-section-label">Catalog</div>
    <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        <i data-lucide="shopping-bag"></i><span>Products</span>
    </a>
    <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
        <i data-lucide="layers"></i><span>Categories</span>
    </a>
    <a href="{{ route('admin.product-types.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.product-types.*') ? 'active' : '' }}">
        <i data-lucide="box"></i><span>Product Types</span>
    </a>
    <a href="{{ route('admin.sizes.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.sizes.*') ? 'active' : '' }}">
        <i data-lucide="ruler"></i><span>Sizes</span>
    </a>
    <a href="{{ route('admin.colors.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.colors.*') ? 'active' : '' }}">
        <i data-lucide="palette"></i><span>Colors</span>
    </a>

    <div class="sidebar-section-label">Sales</div>
    <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        <i data-lucide="receipt"></i><span>Orders</span>
    </a>
    <a href="{{ route('admin.discounts.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.discounts.*') ? 'active' : '' }}">
        <i data-lucide="percent"></i><span>Discounts</span>
    </a>
    <a href="{{ route('admin.coupons.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
        <i data-lucide="ticket"></i><span>Coupons</span>
    </a>

    <div class="sidebar-section-label">Customers</div>
    <a href="{{ route('admin.customers.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
        <i data-lucide="users"></i><span>Customers</span>
    </a>

    <div class="sidebar-section-label">Analytics</div>
    <a href="{{ route('admin.homepage-slides.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.homepage-slides.*') ? 'active' : '' }}">
        <i data-lucide="sliders-horizontal"></i><span>Homepage Slides</span>
    </a>
    <a href="{{ route('admin.reports.orders.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        <i data-lucide="bar-chart-3"></i><span>Reports</span>
    </a>

    <div class="sidebar-section-label">System</div>
    <a href="{{ route('admin.about.edit') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.about.*') ? 'active' : '' }}">
        <i data-lucide="info"></i><span>About</span>
    </a>
    <a href="{{ route('admin.feedback.settings.edit') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
        <i data-lucide="message-square"></i><span>Feedback</span>
    </a>
    <a href="{{ route('admin.settings.edit') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <i data-lucide="settings"></i><span>Settings</span>
    </a>
    <a href="{{ route('admin.profile.edit') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
        <i data-lucide="user"></i><span>Profile</span>
    </a>
</div>
