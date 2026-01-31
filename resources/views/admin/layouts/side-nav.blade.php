<aside class="sidebar-nav">
    <div class="d-flex align-items-center justify-content-center">
        <a class="navbar-brand"
            href="">
            <img class="full-imgbox" src="{{ asset('assets/admin/images/molofzo_logo.png') }}" width="100" alt="logo">
        </a>
    </div>
 
    <ul class="side-menu">

        <!-- Menu for role 1 -->
        @if (Auth::user()->role == 1)
            <!-- Dashboard -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.dashboard') active @endif">
                <a href="{{ route('dashboard.admin.dashboard') }}">
                    <span class="d-flex gap-3 align-items-end">
                        <i class="fas fa-tachometer-alt icon-font-size"></i>
                        <span class="nav-content-menu">Dashboard</span>
                    </span>
                </a>
            </li>

            <!-- Categories -->
            <li class="@if (Route::is('dashboard.admin.categories','dashboard.admin.categories.create')) active @endif">
                <a href="{{ route('dashboard.admin.categories') }}">
                    <span class="d-flex gap-3 align-items-end">
                        <i class="fas fa-folder icon-font-size"></i>
                        <span class="nav-content-menu">Categories</span>
                    </span>
                </a>
            </li>

            <!-- Subcategories -->
            <li class="@if (Route::is('dashboard.admin.subcategories','dashboard.admin.subcategories.create')) active @endif">
                <a href="{{ route('dashboard.admin.subcategories') }}">
                    <span class="d-flex gap-3 align-items-end">
                        <i class="fas fa-layer-group icon-font-size"></i>
                        <span class="nav-content-menu">Subcategories</span>
                    </span>
                </a>
            </li>

            <!-- Child Categories -->
            <li class="@if (Route::is('dashboard.admin.childcategories','dashboard.admin.subcategories.createChildCategory')) active @endif">
                <a href="{{ route('dashboard.admin.childcategories') }}">
                    <span class="d-flex gap-3 align-items-end">
                        <i class="fas fa-sitemap icon-font-size"></i>
                        <span class="nav-content-menu">Child Categories</span>
                    </span>
                </a>
            </li>

            <!-- Attributes -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.attributes') active @endif">
                <a href="{{ route('dashboard.admin.attributes') }}">
                    <span class="d-flex gap-3 align-items-end">
                        <i class="fas fa-list-alt icon-font-size"></i>
                        <span class="nav-content-menu">All Attributes</span>
                    </span>
                </a>
            </li>

            <!-- Vendors -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.vendors') active @endif">
                <a href="{{ route('dashboard.admin.vendors') }}">
                    <span class="d-flex gap-3 align-items-end">
                        <i class="fas fa-store-alt icon-font-size"></i>
                        <span class="nav-content-menu">All Vendors</span>
                    </span>
                </a>
            </li>

            <!-- Stores -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.stores') active @endif">
                <a href="{{ route('dashboard.admin.stores') }}">
                    <span class="d-flex gap-3 align-items-end">
                        <i class="fas fa-building icon-font-size"></i>
                        <span class="nav-content-menu">All Stores</span>
                    </span>
                </a>
            </li>

            <li class="@if (Route::currentRouteName() == 'dashboard.admin.products') active @endif">
                <a href="{{ route('dashboard.admin.products') }}">
                    <span class="d-flex gap-3 align-items-end">
                      <i class="fas fa-box-open icon-font-size"></i>
                        <span class="nav-content-menu">All Products</span>
                    </span>
                </a>
            </li>

        @endif
    </ul>

</aside>

