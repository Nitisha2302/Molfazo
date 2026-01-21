<aside class="sidebar-nav">
    <div class="d-flex align-items-center justify-content-center">
        <a class="navbar-brand"
            href="">
            <img class="full-imgbox" src="{{ asset('assets/admin/images/qadampayk-dash.png') }}" width="200" alt="logo">
        </a>
    </div>
 
    <ul class="side-menu">

        <!-- Menu for role 1 -->
        @if (Auth::user()->role == 1)
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.dashboard') active @endif">
                <a href="{{ route('dashboard.admin.dashboard') }}">
                    <span class="d-flex gap-3 align-items-end">
                        <i class="fas fa-tachometer-alt icon-font-size"></i>
                        <span class="nav-content-menu">Dashboard</span>
                    </span>
                </a>
            </li>

            <li class="@if (Route::currentRouteName() == 'dashboard.admin.categories') active @endif">
                <a href="{{ route('dashboard.admin.categories') }}">
                    <span class="d-flex gap-3 align-items-end">
                         <i class="fas fa-th-large icon-font-size"></i>
                        <span class="nav-content-menu">Categeories</span>
                    </span>
                </a>
            </li>
            
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.subcategories') active @endif">
                <a href="{{ route('dashboard.admin.subcategories') }}">
                    <span class="d-flex gap-3 align-items-end">
                        <i class="fas fa-list-ul icon-font-size"></i>
                        <span class="nav-content-menu">SubCategeories</span>
                    </span>
                </a>
            </li>

            <li class="@if (Route::currentRouteName() == 'dashboard.admin.vendors') active @endif">
                <a href="{{ route('dashboard.admin.vendors') }}">
                    <span class="d-flex gap-3 align-items-end">
                        <i class="fas fa-store icon-font-size"></i>
                        <span class="nav-content-menu">All Vendors</span>
                    </span>
                </a>
            </li>

            <li class="@if (Route::currentRouteName() == 'dashboard.admin.stores') active @endif">
                <a href="{{ route('dashboard.admin.stores') }}">
                    <span class="d-flex gap-3 align-items-end">
                        <i class="fas fa-building icon-font-size"></i>
                        <span class="nav-content-menu">All Stores</span>
                    </span>
                </a>
            </li>

        @endif
    </ul>
</aside>

