<div class="card-body py-4">
    <div class="table-responsive">
        <table class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-150px">Nama Metode</th>
                    <th class="min-w-150px">Detail Alat</th>
                    @if (Auth::user()->role_id == 1)
                        <th class="min-w-100px">Organisasi</th>
                    @endif
                    <th class="text-end min-w-100px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
                @forelse ($methods as $index => $method)
                    <tr>
                        <td>
                            <span class="text-gray-800 fw-bold">{{ $method->nama_method }}</span>
                        </td>
                        <td>
                            @if ($method->tools->isNotEmpty())
                                @php
                                    $limit = 4;
                                    $count = $method->tools->count();
                                    $remaining = $count - $limit;
                                @endphp

                                @foreach ($method->tools as $key => $tool)
                                    <span
                                        class="badge badge-light-primary m-1 border border-primary {{ $key >= $limit ? 'd-none tool-item-' . $method->method_id : '' }}">
                                        {{ $tool->nama }}
                                        <span class="fw-bold ms-1">({{ $tool->qty_requirement }})</span>
                                    </span>
                                @endforeach

                                @if ($count > $limit)
                                    <a href="javascript:;"
                                        class="badge badge-light-primary border border-primary text-primary fs-8 fw-bold cursor-pointer btn-toggle-tools m-1"
                                        data-id="{{ $method->method_id }}" data-remaining="{{ $remaining }}">
                                        +{{ $remaining }} Lainnya
                                    </a>
                                @endif
                            @else
                                <span class="text-muted fs-7 fst-italic">Tidak ada alat</span>
                            @endif
                        </td>
                        @if (Auth::user()->role_id == 1)
                            <td>{{ $method->organization->organization_name ?? '-' }}</td>
                        @endif
                        <td class="text-end">
                            <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm"
                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Aksi
                                <i class="ki-duotone ki-down fs-5 ms-1"></i></a>

                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 btn-edit"
                                        data-id="{{ $method->method_id }}">
                                        <i class="ki-duotone ki-notepad-edit me-2 fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Edit
                                    </a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 btn-delete text-danger"
                                        data-id="{{ $method->method_id }}">
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
                        <td colspan="{{ session('user_data.role_id') == 1 ? 4 : 3 }}" class="text-center">Data tidak
                            ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-end mt-3">
    {{ $methods->links() }}
</div>
