{{-- Layout Utama --}}
@extends('layouts.app')

{{-- CSS --}}
@push('styles')
@endpush

{{-- Title --}}
@section('title', 'Gudang')

{{-- Page Title --}}
@section('pageTitle', 'Gudang')

{{-- Breadcumbs --}}
@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-line fw-semibold fs-7 my-1">
        <li class="breadcrumb-item text-gray-600">
            <a href="{{ route(session('user_data.short_role_name') . '.dashboard') }}"
                class="text-gray-600 text-hover-primary">Home</a>
        </li>
        <li class="breadcrumb-item text-gray-600">Gudang</li>
    </ul>
@endsection

{{-- Content --}}
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <input type="text" name="keyword" id="search-input"
                        class="form-control form-control-solid w-250px ps-13" placeholder="Cari Gudang..."
                        value="{{ $keyword ?? '' }}" />
                </div>
            </div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <a href="{{ route(session('user_data.short_role_name') . '.warehouse.export') }}"
                        class="btn btn-light-success me-3" id="btn-export-warehouse">
                        <i class="ki-duotone ki-exit-up fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>Export Excel
                    </a>

                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_warehouse" id="btn-add-warehouse">
                        <i class="ki-duotone ki-plus fs-2"></i> Gudang
                    </button>
                </div>
            </div>
        </div>

        <div id="warehouse-table-container">
            @include('warehouse.partials.table_data', ['warehouses' => $warehouses])
        </div>
    </div>

    {{-- Modal Tambah/Edit --}}
    <div class="modal fade" id="kt_modal_warehouse" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_warehouse_header">
                    <h2 class="fw-bold">Tambah Gudang</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>

                <div class="modal-body mx-2 my-2">
                    <form id="kt_modal_warehouse_form" class="form"
                        action="{{ route(session('user_data.short_role_name') . '.warehouse.store') }}">
                        @csrf
                        <input type="hidden" name="_method" value="POST">

                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Nama Gudang</label>
                            <input type="text" name="warehouse_name" class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="Nama Gudang" />
                            <div class="fv-plugins-message-container invalid-feedback" id="warehouse_name_error"></div>
                        </div>

                        @if (Auth::user()->role_id == 1)
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Organisasi</label>
                                <select name="organization_id" class="form-select form-select-solid" data-control="select2"
                                    data-placeholder="Pilih Organisasi..." data-dropdown-parent="#kt_modal_warehouse">
                                    <option></option>
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->organization_id }}">
                                            {{ $org->organization_name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="organization_id_error">
                                </div>
                            </div>
                        @endif

                        <div class="col-md-12 fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">Status</label>
                            <div class="form-check form-switch form-check-custom form-check-solid form-check-success">
                                <input class="form-check-input" type="checkbox" value="1" id="modal_is_active"
                                    name="is_active" checked />
                                <label class="form-check-label" for="modal_is_active">
                                    Aktif
                                </label>
                            </div>
                        </div>

                        <div class="text-end pt-10">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="kt_modal_warehouse_submit"
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

    {{-- Modal View Tools --}}
    <div class="modal fade" id="kt_modal_view_tools" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-700px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_tools_list_header">
                    <h2 class="fw-bold">Daftar Alat</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>

                <div class="modal-body scroll-y mx-2 my-2" id="kt_modal_tools_list_scroll" data-kt-scroll="true"
                    data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
                    data-kt-scroll-dependencies="#kt_modal_tools_list_header"
                    data-kt-scroll-wrappers="#kt_modal_tools_list_scroll" data-kt-scroll-offset="300px">
                    <div id="modal-tools-container">
                        <div class="text-center p-10">
                            <span class="spinner-border text-primary"></span> Loading...
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
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
                base: "{{ route(session('user_data.short_role_name') . '.warehouse.index') }}",
                store: "{{ route(session('user_data.short_role_name') . '.warehouse.store') }}",
                updateStatus: "{{ url(session('user_data.short_role_name') . '/warehouse') }}",
                export: "{{ route(session('user_data.short_role_name') . '.warehouse.export') }}",
                toolsBase: "{{ url(session('user_data.short_role_name') . '/warehouse') }}"
            },
            selectors: {
                searchInput: '#search-input',
                tableContainer: '#warehouse-table-container',
                modal: '#kt_modal_warehouse',
                modalHeader: '#kt_modal_warehouse_header h2',
                form: '#kt_modal_warehouse_form',
                addButton: '#btn-add-warehouse',
                submitButton: '#kt_modal_warehouse_submit',
                exportBtn: '#btn-export-warehouse',
                toolsModal: '#kt_modal_view_tools',
                toolsContainer: '#modal-tools-container',
                toolsTitle: '#modal-warehouse-name'
            },
            messages: {
                loading: '<div class="text-center p-10"><span class="spinner-border text-primary"></span> Loading...</div>',
                errorLoad: 'Gagal memuat data.',
                confirmDelete: "Apakah anda yakin ingin menghapus data ini?",
                titleDelete: "Hapus Gudang?",
                success: "Berhasil disimpan",
                error: "Terjadi kesalahan sistem"
            },
            timing: {
                searchDelay: 300,
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
            const container = $(CONFIG.selectors.tableContainer);

            container.html(CONFIG.messages.loading);

            $.ajax({
                url: finalUrl,
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    container.html(response);
                    if (typeof KTMenu !== 'undefined') KTMenu.createInstances();
                },
                error: function() {
                    container.html('<div class="text-center text-danger p-10">Gagal memuat data.</div>');
                    showNotification(CONFIG.messages.errorLoad, "error");
                }
            });
        }

        let searchTimer;
        const formEl = $(CONFIG.selectors.form);
        const submitButtonEl = formEl.find(CONFIG.selectors.submitButton);
        const modalEl = document.querySelector(CONFIG.selectors.modal);
        const toolsModalEl = document.querySelector(CONFIG.selectors.toolsModal);

        const entityModal = modalEl ? new bootstrap.Modal(modalEl) : null;
        const toolsModal = toolsModalEl ? new bootstrap.Modal(toolsModalEl) : null;

        function loadWarehouseData(id) {
            const getUrl = `${CONFIG.urls.base}/${id}`;
            resetModalForm();

            $.ajax({
                url: getUrl,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const warehouse = response.warehouse || response;

                    $(CONFIG.selectors.modalHeader).text('Edit Gudang');
                    formEl.attr('action', getUrl);
                    formEl.find('[name="_method"]').val('PUT');

                    formEl.find('[name="warehouse_name"]').val(warehouse.warehouse_name);

                    const isChecked = (warehouse.is_active == 1 || warehouse.is_active === true);
                    formEl.find('#modal_is_active').prop('checked', isChecked);

                    formEl.find('[name="organization_id"]').val(warehouse.organization_id).trigger('change');

                    entityModal.show();
                },
                error: function(xhr) {
                    const msg = xhr.status === 403 ? "Akses ditolak." : "Gagal memuat data gudang.";
                    showNotification(msg, "error");
                }
            });
        }

        function loadToolsData(url) {
            const container = $(CONFIG.selectors.toolsContainer);
            container.css('opacity', '0.5');

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    container.css('opacity', '1');
                    container.html(response);
                },
                error: function() {
                    container.css('opacity', '1');
                    container.html('<div class="alert alert-danger text-center">Gagal memuat data alat.</div>');
                }
            });
        }

        function submitForm(e) {
            e.preventDefault();

            const form = $(this);
            const url = form.attr('action');
            const formData = new FormData(this);

            formData.set('is_active', formEl.find('[name="is_active"]').is(':checked') ? '1' : '0');

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
                    fetchTableData();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        displayFormErrors(formEl, xhr.responseJSON.errors);
                    } else {
                        showNotification("Terjadi kesalahan. Silakan coba lagi.", "error");
                    }
                },
                complete: function() {
                    toggleButtonLoading(submitButtonEl, false);
                }
            });
        }

        function deleteWarehouse(id) {
            const deleteUrl = `${CONFIG.urls.base}/${id}`;

            showConfirmation(CONFIG.messages.confirmDelete, "warning", CONFIG.messages.titleDelete)
                .then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'POST',
                            data: {
                                _method: 'DELETE'
                            },
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                showNotification(response.success, 'success');
                                fetchTableData();
                            },
                            error: function(xhr) {
                                const errorMessage = xhr.responseJSON?.error || "Gagal menghapus data.";
                                showNotification(errorMessage, "error");
                            }
                        });
                    }
                });
        }

        function updateWarehouseStatus(switcher) {
            const warehouseId = switcher.data('id');
            const newStatus = switcher.is(':checked');
            const originalStatus = !newStatus;
            const warehouseName = switcher.closest('tr').find('td:first').text();
            const statusText = newStatus ? 'Aktif' : 'Nonaktif';
            const updateUrl = `${CONFIG.urls.updateStatus}/${warehouseId}/update-status`;

            showConfirmation(
                `Apakah anda yakin ingin mengubah status <strong>${warehouseName}</strong> menjadi <strong>${statusText}</strong>?`,
                'warning',
                'Ubah Status Gudang?'
            ).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: updateUrl,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            is_active: newStatus ? 1 : 0
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showNotification(response.message, 'success');
                            } else {
                                showNotification(response.message, 'error');
                                switcher.prop('checked', originalStatus);
                            }
                        },
                        error: function() {
                            showNotification('Terjadi kesalahan saat menghubungi server.', 'error');
                            switcher.prop('checked', originalStatus);
                        }
                    });
                } else {
                    switcher.prop('checked', originalStatus);
                }
            });
        }

        function resetModalForm() {
            formEl[0].reset();
            formEl.attr('action', CONFIG.urls.store);
            formEl.find('[name="_method"]').val('POST');
            $(CONFIG.selectors.modalHeader).text('Tambah Gudang');
            formEl.find('#modal_is_active').prop('checked', true);
            formEl.find('[name="organization_id"]').val(null).trigger('change');
            clearFormErrors(formEl);
        }

        function clearFormErrors(form) {
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').hide();
        }

        function displayFormErrors(form, errors) {
            if (errors) {
                Object.keys(errors).forEach(key => {
                    const input = form.find(`[name="${key}"]`);
                    input.addClass('is-invalid');
                    input.closest('div').find('.invalid-feedback').text(errors[key][0]).show();
                });
            }
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

            $(document).on('click', CONFIG.selectors.addButton, function() {
                resetModalForm();
                entityModal.show();
            });

            $(document).on('click', '.btn-edit', function() {
                loadWarehouseData($(this).data('id'));
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                deleteWarehouse($(this).data('id'));
            });

            formEl.on('submit', submitForm);

            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', resetModalForm);
            }

            $(document).on('change', '.warehouse-status-switch', function() {
                updateWarehouseStatus($(this));
            });

            $(document).on('click', '.btn-view-tools', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                $(CONFIG.selectors.toolsTitle).text(name);

                if (toolsModal) {
                    toolsModal.show();
                    const url = `${CONFIG.urls.toolsBase}/${id}/tools`;
                    loadToolsData(url);
                }
            });

            $(document).on('click', `${CONFIG.selectors.toolsContainer} .pagination a`, function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (url) loadToolsData(url);
            });
        });
    </script>
@endpush
