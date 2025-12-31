{{-- Layout Utama --}}
@extends('layouts.app')

{{-- CSS --}}
@push('styles')
    <style>
        .tool-row {
            /* background-color: #8d8d8d; */
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #e4e6ef;
        }
    </style>
@endpush

{{-- Title --}}
@section('title', 'Metode Alat Kerja')

{{-- Page Title --}}
@section('pageTitle', 'Metode Alat Kerja')

{{-- Breadcumbs --}}
@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-line fw-semibold fs-7 my-1">
        <li class="breadcrumb-item text-gray-600">
            <a href="{{ route(session('user_data.short_role_name') . '.dashboard') }}"
                class="text-gray-600 text-hover-primary">Home</a>
        </li>
        <li class="breadcrumb-item text-gray-600">Metode Alat Kerja</li>
    </ul>
@endsection

{{-- Content --}}
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1 me-3">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5"><span class="path1"></span><span
                            class="path2"></span></i>
                    <input type="text" id="search-input" class="form-control form-control-solid w-250px ps-13"
                        placeholder="Cari Metode..." />
                </div>
            </div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" id="btn-add-method">
                        <i class="ki-duotone ki-plus fs-2"></i> Metode
                    </button>
                </div>
            </div>
        </div>

        <div id="method-table-container">
            @include('method.partials.table_data', ['methods' => $methods])
        </div>
    </div>

    {{-- Modal Tambah/Edit --}}
    <div class="modal fade" id="kt_modal_method" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold" id="modal-title">Tambah Metode</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>

                <div class="modal-body">
                    <form id="kt_modal_method_form" class="form" action="#">
                        @csrf
                        <input type="hidden" name="_method" value="POST">
                        <div class="d-flex flex-column me-n7 pe-7">

                            @if (Auth::user()->role_id == 1)
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Organisasi</label>
                                    <select name="organization_id" id="select_organization"
                                        class="form-select form-select-solid" data-control="select2"
                                        data-dropdown-parent="#kt_modal_method" data-placeholder="Pilih Organisasi">
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
                                <label class="required fw-semibold fs-6 mb-2">Nama Metode</label>
                                <input type="text" name="nama_method" class="form-control form-control-solid"
                                    placeholder="Contoh: Metode Instalasi FO" />
                                <div class="invalid-feedback" id="nama_method_error"></div>
                            </div>

                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Daftar Alat Kerja</label>
                                <div class="form-text mb-3">Pilih alat dan tentukan jumlah yang dibutuhkan.</div>

                                <div id="tools-repeater-container">
                                </div>

                                <button type="button" class="btn btn-light-primary btn-sm mt-2" id="btn-add-tool-row">
                                    <i class="ki-duotone ki-plus fs-3"></i> Tambah Alat
                                </button>

                                <div class="invalid-feedback d-block" id="list_tools_error"></div>
                            </div>

                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-submit">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">Please wait... <span
                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <template id="tool-row-template">
        <div class="tool-row row mb-3 align-items-start mx-1 bg-light">
            <div class="col-md-7">
                <label class="form-label fs-7 required">Pilih Alat</label>
                <select class="form-select form-select-sm tool-select" data-placeholder="Pilih Alat">
                    <option></option>
                    @foreach ($available_tools as $tool)
                        <option value="{{ $tool->tool_id }}" data-stok="{{ $tool->jumlah }}">
                            {{ $tool->nama }} (Stok: {{ $tool->jumlah }})
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback tool-id-error"></div>
            </div>
            <div class="col-md-3">
                <label class="form-label fs-7 required">Jumlah</label>
                <input type="number" class="form-control form-control-sm tool-qty" placeholder="0" min="1">
                <div class="invalid-feedback tool-qty-error"></div>
            </div>
            <div class="col-md-2 pt-8">
                <div class="btn btn-sm btn-icon btn-light-danger btn-remove-tool w-100" title="Hapus Baris">
                    <i class="ki-duotone ki-trash fs-3 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                    </i> Hapus
                </div>
            </div>
        </div>
    </template>
@endsection

{{-- Javascript --}}
@push('scripts')
    <script>
        const CONFIG = {
            urls: {
                base: "{{ route(session('user_data.short_role_name') . '.setting-method.index') }}",
                store: "{{ route(session('user_data.short_role_name') . '.setting-method.store') }}",
                getTools: "{{ route(session('user_data.short_role_name') . '.setting-method.get-tools') }}"
            },
            selectors: {
                tableContainer: '#method-table-container',
                searchInput: '#search-input',
                modal: '#kt_modal_method',
                form: '#kt_modal_method_form',
                submitBtn: '#btn-submit',
                addBtn: '#btn-add-method',
                modalTitle: '#modal-title',
                orgSelect: '[name="organization_id"]'
            },
            messages: {
                loading: '<div class="text-center p-10"><span class="spinner-border text-primary"></span> Loading...</div>',
                confirmDel: 'Apakah anda yakin ingin menghapus metode ini?',
                success: 'Berhasil disimpan',
                error: 'Terjadi kesalahan sistem'
            },
            timing: {
                searchDelay: 300
            }
        };

        let globalAvailableTools = @json($available_tools ?? []);

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
                success: (res) => {
                    $(CONFIG.selectors.tableContainer).html(res);
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
        const orgSelect = formEl.find(CONFIG.selectors.orgSelect);

        $(document).on('change', CONFIG.selectors.orgSelect, function() {
            const orgId = $(this).val();

            $('#tools-repeater-container').empty();

            if (!orgId) {
                globalAvailableTools = [];
                return;
            }

            $.ajax({
                url: CONFIG.urls.getTools,
                type: 'GET',
                data: {
                    organization_id: orgId
                },
                success: function(res) {
                    globalAvailableTools = res;
                    addToolRow();
                },
                error: function() {
                    showNotification('Gagal memuat data alat untuk organisasi ini.', 'error');
                }
            });
        });

        function updateToolAvailability() {
            let selectedIds = [];
            $('#tools-repeater-container .tool-select').each(function() {
                let val = $(this).val();
                if (val) selectedIds.push(val);
            });

            $('#tools-repeater-container .tool-select').each(function() {
                let $select = $(this);
                let currentVal = $select.val();

                $select.find('option').each(function() {
                    let $option = $(this);
                    let optVal = $option.val();

                    if (!optVal) return;

                    if (selectedIds.includes(optVal) && optVal != currentVal) {
                        $option.prop('disabled', true);
                    } else {
                        $option.prop('disabled', false);
                    }
                });
            });
        }

        function addToolRow(data = null) {
            const template = document.getElementById('tool-row-template');
            const clone = template.content.cloneNode(true);
            const container = $('#tools-repeater-container');

            container.append(clone);

            const newRow = container.find('.tool-row').last();
            const select = newRow.find('.tool-select');
            const inputQty = newRow.find('.tool-qty');

            select.empty();
            select.append('<option></option>');

            if (globalAvailableTools && globalAvailableTools.length > 0) {
                globalAvailableTools.forEach(tool => {
                    const id = tool.tool_id || tool.id;

                    const option = new Option(`${tool.nama} (Stok: ${tool.jumlah})`, id);

                    $(option).attr('data-stok', tool.jumlah);

                    select.append(option);
                });
            }

            select.select2({
                dropdownParent: $(CONFIG.selectors.modal),
                width: '100%',
                allowClear: true,
                placeholder: 'Pilih Alat'
            });

            if (data) {
                select.val(data.id).trigger('change');
                inputQty.val(data.qty);
            }

            const validateStok = () => {
                const selectedOption = select.find(':selected');
                const maxStok = parseInt(selectedOption.attr('data-stok')) || 0;
                const val = parseInt(inputQty.val()) || 0;
            };

            inputQty.on('input', validateStok);

            select.on('change', function() {
                validateStok();
                updateToolAvailability();
            });

            updateToolAvailability();
        }


        function loadEntityData(id) {
            const url = `${CONFIG.urls.base}/${id}`;
            resetModalForm();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: (res) => {
                    if (res.available_tools) {
                        globalAvailableTools = res.available_tools;
                    }

                    prepareFormForEdit(url, res);
                    mainModal.show();
                },
                error: () => showNotification("Gagal mengambil data.", "error")
            });
        }

        function submitForm(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.delete('list_tools');

            $('#tools-repeater-container .tool-row').each(function(index) {
                const toolId = $(this).find('.tool-select').val();
                const qty = $(this).find('.tool-qty').val();

                if (toolId || qty) {
                    formData.append(`list_tools[${index}][id]`, toolId);
                    formData.append(`list_tools[${index}][qty]`, qty);
                }
            });

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
                success: (res) => {
                    mainModal.hide();
                    showNotification(res.success, 'success');
                    fetchTableData();
                },
                error: (xhr) => {
                    if (xhr.status === 422) {
                        handleValidationErrors(xhr.responseJSON.errors);
                    } else {
                        showNotification(xhr.responseJSON?.error || CONFIG.messages.error, "error");
                    }
                },
                complete: () => toggleButtonLoading(submitBtn, false)
            });
        }

        function deleteEntity(id) {
            showConfirmation(CONFIG.messages.confirmDel, "warning", "Hapus Data").then((result) => {
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
                        success: (res) => {
                            showNotification(res.success, 'success');
                            fetchTableData();
                        },
                        error: (xhr) => showNotification(xhr.responseJSON?.error || "Gagal menghapus.",
                            "error")
                    });
                }
            });
        }

        function resetModalForm() {
            formEl[0].reset();
            formEl.attr('action', CONFIG.urls.store);
            formEl.find('[name="_method"]').val('POST');
            $(CONFIG.selectors.modalTitle).text('Tambah Metode');

            if (orgSelect.length && orgSelect.is('select')) {
                orgSelect.val(null).trigger('change');
            }

            $('#tools-repeater-container').empty();

            const isSelect = orgSelect.length && orgSelect.is('select');
            if (!isSelect || (isSelect && orgSelect.val())) {
                addToolRow();
            }

            clearFormErrors(formEl);
            $('#list_tools_error').hide();
        }

        function prepareFormForEdit(url, data) {
            const method = data.method;
            const toolList = data.list_tools;

            formEl.attr('action', url);
            formEl.find('[name="_method"]').val('PUT');
            $(CONFIG.selectors.modalTitle).text('Edit Metode');
            formEl.find('[name="nama_method"]').val(method.nama_method);

            if (orgSelect.length) {
                if (orgSelect.is('select')) {
                    orgSelect.val(method.organization_id).trigger('change.select2');
                } else {
                    orgSelect.val(method.organization_id);
                }
            }

            $('#tools-repeater-container').empty();
            if (toolList && toolList.length > 0) {
                toolList.forEach(item => {
                    addToolRow(item);
                });
            } else {
                addToolRow();
            }
            setTimeout(updateToolAvailability, 100);
        }

        function handleValidationErrors(errors) {
            if (errors) {
                Object.keys(errors).forEach(key => {
                    if (key.startsWith('list_tools')) {
                        const parts = key.split('.');
                        const index = parts[1];
                        const field = parts[2];
                        const row = $('#tools-repeater-container .tool-row').eq(index);
                        const errorMsg = errors[key][0];

                        if (field === 'qty') {
                            row.find('.tool-qty').addClass('is-invalid');
                            row.find('.tool-qty-error').text(errorMsg).show();
                        }
                        if (field === 'id') {
                            row.find('.select2-selection').addClass('is-invalid');
                            row.find('.tool-id-error').text(errorMsg).show();
                        }
                    } else {
                        const errorId = `#${key}_error`;
                        const $errorEl = $(errorId);
                        if ($errorEl.length) {
                            $errorEl.text(errors[key][0]).show();
                            if (key === 'organization_id' && orgSelect.is('select')) {
                                $errorEl.parent().find('.select2-selection').addClass('is-invalid');
                            } else {
                                $errorEl.closest('.fv-row').find('.form-control').addClass('is-invalid');
                            }
                        }
                    }
                });
            }
        }

        function clearFormErrors(form) {
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').hide();
            form.find('.tool-qty-error, .tool-id-error').hide();
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

            $(document).on('click', CONFIG.selectors.submitBtn, function(e) {
                e.preventDefault();
                $(CONFIG.selectors.form).trigger('submit');
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

            $(document).on('click', '.btn-edit', function() {
                loadEntityData($(this).data('id'));
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                deleteEntity($(this).data('id'));
            });

            modalEl.addEventListener('hidden.bs.modal', resetModalForm);
            formEl.on('submit', submitForm);

            $(document).on('click', '#btn-add-tool-row', function() {
                addToolRow();
            });

            $(document).on('click', '.btn-remove-tool', function() {
                $(this).closest('.tool-row').remove();
                updateToolAvailability();
            });

            $(document).on('click', '.btn-toggle-tools', function(e) {
                e.preventDefault();
                const btn = $(this);
                const id = btn.data('id');
                const remaining = btn.data('remaining');
                const hiddenItems = $(`.tool-item-${id}`);
                const isHidden = hiddenItems.first().hasClass('d-none');

                if (isHidden) {
                    hiddenItems.removeClass('d-none');
                    btn.text('Sembunyikan');
                    btn.removeClass('badge-light-primary border-primary text-primary');
                    btn.addClass('badge-light-danger border-danger text-danger');
                } else {
                    hiddenItems.addClass('d-none');
                    btn.text(`+${remaining} Lainnya`);
                    btn.removeClass('badge-light-danger border-danger text-danger');
                    btn.addClass('badge-light-primary border-primary text-primary');
                }
            });
        });
    </script>
@endpush
