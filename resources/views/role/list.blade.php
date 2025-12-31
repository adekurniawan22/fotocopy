{{-- Layout Utama --}}
@extends('layouts.app')

{{-- CSS --}}
@push('styles')
@endpush

{{-- Title --}}
@section('title', 'Jabatan')

{{-- Page Title --}}
@section('pageTitle', 'Jabatan')

{{-- Breadcumbs --}}
@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-line fw-semibold fs-7 my-1">
        <li class="breadcrumb-item text-gray-600">
            <a href="{{ route(session('user_data.short_role_name') . '.dashboard') }}"
                class="text-gray-600 text-hover-primary">Home</a>
        </li>
        <li class="breadcrumb-item text-gray-600">Jabatan</li>
    </ul>
@endsection

{{-- Content --}}
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-hover table-row-dashed fs-6 gy-5 gs-7" id="kt_table_roles">
                    <thead>
                        <tr class="text-start text-gray-800 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-125px">Jabatan</th>
                            <th class="min-w-125px">Jumlah User</th>
                            <th class="text-end min-w-100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold px-3">
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $role->role_name }}</td>
                                <td>{{ $role->users_count }} user</td>
                                <td class="text-end">
                                    <a href="#"
                                        class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm"
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Aksi
                                        <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                        data-kt-menu="true">
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3 btn-edit" data-id="{{ $role->role_id }}"
                                                data-bs-toggle="modal" data-bs-target="#kt_modal_role">
                                                <i class="ki-duotone ki-notepad-edit me-2 fs-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Edit
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Data tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="kt_modal_role" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_role_header">
                    <h2 class="fw-bold">Edit Jabatan</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>

                <div class="modal-body mx-2 my-2">
                    <form id="kt_modal_role_form" class="form" method="POST" action="#">
                        @csrf
                        <input type="hidden" name="_method" value="POST">

                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Nama Role</label>
                            <input type="text" name="role_name" class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="Nama Role" />
                            <div class="fv-plugins-message-container invalid-feedback" id="role_name_error"></div>
                        </div>

                        <div class="text-end pt-10">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="kt_modal_role_submit"
                                data-kt-users-modal-action="submit">
                                <span class="indicator-label">Simpan</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Javascript --}}
@push('scripts')
    <script>
        const CONFIG = {
            urls: {
                base: "{{ route(session('user_data.short_role_name') . '.role.index') }}",
            },
            selectors: {
                modal: '#kt_modal_role',
                form: '#kt_modal_role_form',
                submitButton: '#kt_modal_role_submit',
            },
        };

        const modalEl = document.getElementById('kt_modal_role');
        const entityModal = new bootstrap.Modal(modalEl);
        const formEl = $(CONFIG.selectors.form);
        const submitButtonEl = formEl.find(CONFIG.selectors.submitButton);

        function resetModalForm() {
            formEl[0].reset();
            formEl.find('[name="role_name"]').val('');
            formEl.attr('action', '#');
            formEl.find('[name="_method"]').val('POST');
            clearFormErrors(formEl);
        }

        function loadroleData(id) {
            const getUrl = `${CONFIG.urls.base}/${id}`;
            const updateUrl = getUrl;

            resetModalForm();

            $.ajax({
                url: getUrl,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const role = response.role || response;

                    formEl.attr('action', updateUrl);
                    formEl.find('[name="_method"]').val('PUT');
                    formEl.find('[name="role_name"]').val(role.role_name);

                    entityModal.show();
                },
                error: function() {
                    showNotification("Gagal memuat data jabatan.", "error");
                }
            });
        }

        function submitForm(e) {
            e.preventDefault();

            const form = $(this);
            const url = form.attr('action');
            const formData = new FormData(this);

            const newName = formData.get('role_name');
            const id = url.split('/').pop();

            clearFormErrors(formEl);
            toggleButtonLoading(submitButtonEl, true);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    entityModal.hide();
                    showNotification(response.success, 'success');

                    const editButton = $(`.btn-edit[data-id="${id}"]`);
                    const row = editButton.closest('tr');
                    row.find('td:first').text(newName);
                    editButton.attr('data-name', newName);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        displayFormErrors(formEl, xhr.responseJSON.errors);
                    } else {
                        showNotification("Terjadi kesalahan.", "error");
                    }
                },
                complete: function() {
                    toggleButtonLoading(submitButtonEl, false);
                }
            });
        }

        $(document).on('click', '.btn-edit', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            loadroleData(id);
        });

        formEl.on('submit', submitForm);

        modalEl.addEventListener('hidden.bs.modal', function() {
            resetModalForm();
        });
    </script>
@endpush
