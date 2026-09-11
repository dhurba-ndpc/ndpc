<?php

$dropdownMenus = [
    [
        'title' => 'Content Management',
        'icon' => 'fas fa-fw fa-info-circle',
        'id' => 'collapseContentManagement',
        'active' => false,
        'permissions' => false,
        'items' => [
            [
                'title' => 'Home Hero Banner',
                'route' => 'hero-banners.index',
                'permissions' => false,
            ],
            [
                'title' => 'About Us',
                'route' => 'about.index',
                'permissions' => false,
            ],
        ],
    ],
];

?>





<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    @foreach ($dropdownMenus as $menu)
        <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#{{ $menu['id'] }}"
                aria-expanded="true" aria-controls="{{ $menu['id'] }}">
                <i class="{{ $menu['icon']}}"></i>
                <span>{{ $menu['title'] }}</span>
            </a>
            <div id="{{ $menu['id'] }}" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Post Management:</h6>
                    @foreach ($menu['items'] as $item)
                        <a class="collapse-item" href="{{ route($item['route']) }}">{{ $item['title']}}</a>
                    @endforeach
                </div>
            </div>
        </li>
    @endforeach

    <!-- Nav Item - Charts -->
    <li class="nav-item">
        <a class="nav-link" href="charts.html">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Charts</span></a>
    </li>



    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>


</ul>
