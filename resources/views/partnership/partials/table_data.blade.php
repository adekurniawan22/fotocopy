<div class="table-responsive">
    <table class="table align-middle table-row-dashed fs-6 gy-5">
        <thead>
            <tr class="text-start text-gray-800 fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-250px">Partnership</th>
                @if (session('user_data.role_id') == 1)
                    <th class="min-w-150px">Organisasi</th>
                @endif
                <th class="min-w-200px">Alamat</th>
                <th class="min-w-150px">Kontak</th>
                <th class="text-end min-w-100px">Aksi</th>
            </tr>
        </thead>
        <tbody class="fw-semibold text-gray-600">
            @forelse ($partnerships as $index => $item)
                <tr>
                    <td class="d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                            <div class="symbol-label fs-3 bg-light-primary text-primary">
                                {{ substr($item->partnership_name, 0, 1) }}
                            </div>
                        </div>

                        <div class="d-flex flex-column">
                            <span class="text-gray-800 mb-1 fw-bold">{{ $item->partnership_name }}</span>

                            <div class="text-muted fs-7 lh-sm">
                                <i class="ki-duotone ki-user fs-8 me-1"><span class="path1"></span><span
                                        class="path2"></span></i>
                                {{ $item->penanggung_jawab }}
                            </div>
                        </div>
                    </td>

                    @if (session('user_data.role_id') == 1)
                        <td>
                            {{ optional($item->organization)->organization_name }}
                        </td>
                    @endif

                    <td>
                        @if ($item->alamat)
                            <span class="text-gray-600 cursor-help" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="{{ $item->alamat }}">
                                {{ Str::limit($item->alamat, 60) }}
                            </span>
                        @else
                            <span class="text-gray-600">-</span>
                        @endif
                    </td>

                    <td>
                        @if ($item->no_hp)
                            {{ $item->no_hp }}
                        @endif
                    </td>

                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm"
                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Aksi
                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                            data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 btn-edit" data-id="{{ $item->partnership_id }}"
                                    data-bs-toggle="modal" data-bs-target="#kt_modal_partnership">
                                    <i class="ki-duotone ki-notepad-edit me-2 fs-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Edit
                                </a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 btn-delete text-danger"
                                    data-id="{{ $item->partnership_id }}">
                                    <i class="ki-duotone ki-trash me-2 fs-3 text-danger">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                    Hapus
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ session('user_data.role_id') == 1 ? 5 : 4 }}" class="text-center">Data tidak
                        ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-end mt-4">
    {{ $partnerships->links() }}
</div>
