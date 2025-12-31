<div class="card-body py-4">
    <div class="table-responsive">
        <table class="table align-middle table-hover table-row-dashed fs-6 gy-5 gs-7" id="kt_table_warehouses">
            <thead>
                <tr class="text-start text-gray-800 fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-125px">Nama</th>
                    <th class="min-w-125px">Jumlah</th>

                    @if (Auth::user()->role_id == 1)
                        <th class="min-w-125px">Organisasi</th>
                    @endif

                    <th class="min-w-125px">Status Aktif</th>
                    <th class="text-end min-w-100px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold px-3">
                @forelse ($warehouses as $warehouse)
                    <tr>
                        <td>
                            <span class="text-gray-800 fw-bold">{{ $warehouse->warehouse_name }}</span>
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <span class="">{{ $warehouse->tool_count }} Alat</span>
                                <button type="button" class="btn btn-icon btn-sm btn-light-primary ms-2 btn-view-tools"
                                    data-id="{{ $warehouse->warehouse_id }}"
                                    data-name="{{ $warehouse->warehouse_name }}" data-bs-toggle="tooltip"
                                    data-bs-placement="right" title="Lihat Alat">
                                    <i class="ki-duotone ki-eye fs-3">
                                        <span class="path1"></span><span class="path2"></span><span
                                            class="path3"></span>
                                    </i>
                                </button>
                            </div>
                        </td>

                        @if (Auth::user()->role_id == 1)
                            <td>{{ $warehouse->organization->organization_name ?? '-' }}</td>
                        @endif

                        <td>
                            <div class="form-check form-switch form-check-custom form-check-solid form-check-success">
                                <input class="form-check-input warehouse-status-switch" type="checkbox" value="1"
                                    id="status_switch_{{ $warehouse->warehouse_id }}"
                                    data-id="{{ $warehouse->warehouse_id }}" @checked($warehouse->is_active) />
                                <label class="form-check-label"
                                    for="status_switch_{{ $warehouse->warehouse_id }}"></label>
                            </div>
                        </td>

                        <td class="text-end">
                            <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm"
                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Aksi
                                <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 btn-edit"
                                        data-id="{{ $warehouse->warehouse_id }}" data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_warehouse">
                                        <i class="ki-duotone ki-notepad-edit me-2 fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Edit
                                    </a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 btn-delete text-danger"
                                        data-id="{{ $warehouse->warehouse_id }}">
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
                        <td colspan="{{ Auth::user()->role_id == 1 ? 5 : 4 }}" class="text-center">Data tidak
                            ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $warehouses->links() }}
</div>
