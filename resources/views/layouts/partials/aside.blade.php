<div id="kt_aside" class="aside" data-kt-drawer="true" data-kt-drawer-name="aside"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="auto"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_toggle">

    <div class="aside-logo flex-column-auto pt-10 pt-lg-20" id="kt_aside_logo">
        <img alt="Logo" src="{{ asset('assets/media/logos/logo-simpati-pdkb.png') }}" class="h-65px" />
    </div>

    <div class="aside-menu flex-column-fluid pt-0 pb-7 py-lg-10" id="kt_aside_menu">
        <div id="kt_aside_menu_wrapper" class="w-100 hover-scroll-y scroll-lg-ms d-flex" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: trur}" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer"
            data-kt-scroll-wrappers="#kt_aside, #kt_aside_menu" data-kt-scroll-offset="0">
            <div id="kt_aside_menu"
                class="menu menu-column menu-title-gray-600 menu-state-primary menu-state-icon-primary menu-state-bullet-primary menu-icon-gray-500 menu-arrow-gray-500 fw-semibold fs-6 my-auto"
                data-kt-menu="true">

                @php
                    $role = session('user_data.short_role_name');
                    $roleBaseUrl = url($role);

                    $segment1 = request()->segment(1);
                    $segment2 = request()->segment(2);

                    $dashboardLinks = ['dashboard'];
                    $manajemenEntitasLinks = ['organization', 'user', 'partnership', 'role'];
                    $kegiatanDokumenLinks = [
                        'work-plan',
                        'spki',
                        'laporan-pekerjaan',
                        'jsa',
                        'gardu-induk',
                        'jaringan',
                    ];
                    $gudangLinks = ['warehouse', 'tool', 'history-tool'];
                    $pusatInformasiLinks = ['ik-gardu-induk', 'ik-jaringan'];
                    $settingLinks = [
                        'setting-spki',
                        'setting-laporan-pekerjaan',
                        'setting-jsa',
                        'setting-history-tool',
                        'setting-method',
                        'setting-ews',
                    ];

                    $isDashboardActive = $segment1 == $role && in_array($segment2, $dashboardLinks);
                    $isManajemenEntitasActive = $segment1 == $role && in_array($segment2, $manajemenEntitasLinks);
                    $isKegiatanDokumenActive = $segment1 == $role && in_array($segment2, $kegiatanDokumenLinks);
                    $isGudangActive = $segment1 == $role && in_array($segment2, $gudangLinks);
                    $isPusatInformasiActive = $segment1 == $role && in_array($segment2, $pusatInformasiLinks);
                    $isSettingsActive = $segment1 == $role && in_array($segment2, $settingLinks);

                @endphp

                <div class="menu-item py-2 {{ $isDashboardActive ? 'here show' : '' }}">
                    <a href="{{ $roleBaseUrl }}/dashboard" class="menu-link menu-center" title="Dashboard"
                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
                        <span class="menu-icon me-0 {{ $isDashboardActive ? 'active' : '' }}">
                            <i class="ki-duotone ki-home fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </a>
                </div>

                @if (session('user_data.role_id') == 1 || session('user_data.role_id') == 2)
                    <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start"
                        class="menu-item py-2 {{ $isManajemenEntitasActive ? 'here show' : '' }}">

                        <span class="menu-link menu-center {{ $isManajemenEntitasActive ? 'active' : '' }}">
                            <span class="menu-icon me-0">
                                <i class="ki-duotone ki-profile-user fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                        </span>

                        <div class="menu-sub menu-sub-dropdown px-2 py-4 w-200px w-lg-225px mh-75 overflow-auto">
                            <div class="menu-item">
                                <div class="menu-content">
                                    <span class="menu-section fs-5 fw-bolder ps-1 py-1">Manajemen Entitas</span>
                                </div>
                            </div>

                            @if (session('user_data.role_id') == 1)
                                <div class="menu-item">
                                    <a class="menu-link {{ $segment2 == 'organization' ? 'active' : '' }}"
                                        href="{{ $roleBaseUrl }}/organization">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">Organisasi</span>
                                    </a>
                                </div>
                            @endif

                            <div class="menu-item">
                                <a class="menu-link {{ $segment2 == 'user' ? 'active' : '' }}"
                                    href="{{ $roleBaseUrl }}/user" title="Personil" data-bs-toggle="tooltip"
                                    data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Personil</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ $segment2 == 'partnership' ? 'active' : '' }}"
                                    href="{{ $roleBaseUrl }}/partnership" title="Partnership" data-bs-toggle="tooltip"
                                    data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Partnership</span>
                                </a>
                            </div>

                            @if (session('user_data.role_id') == 1)
                                <div class="menu-item">
                                    <a class="menu-link {{ $segment2 == 'role' ? 'active' : '' }}"
                                        href="{{ $roleBaseUrl }}/role" title="Jabatan" data-bs-toggle="tooltip"
                                        data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">Jabatan</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif


                <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start"
                    class="menu-item py-2 {{ $isKegiatanDokumenActive ? 'here show' : '' }}">

                    <span class="menu-link menu-center {{ $isKegiatanDokumenActive ? 'active' : '' }}">
                        <span class="menu-icon me-0">
                            <i class="ki-duotone ki-book-open fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </span>
                    </span>

                    <div class="menu-sub menu-sub-dropdown px-2 py-4 w-200px w-lg-225px mh-75 overflow-auto">
                        <div class="menu-item">
                            <div class="menu-content">
                                <span class="menu-section fs-5 fw-bolder ps-1 py-1">Kegiatan</span>
                            </div>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link {{ $segment2 == 'work-plan' ? 'active' : '' }}"
                                href="{{ $roleBaseUrl }}/work-plan" title="Rencana Kerja" data-bs-toggle="tooltip"
                                data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Rencana Kerja</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <div class="menu-content">
                                <span class="menu-section fs-5 fw-bolder ps-1 py-1">Manajemen Dokumen</span>
                            </div>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link {{ $segment2 == 'spki' ? 'active' : '' }}"
                                href="{{ $roleBaseUrl }}/spki" title="SPKI" data-bs-toggle="tooltip"
                                data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">SPKI</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link {{ $segment2 == 'laporan-pekerjaan' ? 'active' : '' }}"
                                href="{{ $roleBaseUrl }}/laporan-pekerjaan" title="Laporan Pekerjaan"
                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click"
                                data-bs-placement="right">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Laporan Pekerjaan</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link {{ $segment2 == 'jsa' ? 'active' : '' }}"
                                href="{{ $roleBaseUrl }}/jsa" title="JSA" data-bs-toggle="tooltip"
                                data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">JSA</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <div class="menu-content">
                                <span class="menu-section fs-5 fw-bolder ps-1 py-1">Anomali</span>
                            </div>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link {{ $segment2 == 'gardu-induk' ? 'active' : '' }}"
                                href="{{ $roleBaseUrl }}/gardu-induk" title="Gardu Induk" data-bs-toggle="tooltip"
                                data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Gardu Induk</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link {{ $segment2 == 'jaringan' ? 'active' : '' }}"
                                href="{{ $roleBaseUrl }}/jaringan" title="Jaringan" data-bs-toggle="tooltip"
                                data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Jaringan</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start"
                    class="menu-item py-2 {{ $isGudangActive ? 'here show' : '' }}">

                    <span class="menu-link menu-center {{ $isGudangActive ? 'active' : '' }}">
                        <span class="menu-icon me-0">
                            <i class="ki-duotone ki-parcel fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                        </span>
                    </span>

                    <div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
                        <div class="menu-item">
                            <a class="menu-link {{ $segment2 == 'warehouse' ? 'active' : '' }}"
                                href="{{ $roleBaseUrl }}/warehouse">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Gudang</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link {{ $segment2 == 'tool' ? 'active' : '' }}"
                                href="{{ $roleBaseUrl }}/tool">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Alat Kerja</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link {{ $segment2 == 'history-tool' ? 'active' : '' }}"
                                href="{{ $roleBaseUrl }}/history-tool">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Riwayat Gudang</span>
                            </a>
                        </div>
                    </div>
                </div>

                @if (session('user_data.role_id') == 1 || session('user_data.role_id') == 2)
                    <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start"
                        class="menu-item py-2 {{ $isSettingsActive ? 'here show' : '' }}">

                        <span class="menu-link menu-center {{ $isSettingsActive ? 'active' : '' }}">
                            <span class="menu-icon me-0">
                                <i class="ki-duotone ki-setting-2 fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                        </span>


                        <div class="menu-sub menu-sub-dropdown px-2 py-4 w-200px w-lg-225px mh-75 overflow-auto">
                            <div class="menu-item">
                                <div class="menu-content">
                                    <span class="menu-section fs-5 fw-bolder ps-1 py-1">Konfigurasi Dokumen</span>
                                </div>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ $segment2 == 'setting-spki' ? 'active' : '' }}"
                                    href="{{ $roleBaseUrl }}/setting-spki" title="Template PDF SPKI"
                                    data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click"
                                    data-bs-placement="right">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Template PDF SPKI</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ $segment2 == 'setting-laporan-pekerjaan' ? 'active' : '' }}"
                                    href="{{ $roleBaseUrl }}/setting-laporan-pekerjaan"
                                    title="Template PDF Laporan Pekerjaan" data-bs-toggle="tooltip"
                                    data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Template PDF Laporan Pekerjaan</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ $segment2 == 'setting-jsa' ? 'active' : '' }}"
                                    href="{{ $roleBaseUrl }}/setting-jsa" title="Template PDF JSA"
                                    data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click"
                                    data-bs-placement="right">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Template PDF JSA</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ $segment2 == 'setting-history-tool' ? 'active' : '' }}"
                                    href="{{ $roleBaseUrl }}/setting-history-tool"
                                    title="Template PDF Riwayat Gudang" data-bs-toggle="tooltip"
                                    data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Template PDF Riwayat Gudang</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <div class="menu-content">
                                    <span class="menu-section fs-5 fw-bolder ps-1 py-1">Konfigurasi Lainnya</span>
                                </div>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ $segment2 == 'setting-method' ? 'active' : '' }}"
                                    href="{{ $roleBaseUrl }}/setting-method" title="Metode Alat kerja"
                                    data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click"
                                    data-bs-placement="right">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Metode Alat kerja</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ $segment2 == 'setting-ews' ? 'active' : '' }}"
                                    href="{{ $roleBaseUrl }}/setting-ews" title="Kalkulasi EWS"
                                    data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click"
                                    data-bs-placement="right">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Kalkulasi EWS</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start"
                    class="menu-item py-2 {{ $isPusatInformasiActive ? 'here show' : '' }}">

                    <span class="menu-link menu-center {{ $isPusatInformasiActive ? 'active' : '' }}">
                        <span class="menu-icon me-0">
                            <i class="ki-duotone ki-message-question fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                    </span>

                    <div class="menu-sub menu-sub-dropdown px-2 py-4 w-200px w-lg-225px mh-75 overflow-auto">
                        <div class="menu-item">
                            <div class="menu-content">
                                <span class="menu-section fs-5 fw-bolder ps-1 py-1">Pusat Informasi</span>
                            </div>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link {{ $segment2 == 'ik-gardu-induk' ? 'active' : '' }}"
                                href="{{ $roleBaseUrl }}/ik-gardu-induk" title="Instruksi Kerja Gardu Induk"
                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click"
                                data-bs-placement="right">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Intruksi Kerja GI</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link {{ $segment2 == 'ik-jaringan' ? 'active' : '' }}"
                                href="{{ $roleBaseUrl }}/ik-jaringan" title="Instruksi Kerja Jaringan"
                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click"
                                data-bs-placement="right">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Intruksi Kerja Jaringan</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="aside-footer flex-column-auto pb-5 pb-lg-10" id="kt_aside_footer">
        <div class="d-flex flex-center w-100 scroll-px" data-bs-toggle="tooltip" data-bs-placement="right"
            data-bs-dismiss="click" title="Sign Out">
            <form method="POST" action="{{ route('logout') }}">
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
