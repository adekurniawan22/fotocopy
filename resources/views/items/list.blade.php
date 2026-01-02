{{-- Layout Utama --}}
@extends('layouts.app')

{{-- CSS --}}
@push('styles')
    <style>
        .image-input-placeholder {
            background-image: url('{{ asset('assets/media/svg/files/blank-image.svg') }}');
        }

        [data-bs-theme="dark"] .image-input-placeholder {
            background-image: url('{{ asset('assets/media/svg/files/blank-image-dark.svg') }}');
        }

        .image-input {
            margin-right: 10px;
            margin-bottom: 10px;
        }
    </style>
@endpush

{{-- Title --}}
@section('title', 'Data Barang')
@section('pageTitle', 'Data Barang')

{{-- Breadcumbs --}}
@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-line fw-semibold fs-7 my-1">
        <li class="breadcrumb-item text-gray-600">
            <a href="{{ route('dashboard.index') }}" class="text-gray-600 text-hover-primary">Home</a>
        </li>
        <li class="breadcrumb-item text-gray-600">Barang</li>
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
                        placeholder="Cari Nama Barang..." value="{{ request('search') }}" />
                </div>
            </div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" id="btn-add">
                        <i class="ki-duotone ki-plus fs-2"></i> Tambah Barang
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body py-4">
            <div id="table-container">
                @include('items.partials.table_data', ['items' => $items])
            </div>
        </div>
    </div>

    <div class="modal fade" id="kt_modal_item" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold" id="modal-title">Tambah Barang</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>

                <div class="modal-body mx-2 my-2">
                    <form id="kt_modal_form" class="form" action="#" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_method" value="POST">
                        <input type="hidden" name="item_id" value="">

                        <div class="scroll-y me-n7 pe-7" data-kt-scroll="true"
                            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
                            data-kt-scroll-dependencies="#modal-title" data-kt-scroll-offset="300px">

                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Nama Barang</label>
                                <input type="text" name="item_name" class="form-control form-control-solid"
                                    placeholder="Contoh: Laptop Asus ROG" />
                                <div class="invalid-feedback" id="item_name_error"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Harga Modal</label>
                                    <div class="input-group input-group-solid">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="buy_price"
                                            class="form-control form-control-solid rupiah-input" placeholder="0" value="0" />
                                    </div>
                                    <div class="invalid-feedback d-block" id="buy_price_error"></div>
                                </div>

                                <div class="col-md-4 fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Harga Jual</label>
                                    <div class="input-group input-group-solid">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="sell_price"
                                            class="form-control form-control-solid rupiah-input" placeholder="0" />
                                    </div>
                                    <div class="invalid-feedback d-block" id="sell_price_error"></div>
                                </div>

                                <div class="col-md-4 fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Satuan</label>
                                    <select name="unit" class="form-select form-select-solid">
                                        <option value="Pcs" selected>Pcs</option>
                                        <option value="Pack">Pack</option>
                                        <option value="Lusin">Lusin</option>
                                        <option value="Sepasang">Sepasang</option>
                                        <option value="Kotak">Kotak</option>
                                        <option value="Lembar">Lembar</option>
                                    </select>
                                    <div class="invalid-feedback" id="unit_error"></div>
                                </div>
                            </div>

                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">Deskripsi</label>
                                <textarea name="description" class="form-control form-control-solid" rows="3"
                                    placeholder="Keterangan detail barang..."></textarea>
                            </div>

                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">Foto Barang</label>
                                <div class="text-muted fs-7 mb-4">
                                    Format: png, jpg, jpeg. Klik tombol pencil untuk upload.
                                </div>

                                <div id="photo_container" class="d-flex flex-wrap gap-5">
                                </div>

                                <button type="button" class="btn btn-light-primary btn-sm mt-5" id="btn_add_photo">
                                    <i class="ki-duotone ki-plus fs-3"></i> Tambah Foto Lain
                                </button>
                                <div class="invalid-feedback d-block" id="foto_error"></div>
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
    <script src="{{ asset('assets/plugins/custom/fslightbox/fslightbox.bundle.js') }}"></script>
    <script>
        const CONFIG = {
            urls: {
                base: "{{ route('items.index') }}",
                store: "{{ route('items.store') }}",
                storage: "{{ asset('storage') }}"
            },
            selectors: {
                tableContainer: '#table-container',
                searchInput: '#search-input',
                modal: '#kt_modal_item',
                form: '#kt_modal_form',
                modalTitle: '#modal-title',
                submitBtn: '#btn_submit',
                addBtn: '#btn-add',
                addPhotoBtn: '#btn_add_photo',
                photoContainer: '#photo_container'
            },
            messages: {
                loading: '<div class="text-center p-10"><span class="spinner-border text-primary"></span> Loading...</div>',
                confirmDelete: "Apakah anda yakin ingin menghapus barang ini?",
                titleDelete: "Hapus Barang?",
                success: "Berhasil disimpan",
                error: "Terjadi kesalahan sistem",
                loadFail: "Gagal memuat data."
            },
            timing: {
                searchDelay: 500
            }
        };

        const formatRupiah = (angka) => {
            if (!angka && angka !== 0) return '';
            let num = typeof angka === 'string' ? parseFloat(angka.toString().replace(/\./g, '').replace(',', '.')) :
                angka;
            if (isNaN(num)) return '';
            return new Intl.NumberFormat('id-ID').format(num);
        }

        let photoIndex = 0;

        const createPhotoInput = (index, existingPath = null) => {
            const imgUrl = existingPath ? `{{ asset('storage') }}/${existingPath}` : '';
            const bgImage = existingPath ? `url('${imgUrl}')` : 'none';

            const hiddenInput = existingPath ?
                `<input type="hidden" name="saved_fotos[]" value="${existingPath}">` :
                '';

            return `
                <div class="ms-3 image-input image-input-outline image-input-placeholder" data-kt-image-input="true" id="kt_image_${index}">
                    ${hiddenInput}
                    <div class="image-input-wrapper w-125px h-125px shadow-sm" style="background-image: ${bgImage}; background-size: cover; background-position: center;"></div>
                    
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah Foto">
                        <i class="ki-duotone ki-pencil fs-7"><span class="path1"></span><span class="path2"></span></i>
                        <input type="file" name="foto[]" accept=".png, .jpg, .jpeg" />
                        <input type="hidden" name="avatar_remove" />
                    </label>

                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Batal">
                        <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </span>

                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow remove-photo-slot" data-index="${index}" data-bs-toggle="tooltip" title="Hapus Slot">
                        <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                    </span>
                </div>
            `;
        }

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

                    if (typeof refreshFsLightbox === 'function') {
                        refreshFsLightbox();
                    }
                },
                error: () => Swal.fire("Error", CONFIG.messages.loadFail, "error")
            });
        }

        function resetModalForm() {
            const formEl = $(CONFIG.selectors.form);
            formEl[0].reset();
            formEl.attr('action', CONFIG.urls.store);
            formEl.find('[name="_method"]').val('POST');
            formEl.find('[name="item_id"]').val('');
            
            formEl.find('[name="buy_price"]').val('0');
            
            formEl.find('[name="unit"]').val('Pcs');
            
            $(CONFIG.selectors.modalTitle).text('Tambah Barang');

            $(CONFIG.selectors.photoContainer).empty();
            photoIndex = 1;
            $(CONFIG.selectors.photoContainer).append(createPhotoInput(photoIndex));
            KTImageInput.createInstances();

            clearFormErrors(formEl);
        }

        function loadEntityData(id) {
            const url = `${CONFIG.urls.base}/${id}`;
            const formEl = $(CONFIG.selectors.form);
            const mainModal = bootstrap.Modal.getOrCreateInstance(document.querySelector(CONFIG.selectors.modal));

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
            $(CONFIG.selectors.modalTitle).text('Edit Barang');
            formEl.attr('action', url);
            formEl.find('[name="_method"]').val('PUT');

            formEl.find('[name="item_id"]').val(data.item_id);
            formEl.find('[name="item_name"]').val(data.item_name);
            formEl.find('[name="buy_price"]').val(formatRupiah(data.buy_price));
            formEl.find('[name="sell_price"]').val(formatRupiah(data.sell_price));
            formEl.find('[name="unit"]').val(data.unit);
            formEl.find('[name="description"]').val(data.description);

            $(CONFIG.selectors.photoContainer).empty();
            if (data.foto && Array.isArray(data.foto) && data.foto.length > 0) {
                data.foto.forEach((path, i) => {
                    photoIndex = i + 1;
                    $(CONFIG.selectors.photoContainer).append(createPhotoInput(photoIndex, path));
                });
            } else {
                $(CONFIG.selectors.photoContainer).append(createPhotoInput(1));
            }
            KTImageInput.createInstances();
            $('[data-bs-toggle="tooltip"]').tooltip();
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

                    const input = $(`[name="${cleanKey}"], [name^="${cleanKey}["]`);

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
            const rawBuy = formEl.find('[name="buy_price"]').val().replace(/\./g, '');
            const rawSell = formEl.find('[name="sell_price"]').val().replace(/\./g, '');

            formData.set('buy_price', rawBuy);
            formData.set('sell_price', rawSell);

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
                    const modalEl = document.querySelector(CONFIG.selectors.modal);
                    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modalInstance.hide();

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
            const mainModal = bootstrap.Modal.getOrCreateInstance(modalEl);

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

            $(document).on('keyup', '.rupiah-input', function() {
                let value = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(formatRupiah(value));
            });

            $(CONFIG.selectors.addPhotoBtn).on('click', function() {
                photoIndex++;
                $(CONFIG.selectors.photoContainer).append(createPhotoInput(photoIndex));
                KTImageInput.createInstances();
                $('[data-bs-toggle="tooltip"]').tooltip();
            });

            $(document).on('click', '.remove-photo-slot', function() {
                const index = $(this).data('index');
                $(`#kt_image_${index}`).remove();
            });
        });
    </script>
@endpush