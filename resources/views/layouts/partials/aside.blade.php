<div id="kt_aside" class="aside" data-kt-drawer="true" data-kt-drawer-name="aside"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="auto"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_toggle">

    <div class="aside-logo flex-column-auto pt-10 pt-lg-20" id="kt_aside_logo">
        <a href="{{ route('dashboard.index') }}">
            <img alt="Logo" src="{{ asset('assets/media/logos/logo-simpati-pdkb.png') }}" class="h-65px" />
        </a>
    </div>

    <div class="aside-menu flex-column-fluid pt-0 pb-7 py-lg-10" id="kt_aside_menu">
        <div id="kt_aside_menu_wrapper" class="w-100 hover-scroll-y scroll-lg-ms d-flex" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer"
            data-kt-scroll-wrappers="#kt_aside, #kt_aside_menu" data-kt-scroll-offset="0">

            <div id="kt_aside_menu"
                class="menu menu-column menu-title-gray-600 menu-state-primary menu-state-icon-primary menu-state-bullet-primary menu-icon-gray-500 menu-arrow-gray-500 fw-semibold fs-6 my-auto"
                data-kt-menu="true">

                @php
                    $route = Route::currentRouteName();
                @endphp

                {{-- 1. MENU DASHBOARD --}}
                <div class="menu-item py-2 {{ Str::startsWith($route, 'dashboard') ? 'here show' : '' }}">
                    <a href="{{ route('dashboard.index') }}" class="menu-link menu-center" title="Dashboard"
                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
                        <span class="menu-icon me-0 {{ Str::startsWith($route, 'dashboard') ? 'active' : '' }}">
                            <i class="ki-duotone ki-home fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </a>
                </div>

                {{-- 4. MENU USER (HANYA MUNCUL JIKA USER ID == 1) --}}
                @if (Auth::id() == 1)
                    <div class="menu-item py-2 {{ Str::startsWith($route, 'users') ? 'here show' : '' }}">
                        {{-- Pastikan route 'users.index' sudah dibuat di web.php --}}
                        <a href="{{ route('users.index') }}" class="menu-link menu-center" title="Manajemen User"
                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
                            <span class="menu-icon me-0 {{ Str::startsWith($route, 'users') ? 'active' : '' }}">
                                {{-- Icon User --}}
                                <i class="ki-duotone ki-user fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                        </a>
                    </div>
                @endif

                {{-- 2. MENU ITEM (BARANG) --}}
                <div class="menu-item py-2 {{ Str::startsWith($route, 'items') ? 'here show' : '' }}">
                    <a href="{{ route('items.index') }}" class="menu-link menu-center" title="Data Barang"
                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
                        <span class="menu-icon me-0 {{ Str::startsWith($route, 'items') ? 'active' : '' }}">
                            <i class="ki-duotone ki-parcel fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                        </span>
                    </a>
                </div>

                {{-- 3. MENU OPTION (PENGATURAN) --}}
                <div class="menu-item py-2 {{ Str::startsWith($route, 'options') ? 'here show' : '' }}">
                    <a href="{{ route('options.index') }}" class="menu-link menu-center" title="Pengaturan"
                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
                        <span class="menu-icon me-0 {{ Str::startsWith($route, 'options') ? 'active' : '' }}">
                            <i class="ki-duotone ki-setting-2 fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </a>
                </div>

                <div class="menu-item py-2">
                    <a href="{{ url('/') }}" class="menu-link menu-center" title="Lihat Katalog Barang"
                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
                        <span class="menu-icon me-0">
                            <i class="ki-duotone ki-shop fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="aside-footer flex-column-auto pb-5 pb-lg-10" id="kt_aside_footer">
        <div class="d-flex flex-center w-100 scroll-px" data-bs-toggle="tooltip" data-bs-placement="right"
            data-bs-dismiss="click" title="Log Out">
            <form method="GET" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-custom">
                    <i class="ki-duotone ki-entrance-left fs-2x">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </button>
            </form>
        </div>
    </div>
</div>
