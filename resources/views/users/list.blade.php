{{-- Layout Utama --}}
@extends('layouts.app')

{{-- Title --}}
@section('title', 'Data User')
@section('pageTitle', 'Data User')

{{-- Breadcumbs --}}
@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-line fw-semibold fs-7 my-1">
        <li class="breadcrumb-item text-gray-600">
            <a href="{{ route('dashboard.index') }}" class="text-gray-600 text-hover-primary">Home</a>
        </li>
        <li class="breadcrumb-item text-gray-600">User</li>
    </ul>
@endsection

{{-- Content --}}
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" id="search-input" class="form-control form-control-solid w-250px ps-13"
                        placeholder="Cari User..." value="{{ request('search') }}" />
                </div>
            </div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" id="btn-add">
                        <i class="ki-duotone ki-plus fs-2"></i> Tambah User
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body py-4">
            <div id="table-container">
                @include('users.partials.table_data', ['users' => $users])
            </div>
        </div>
    </div>

    <div class="modal fade" id="kt_modal_user" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold" id="modal-title">Tambah User</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>

                <div class="modal-body mx-2 my-2">
                    <form id="kt_modal_form" class="form" action="#">
                        @csrf
                        <input type="hidden" name="_method" value="POST">
                        <input type="hidden" name="user_id" value="">

                        <div class="scroll-y me-n7 pe-7" data-kt-scroll="true"
                            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
                            data-kt-scroll-dependencies="#modal-title" data-kt-scroll-offset="300px">

                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control form-control-solid"
                                    placeholder="Contoh: John Doe" />
                                <div class="invalid-feedback" id="name_error"></div>
                            </div>

                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Username</label>
                                <input type="text" name="user_name" class="form-control form-control-solid"
                                    placeholder="Contoh: johndoe123" />
                                <div class="invalid-feedback" id="user_name_error"></div>
                            </div>

                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    <span class="required" id="password-required-label">Password</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Minimal 6 karakter"></i>
                                </label>

                                <div class="input-group input-group-solid">
                                    <input type="password" name="password" class="form-control form-control-solid"
                                        placeholder="********" autocomplete="new-password" />

                                    <span class="input-group-text cursor-pointer" id="toggle-password">
                                        <i class="ki-duotone ki-eye-slash fs-2">
                                            <span class="path1"></span><span class="path2"></span>
                                            <span class="path3"></span><span class="path4"></span>
                                        </i>
                                    </span>
                                </div>

                                <div class="text-muted fs-7 mt-1 d-none" id="password-hint">
                                    Kosongkan jika tidak ingin mengubah password.
                                </div>
                                <div class="invalid-feedback d-block" id="password_error"></div>
                            </div>

                        </div>

                        <div class="text-end pt-10">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btn_submit">
                                <span class="indicator-label">Simpan</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const CONFIG = {
            urls: {
                base: "{{ route('users.index') }}",
                store: "{{ route('users.store') }}",
            },
            selectors: {
                tableContainer: '#table-container',
                searchInput: '#search-input',
                modal: '#kt_modal_user',
                form: '#kt_modal_form',
                modalTitle: '#modal-title',
                submitBtn: '#btn_submit',
                addBtn: '#btn-add',
            },
            messages: {
                loading: '<div class="text-center p-10"><span class="spinner-border text-primary"></span> Loading...</div>',
                confirmDelete: "Apakah anda yakin ingin menghapus user ini?",
                titleDelete: "Hapus User?",
                success: "Berhasil disimpan",
                error: "Terjadi kesalahan sistem",
                loadFail: "Gagal memuat data."
            },
            timing: {
                searchDelay: 500
            }
        };

        function buildUrl(baseUrl) {
            const url = new URL(baseUrl || CONFIG.urls.base, window.location.origin);
            const search = $(CONFIG.selectors.searchInput).val();
            if (search) url.searchParams.set('search', search);
            else url.searchParams.delete('search');
            return url.href;
        }

        function fetchTableData(targetUrl = null) {
            const finalUrl = buildUrl(targetUrl);
            $(CONFIG.selectors.tableContainer).html(CONFIG.messages.loading);

            $.ajax({
                url: finalUrl,
                type: 'GET',
                success: (response) => {
                    $(CONFIG.selectors.tableContainer).html(response);
                    if (typeof KTMenu !== 'undefined') KTMenu.createInstances();
                },
                error: () => Swal.fire("Error", CONFIG.messages.loadFail, "error")
            });
        }

        function resetModalForm() {
            const formEl = $(CONFIG.selectors.form);
            formEl[0].reset();
            formEl.attr('action', CONFIG.urls.store);
            formEl.find('[name="_method"]').val('POST');
            formEl.find('[name="user_id"]').val('');

            $(CONFIG.selectors.modalTitle).text('Tambah User');
            $('#password-hint').addClass('d-none');
            $('#password-required-label').addClass('required');
            formEl.find('[name="password"]').attr('required', true);

            const passInput = formEl.find('[name="password"]');
            const passIcon = $('#toggle-password').find('i');

            passInput.attr('type', 'password');

            passIcon.removeClass('ki-eye').addClass('ki-eye-slash');

            clearFormErrors(formEl);
        }

        function loadEntityData(id) {
            const url = `${CONFIG.urls.base}/${id}`;
            const formEl = $(CONFIG.selectors.form);
            const mainModal = new bootstrap.Modal(document.querySelector(CONFIG.selectors.modal));

            resetModalForm();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: (data) => {
                    prepareFormForEdit(url, data);
                    mainModal.show();
                },
                error: () => Swal.fire("Error", "Gagal mengambil data.", "error")
            });
        }

        function prepareFormForEdit(url, data) {
            const formEl = $(CONFIG.selectors.form);
            $(CONFIG.selectors.modalTitle).text('Edit User');
            formEl.attr('action', url);
            formEl.find('[name="_method"]').val('PUT');

            formEl.find('[name="user_id"]').val(data.user_id);
            formEl.find('[name="name"]').val(data.name);
            formEl.find('[name="user_name"]').val(data.user_name);

            formEl.find('[name="password"]').val('');

            $('#password-hint').removeClass('d-none');
            $('#password-required-label').removeClass('required');
            formEl.find('[name="password"]').removeAttr('required');
        }

        function deleteEntity(id) {
            Swal.fire({
                title: CONFIG.messages.titleDelete,
                text: CONFIG.messages.confirmDelete,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: "btn btn-danger",
                    cancelButton: "btn btn-active-light"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `${CONFIG.urls.base}/${id}`,
                        type: 'POST',
                        data: {
                            _method: 'DELETE'
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: (response) => {
                            Swal.fire("Terhapus!", "Data berhasil dihapus.", "success");
                            fetchTableData();
                        },
                        error: () => Swal.fire("Gagal", "Gagal menghapus data.", "error")
                    });
                }
            });
        }

        function handleValidationErrors(errors) {
            if (errors) {
                Object.keys(errors).forEach(key => {
                    let cleanKey = key.split('.')[0];
                    const errorId = `#${cleanKey}_error`;
                    let $errorEl = $(errorId);
                    const input = $(`[name="${cleanKey}"]`);

                    if (input.length) {
                        input.addClass('is-invalid');
                        if ($errorEl.length) {
                            $errorEl.text(errors[key][0]).show();
                        }
                    }
                });
            }
        }

        function clearFormErrors(form) {
            const target = form || $(CONFIG.selectors.form);
            target.find('.is-invalid').removeClass('is-invalid');
            target.find('.invalid-feedback').hide().text('').removeClass('d-block');
        }

        function toggleButtonLoading(btn, isLoading) {
            if (isLoading) {
                btn.attr('data-kt-indicator', 'on').prop('disabled', true);
            } else {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
            }
        }

        function submitForm(e) {
            e.preventDefault();
            const formEl = $(this);
            const submitBtn = formEl.find(CONFIG.selectors.submitBtn);
            const formData = new FormData(this);
            const url = $(this).attr('action');

            clearFormErrors(formEl);
            toggleButtonLoading(submitBtn, true);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: (response) => {
                    const modalInstance = bootstrap.Modal.getInstance(document.querySelector(CONFIG.selectors
                        .modal));
                    if (modalInstance) modalInstance.hide();
                    Swal.fire("Berhasil", CONFIG.messages.success, "success");
                    fetchTableData();
                },
                error: (xhr) => {
                    if (xhr.status === 422) {
                        handleValidationErrors(xhr.responseJSON.errors);
                    } else {
                        Swal.fire("Gagal", CONFIG.messages.error, "error");
                    }
                },
                complete: () => toggleButtonLoading(submitBtn, false)
            });
        }

        $(document).ready(function() {
            let searchTimer;
            const modalEl = document.querySelector(CONFIG.selectors.modal);
            const formEl = $(CONFIG.selectors.form);
            const mainModal = new bootstrap.Modal(modalEl);

            $(CONFIG.selectors.searchInput).on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => fetchTableData(), CONFIG.timing.searchDelay);
            });

            $(document).on('click', `${CONFIG.selectors.tableContainer} .pagination a`, function(e) {
                e.preventDefault();
                const href = $(this).attr('href');
                if (href && href !== '#') fetchTableData(href);
            });

            $(document).on('click', CONFIG.selectors.addBtn, function() {
                resetModalForm();
                mainModal.show();
            });

            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();
                loadEntityData($(this).data('id'));
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                deleteEntity($(this).data('id'));
            });

            formEl.on('submit', submitForm);

            modalEl.addEventListener('hidden.bs.modal', function() {
                resetModalForm();
            });

            $(document).on('click', '#toggle-password', function() {
                const input = $('[name="password"]');
                const icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');

                    icon.removeClass('ki-eye-slash').addClass('ki-eye');
                } else {
                    input.attr('type', 'password');

                    icon.removeClass('ki-eye').addClass('ki-eye-slash');
                }
            });
        });
    </script>
@endpush
