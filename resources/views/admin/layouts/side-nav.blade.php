<aside class="sidebar-nav">
    <div class="d-flex align-items-center justify-content-center">
        <a class="navbar-brand" href="">
            <img class="full-imgbox" src="{{ asset('assets/admin/images/molofzo_logo.png') }}" width="100" alt="logo">
        </a>
    </div>

    <ul class="side-menu">

        @if (Auth::user()->role == 1)

            <!-- Dashboard -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.dashboard') active @endif">
                <a href="{{ route('dashboard.admin.dashboard') }}">
                    <span class="d-flex gap-3 align-items-end">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </span>
                </a>
            </li>

            <!-- Categories -->
            <li class="@if (Route::is('dashboard.admin.categories','dashboard.admin.categories.create')) active @endif">
                <a href="{{ route('dashboard.admin.categories') }}">
                    <i class="fas fa-folder"></i>
                    <span>Categories</span>
                </a>
            </li>

            <!-- Subcategories -->
            <li class="@if (Route::is('dashboard.admin.subcategories','dashboard.admin.subcategories.create')) active @endif">
                <a href="{{ route('dashboard.admin.subcategories') }}">
                    <i class="fas fa-layer-group"></i>
                    <span>Subcategories</span>
                </a>
            </li>

            <!-- Child Categories -->
            <li class="@if (Route::is('dashboard.admin.childcategories')) active @endif">
                <a href="{{ route('dashboard.admin.childcategories') }}">
                    <i class="fas fa-sitemap"></i>
                    <span>Child Categories</span>
                </a>
            </li>

            <!-- Attributes -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.attributes') active @endif">
                <a href="{{ route('dashboard.admin.attributes') }}">
                    <i class="fas fa-tags"></i>
                    <span>All Attributes</span>
                </a>
            </li>

            <!-- Attribute Requests -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.attribute.requests') active @endif">
                <a href="{{ route('dashboard.admin.attribute.requests') }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Attributes Requests</span>
                </a>
            </li>

            <!-- Cities -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.cities.index') active @endif">
                <a href="{{ route('dashboard.admin.cities.index') }}">
                    <i class="fas fa-city"></i>
                    <span>All Cities</span>
                </a>
            </li>

            <!-- Vendors -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.vendors') active @endif">
                <a href="{{ route('dashboard.admin.vendors') }}">
                    <i class="fas fa-store"></i>
                    <span>All Vendors</span>
                </a>
            </li>

            <!-- Customers -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.customers') active @endif">
                <a href="{{ route('dashboard.admin.customers') }}">
                    <i class="fas fa-users"></i>
                    <span>All Customers</span>
                </a>
            </li>

            <!-- Stores -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.stores') active @endif">
                <a href="{{ route('dashboard.admin.stores') }}">
                    <i class="fas fa-warehouse"></i>
                    <span>All Stores</span>
                </a>
            </li>

            <!-- Products -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.products') active @endif">
                <a href="{{ route('dashboard.admin.products') }}">
                    <i class="fas fa-box"></i>
                    <span>All Products</span>
                </a>
            </li>

            <!-- Banners -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.banners.index') active @endif">
                <a href="{{ route('dashboard.admin.banners.index') }}">
                    <i class="fas fa-image"></i>
                    <span>All Banners</span>
                </a>
            </li>

            <!-- Banks -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.banks.index') active @endif">
                <a href="{{ route('dashboard.admin.banks.index') }}">
                    <i class="fas fa-university"></i>
                    <span>All Banks</span>
                </a>
            </li>

            <!-- Orders -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.orders') active @endif">
                <a href="{{ route('dashboard.admin.orders') }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>All Orders</span>
                </a>
            </li>

            <!-- Notifications -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.notifications.index') active @endif">
                <a href="{{ route('dashboard.admin.notifications.index') }}">
                    <i class="fas fa-bell"></i>
                    <span>All Notifications</span>
                </a>
            </li>


            <!-- Banks -->
            <li class="@if (Route::currentRouteName() == 'dashboard.admin.banks.index') active @endif">
                <a href="{{ route('dashboard.admin.banks.index') }}">
                    <i class="fas fa-university"></i>
                    <span>All Banks</span>
                </a>
            </li>

            <!-- PROMOTION MODULE START -->

            <li class="@if (Route::is('dashboard.admin.packages.*')) active @endif">
                <a href="{{ route('dashboard.admin.packages.index') }}">
                    <i class="fas fa-gift"></i>
                    <span>Promotion Packages</span>
                </a>
            </li>

            <!-- <li class="@if (Route::currentRouteName() == 'dashboard.admin.promotion.requests') active @endif">
                <a href="{{ route('dashboard.admin.promotion.requests') }}">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Promotion Requests</span>
                </a>
            </li>

            <li class="@if (Route::currentRouteName() == 'dashboard.admin.payment.edit') active @endif">
                <a href="{{ route('dashboard.admin.payment.edit') }}">
                    <i class="fas fa-credit-card"></i>
                    <span>Payment Settings</span>
                </a>
            </li> -->

            <!-- PROMOTION MODULE END -->

        @endif


        
    </ul>
</aside>