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

            {{-- <div class="me-3">
                <a href="#" class="btn btn-icon btn-custom btn-active-color-primary" data-kt-menu-trigger="click"
                    data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                    <i class="ki-duotone ki-element-11 fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                </a>

                <div class="menu menu-sub menu-sub-dropdown py-7 px-7 overflow-hidden w-300px w-md-350px"
                    data-kt-menu="true">
                    <div>
                        <div class="d-flex flex-stack fw-semibold mb-4">
                            <span class="text-muted fs-6 me-2">Shorcut:</span>
                        </div>
                        <div class="scroll-y mh-200px mh-lg-325px">
                            <div class="d-flex align-items-center mb-5">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light">
                                        <i class="ki-duotone ki-laptop fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="#" class="fs-6 text-gray-800 text-hover-primary fw-semibold">SKPI
                                        Bulan Ini</a>
                                    <span class="fs-7 text-muted fw-semibold">#45789</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="d-flex align-items-center py-3 py-lg-0">

                <div class="me-3">
                    <a href="#" class="btn btn-icon btn-custom btn-active-color-primary"
                        data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <i class="ki-duotone ki-user fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-auto"
                        data-kt-menu="true">
                        <div class="menu-item px-3">
                            @php
                                $session = session('user_data');
                                $user = \App\Models\User::with(['role'])->find($session['user_id']);
                            @endphp

                            <div class="menu-content d-flex align-items-center px-3">
                                <div class="symbol symbol-50px me-5">
                                    @if (!empty($user->foto))
                                        <img alt="{{ $user->name }}" src="{{ asset('storage/' . $user->foto) }}"
                                            class="w-100" />
                                    @else
                                        <div class="symbol-label fs-3 bg-light-danger text-danger">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif

                                </div>

                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5">
                                        {{ $user->name ?? '-' }}
                                    </div>

                                    <span class="fw-semibold text-muted text-hover-primary fs-7">
                                        {{ $session['short_role_name'] ? ucwords(str_replace('-', ' ', $session['short_role_name'])) : '-' }}
                                        &nbsp;-&nbsp;
                                        {{ $user->nip ?: ($user->no_hp ?: '-') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <a href="{{ route('user.profile') }}" class="menu-link px-5">Profil</a>
                        </div>
                        <div class="menu-item px-5">
                            <a href="{{ route('user.profile') }}/#tab_cert" class="menu-link px-5">
                                <span class="menu-text">Sertifikat</span>
                                <span class="menu-badge">
                                    <span class="badge badge-light-primary badge-circle fw-bold fs-7">
                                        {{ Auth::user()->certificates()->count() }}
                                    </span>
                                </span>
                            </a>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                @csrf
                                <a href="#" class="menu-link px-5"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Sign Out
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
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
