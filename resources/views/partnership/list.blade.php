{{-- Layout Utama --}}
@extends('layouts.app')

{{-- CSS --}}
@push('styles')
@endpush

{{-- Title --}}
@section('title', 'Partnership')

{{-- Page Title --}}
@section('pageTitle', 'Partnership')

{{-- Breadcumbs --}}
@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-line fw-semibold fs-7 my-1">
        <li class="breadcrumb-item text-gray-600">
            <a href="{{ route(session('user_data.short_role_name') . '.dashboard') }}"
                class="text-gray-600 text-hover-primary">Home</a>
        </li>
        <li class="breadcrumb-item text-gray-600">Partnership</li>
    </ul>
@endsection

{{-- Content --}}
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5"><span class="path1"></span><span
                            class="path2"></span></i>
                    <input type="text" id="search-input" class="form-control form-control-solid w-250px ps-13"
                        placeholder="Cari Nama, Alamat, PJ, No. HP..." value="{{ $keyword ?? '' }}" />
                </div>
            </div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <a href="#" id="btn-export" class="btn btn-light-success me-3">
                        <i class="ki-duotone ki-exit-up fs-2"><span class="path1"></span><span class="path2"></span></i>
                        Export Excel
                    </a>
                    <button type="button" class="btn btn-primary" id="btn-add">
                        <i class="ki-duotone ki-plus fs-2"></i> Tambah
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body py-4">
            <div id="table-container">
                @include('partnership.partials.table_data', ['partnerships' => $partnerships])
            </div>
        </div>
    </div>

    {{-- MODAL ADD/EDIT --}}
    <div class="modal fade" id="kt_modal_entity" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold" id="kt_modal_user_entity">Tambah Data</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body mx-2 my-2">
                    <form id="kt_modal_form" class="form" action="#">
                        @csrf
                        <input type="hidden" name="_method" value="POST">
                        <input type="hidden" name="partnership_id" value="">

                        <div class="scroll-y me-n7 pe-7" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                            data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_user_entity"
                            data-kt-scroll-offset="300px">

                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Nama Partnership</label>
                                <input type="text" name="partnership_name" class="form-control form-control-solid"
                                    placeholder="Nama Partnership" />
                                <div class="invalid-feedback" id="partnership_name_error"></div>
                            </div>

                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Penanggung Jawab</label>
                                <input type="text" name="penanggung_jawab" class="form-control form-control-solid"
                                    placeholder="Nama Penanggung Jawab" />
                                <div class="invalid-feedback" id="penanggung_jawab_error"></div>
                            </div>

                            @if (session('user_data.role_id') == 1)
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Organisasi</label>
                                    <select name="organization_id" class="form-select form-select-solid"
                                        data-control="select2" data-placeholder="Pilih Organisasi..."
                                        data-dropdown-parent="#kt_modal_entity">
                                        <option></option>
                                        @foreach ($organizations as $org)
                                            <option value="{{ $org->organization_id }}">{{ $org->organization_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="organization_id_error"></div>
                                </div>
                            @else
                                <input type="hidden" name="organization_id"
                                    value="{{ session('user_data.organization_id') }}">
                            @endif

                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">No. HP</label>
                                <input type="text" name="no_hp" class="form-control form-control-solid"
                                    placeholder="Nomor Handphone" />
                                <div class="invalid-feedback" id="no_hp_error"></div>
                            </div>

                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">Alamat</label>
                                <textarea name="alamat" class="form-control form-control-solid" rows="3" placeholder="Alamat Lengkap"></textarea>
                                <div class="invalid-feedback" id="alamat_error"></div>
                            </div>
                        </div>

                        <div class="text-end pt-10">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btn_submit">
                                <span class="indicator-label">Simpan</span>
                                <span class="indicator-progress">Please wait... <span
                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
                base: "{{ route(session('user_data.short_role_name') . '.partnership.index') }}",
                store: "{{ route(session('user_data.short_role_name') . '.partnership.store') }}",
                export: "{{ route(session('user_data.short_role_name') . '.partnership.export') }}",
            },

            selectors: {
                tableContainer: '#table-container',
                searchInput: '#search-input',
                exportBtn: '#btn-export',

                modal: '#kt_modal_entity',
                form: '#kt_modal_form',
                modalTitle: '#modal-title',
                submitBtn: '#btn_submit',
                addBtn: '#btn-add'
            },

            messages: {
                loading: '<div class="text-center p-10"><span class="spinner-border text-primary"></span> Loading...</div>',
                confirmDelete: "Apakah anda yakin ingin menghapus data ini?",
                titleDelete: "Hapus Data?",
                success: "Berhasil disimpan",
                error: "Terjadi kesalahan sistem"
            },

            timing: {
                searchDelay: 300
            }
        };

        function buildUrl(baseUrl) {
            const url = new URL(baseUrl || CONFIG.urls.base, window.location.origin);

            const keyword = $(CONFIG.selectors.searchInput).val();

            if (keyword) url.searchParams.set('keyword', keyword);
            else url.searchParams.delete('keyword');

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
                error: () => showNotification("Gagal memuat data.", "error")
            });
        }

        let searchTimer;
        const modalEl = document.querySelector(CONFIG.selectors.modal);
        const mainModal = new bootstrap.Modal(modalEl);
        const formEl = $(CONFIG.selectors.form);
        const submitBtn = formEl.find(CONFIG.selectors.submitBtn);

        function loadEntityData(id) {
            const url = `${CONFIG.urls.base}/${id}`;
            resetModalForm();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: (response) => {
                    prepareFormForEdit(url, response);
                    mainModal.show();
                },
                error: () => showNotification("Gagal mengambil data.", "error")
            });
        }

        function submitForm(e) {
            e.preventDefault();
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
                    mainModal.hide();
                    showNotification(response.success, 'success');
                    fetchTableData();
                },
                error: (xhr) => {
                    if (xhr.status === 422) {
                        handleValidationErrors(xhr.responseJSON.errors);
                    } else {
                        showNotification(CONFIG.messages.error, "error");
                    }
                },
                complete: () => toggleButtonLoading(submitBtn, false)
            });
        }

        function deleteEntity(id) {
            showConfirmation(CONFIG.messages.confirmDelete, "warning", CONFIG.messages.titleDelete)
                .then((result) => {
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
                                showNotification(response.success, 'success');
                                fetchTableData();
                            },
                            error: () => showNotification("Gagal menghapus data.", "error")
                        });
                    }
                });
        }

        function resetModalForm() {
            formEl[0].reset();
            formEl.attr('action', CONFIG.urls.store);
            formEl.find('[name="_method"]').val('POST');
            $(CONFIG.selectors.modalTitle).text('Tambah Partnership');

            formEl.find('[name="partnership_id"]').val('');

            const orgInput = formEl.find('[name="organization_id"]');
            if (orgInput.is('select')) {
                orgInput.val(null).trigger('change');
            } else {
                orgInput.val('{{ session('user_data.organization_id') }}');
            }

            clearFormErrors(formEl);
        }

        function prepareFormForEdit(url, data) {
            $(CONFIG.selectors.modalTitle).text('Edit Partnership');
            formEl.attr('action', url);
            formEl.find('[name="_method"]').val('PUT');

            formEl.find('[name="partnership_id"]').val(data.partnership_id);
            formEl.find('[name="partnership_name"]').val(data.partnership_name);
            formEl.find('[name="penanggung_jawab"]').val(data.penanggung_jawab);
            formEl.find('[name="no_hp"]').val(data.no_hp);
            formEl.find('[name="alamat"]').val(data.alamat);

            if (data.organization_id && formEl.find('[name="organization_id"]').is('select')) {
                formEl.find('[name="organization_id"]').val(data.organization_id).trigger('change');
            }
        }

        function handleValidationErrors(errors) {
            if (errors) {
                Object.keys(errors).forEach(key => {
                    const errorId = `#${key}_error`;
                    let $errorEl = $(errorId);

                    if ($errorEl.length === 0) {
                        const input = formEl.find(`[name="${key}"]`);
                        $errorEl = input.closest('div').find('.invalid-feedback');
                        input.addClass('is-invalid');
                    } else {
                        $errorEl.closest('div').find('.form-control, .form-select').addClass('is-invalid');
                    }

                    if ($errorEl.length) {
                        $errorEl.text(errors[key][0]).show();
                    }
                });
            }
        }

        function clearFormErrors(form) {
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').hide();
        }

        function toggleButtonLoading(btn, isLoading) {
            if (isLoading) {
                btn.attr('data-kt-indicator', 'on').prop('disabled', true);
            } else {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
            }
        }

        $(document).ready(function() {
            $(CONFIG.selectors.searchInput).on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => fetchTableData(), CONFIG.timing.searchDelay);
            });

            $(document).on('click', `${CONFIG.selectors.tableContainer} .pagination a`, function(e) {
                e.preventDefault();
                const href = $(this).attr('href');
                if (href && href !== '#') fetchTableData(href);
            });

            $(document).on('click', CONFIG.selectors.exportBtn, function(e) {
                e.preventDefault();
                showNotification('Sedang mendownload data...', 'info');
                window.location.href = buildUrl(CONFIG.urls.export);
            });

            $(document).on('click', CONFIG.selectors.addBtn, function() {
                resetModalForm();
                mainModal.show();
            });

            $(document).on('click', '.btn-edit', function() {
                loadEntityData($(this).data('id'));
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                deleteEntity($(this).data('id'));
            });

            formEl.on('submit', submitForm);
            modalEl.addEventListener('hidden.bs.modal', resetModalForm);
        });
    </script>
@endpush
