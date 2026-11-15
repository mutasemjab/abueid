@php
    $admin = auth('admin')->user();

    $groups = [
        __('messages.main') => [
            ['route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'bi-speedometer2', 'label' => __('messages.dashboard'), 'can' => null],
        ],
        __('admin.nav.store') => [
            ['route' => 'admin.orders.index', 'active' => 'admin.orders.*', 'icon' => 'bi-receipt', 'label' => __('admin.orders.title'), 'can' => 'order-table'],
            ['route' => 'admin.products.index', 'active' => 'admin.products.*', 'icon' => 'bi-box-seam', 'label' => __('admin.products.title'), 'can' => 'product-table'],
            ['route' => 'admin.categories.index', 'active' => 'admin.categories.*', 'icon' => 'bi-grid', 'label' => __('admin.categories.title'), 'can' => 'category-table'],
            ['route' => 'admin.brands.index', 'active' => 'admin.brands.*', 'icon' => 'bi-award', 'label' => __('admin.brands.title'), 'can' => 'brand-table'],
            ['route' => 'admin.delivery-areas.index', 'active' => 'admin.delivery-areas.*', 'icon' => 'bi-truck', 'label' => __('admin.areas.title'), 'can' => 'area-table'],
        ],
        __('messages.site_content') => [
            ['route' => 'admin.sections.index', 'active' => 'admin.sections.*', 'icon' => 'bi-layout-text-window', 'label' => __('admin.sections.title'), 'can' => 'section-table'],
            ['route' => 'admin.branches.index', 'active' => 'admin.branches.*', 'icon' => 'bi-geo-alt', 'label' => __('admin.branches.title'), 'can' => 'branch-table'],
            ['route' => 'admin.settings.edit', 'active' => 'admin.settings.*', 'icon' => 'bi-sliders', 'label' => __('admin.settings_page.title'), 'can' => 'setting-edit'],
        ],
        __('messages.system') => [
            ['route' => 'admin.employee.index', 'active' => 'admin.employee.*', 'icon' => 'bi-people', 'label' => __('messages.employees'), 'can' => 'employee-table'],
            ['route' => 'admin.role.index', 'active' => 'admin.role.*', 'icon' => 'bi-shield-lock', 'label' => __('messages.roles_permissions'), 'can' => 'role-table'],
        ],
    ];
@endphp

<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-lightning-charge-fill"></i></div>
        <span class="brand-text">{{ __('messages.edu_platform') }}</span>
    </div>

    <nav class="sidebar-nav">
        @foreach ($groups as $label => $links)
            @php($visible = array_filter($links, fn ($link) => ! $link['can'] || $admin?->can($link['can'])))
            @continue(! $visible)

            <div class="nav-label">{{ $label }}</div>
            <ul>
                @foreach ($visible as $link)
                    <li class="nav-item">
                        <a href="{{ route($link['route']) }}" class="nav-link {{ request()->routeIs($link['active']) ? 'active' : '' }}">
                            <i class="nav-icon bi {{ $link['icon'] }}"></i>
                            <span>{{ $link['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endforeach
    </nav>

    {{-- Sidebar Footer --}}
    <div class="sidebar-footer">
        <ul>
            <li class="nav-item">
                <a href="{{ route('admin.login.edit', auth('admin')->id()) }}" class="nav-link">
                    <i class="nav-icon bi bi-gear"></i>
                    <span>{{ __('messages.my_profile') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link"
                   onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                    <i class="nav-icon bi bi-box-arrow-right"></i>
                    <span>{{ __('messages.sign_out') }}</span>
                </a>
            </li>
        </ul>
        <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="{{ __('messages.collapse_sidebar') }}">
            <i class="bi bi-arrow-bar-left"></i>
        </button>
    </div>

</aside>
