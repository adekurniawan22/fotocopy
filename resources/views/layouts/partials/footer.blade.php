<div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
    <div class="container-fluid d-flex flex-column flex-md-row flex-stack">
        @php
            $session = session('user_data');
            $organization = \App\Models\Organization::find($session['organization_id']);
        @endphp

        <div class="text-gray-900 order-2 order-md-1 d-flex align-items-center flex-wrap">
            <span class="text-gray-500 fw-semibold me-1">Created by</span>
            <a href="mailto:ade.kurniawan216@gmail.com" class="text-gray-500 fw-semibold text-hover-primary">
                Ade Kurniawan
            </a>

            @if ($organization)
                <span class="text-gray-400 mx-2">•</span>
                <span class="text-gray-500 fw-semibold">
                    {{ $organization->organization_name }}
                </span>
            @endif
        </div>

        <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
            <li class="menu-item">
                <a href="#" target="_blank" class="menu-link px-2">
                    <i class="ki-duotone ki-message-text-2 fs-1 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </a>
            </li>
            <li class="menu-item">
                <a href="#" target="_blank" class="menu-link px-2">
                    <i class="ki-duotone ki-facebook fs-1 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </a>
            </li>
            <li class="menu-item">
                <a href="#" target="_blank" class="menu-link px-2">
                    <i class="ki-duotone ki-whatsapp fs-1 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </a>
            </li>
            <li class="menu-item">
                <a href="#" target="_blank" class="menu-link px-2">
                    <i class="ki-duotone ki-instagram fs-1 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </a>
            </li>
        </ul>
    </div>
</div>
