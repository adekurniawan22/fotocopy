<div class="table-responsive">
    <table class="table align-middle table-row-dashed fs-6 gy-5">
        <thead>
            <tr class="text-start text-gray-800 fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-250px">User</th>
                <th class="min-w-150px">Username</th>
                <th class="text-end min-w-100px">Aksi</th>
            </tr>
        </thead>
        <tbody class="fw-semibold text-gray-600">
            @forelse ($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            {{-- Avatar Inisial --}}
                            <div class="symbol symbol-50px me-3">
                                <div class="symbol-label fs-2 fw-semibold bg-light-primary text-primary">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 mb-1 fw-bold">
                                    {{ $user->name }}
                                </span>
                                <div class="text-muted fs-7 lh-sm">
                                    User ID: {{ $user->user_id }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <span class="text-gray-800 fw-bold">
                            {{ $user->user_name }}
                        </span>
                    </td>

                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm"
                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi <i class="ki-duotone ki-down fs-5 ms-1"></i>
                        </a>

                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                            data-kt-menu="true">

                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 btn-edit" data-id="{{ $user->user_id }}">
                                    <i class="ki-duotone ki-notepad-edit me-2 fs-3">
                                        <span class="path1"></span><span class="path2"></span>
                                    </i>
                                    Edit
                                </a>
                            </div>

                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 btn-delete text-danger"
                                    data-id="{{ $user->user_id }}">
                                    <i class="ki-duotone ki-trash me-2 fs-3 text-danger">
                                        <span class="path1"></span><span class="path2"></span>
                                        <span class="path3"></span><span class="path4"></span>
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
                    <td colspan="3" class="text-center p-5">
                        <div class="d-flex flex-column align-items-center">
                            <i class="ki-duotone ki-user fs-3x mb-2 text-muted">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            <span class="text-muted fw-bold">Data User tidak ditemukan.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $users->links() }}
