<!--Header tablet and mobile-->
<div class="header-mobile py-3">
    <div class="container d-flex flex-stack">
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
            <a href="#">
                <img alt="Logo" src="{{ asset('assets/media/logos/logo-simpati-pdkb.png') }}" class="h-40px" />
            </a>
        </div>
        <button class="btn btn-icon btn-active-color-primary me-n4" id="kt_aside_toggle">
            <i class="ki-duotone ki-abstract-14 fs-2x">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </button>
    </div>
</div>

<div id="kt_header" class="header py-6 py-lg-0" data-kt-sticky="true" data-kt-sticky-name="header"
    data-kt-sticky-offset="{lg: '300px'}">
    <div class="header-container container-xxl">
        <div
            class="page-title d-flex flex-column align-items-start justify-content-center flex-wrap me-lg-20 py-3 py-lg-0 me-3">
            <h1 class="d-flex flex-column text-gray-900 fw-bold my-1">
                <span class="text-white fs-1 my-1">@yield('pageTitle', 'SIMPATI PDKB')</span>
                {{-- Breadcrumbs --}}
                @yield('breadcrumbs')
            </h1>
        </div>

        <div class="d-flex align-items-center flex-wrap">
            <div class="d-flex align-items-center py-3 py-lg-0">
                <div class="d-flex align-items-center me-0">
                    <a href="#" class="btn btn-icon btn-custom btn-active-color-primary"
                        data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent"
                        data-kt-menu-placement="bottom-end">
                        <i class="ki-duotone ki-night-day theme-light-show fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                            <span class="path6"></span>
                            <span class="path7"></span>
                            <span class="path8"></span>
                            <span class="path9"></span>
                            <span class="path10"></span>
                        </i>
                        <i class="ki-duotone ki-moon theme-dark-show fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                        data-kt-menu="true" data-kt-element="theme-mode-menu">
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-night-day fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                        <span class="path7"></span>
                                        <span class="path8"></span>
                                        <span class="path9"></span>
                                        <span class="path10"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Light</span>
                            </a>
                        </div>
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                                data-kt-value="dark">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-moon fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Dark</span>
                            </a>
                        </div>
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                                data-kt-value="system">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-screen fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                                <span class="menu-title">System</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-offset"></div>
</div>
