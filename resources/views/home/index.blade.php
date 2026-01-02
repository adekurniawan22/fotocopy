<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Katalog Barang</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f8fa;
            color: #181c32;
        }

        .navbar {
            background-color: #ffffff;
            box-shadow: 0px 10px 30px 0px rgba(82, 63, 105, 0.05);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: #181c32;
            font-size: 1.5rem;
        }

        /* Input Styles matching Metronic */
        .form-control-solid,
        .form-select-solid {
            background-color: #f5f8fa;
            border-color: #f5f8fa;
            color: #5e6278;
            transition: color 0.2s ease;
        }

        .form-control-solid:focus,
        .form-select-solid:focus {
            background-color: #eef3f7;
            border-color: #eef3f7;
            color: #5e6278;
        }

        .input-group-solid .input-group-text {
            background-color: #f5f8fa;
            border-color: #f5f8fa;
            color: #5e6278;
        }

        /* Card & Layout */
        .search-container {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-icon {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a1a5b7;
        }

        .card-custom {
            border: 0;
            border-radius: 0.85rem;
            box-shadow: 0px 0px 20px 0px rgba(76, 87, 125, 0.02);
            background-color: #ffffff;
            transition: all 0.3s ease;
            height: 100%;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0px 10px 40px 0px rgba(76, 87, 125, 0.1);
        }

        .card-img-wrapper {
            height: 200px;
            overflow: hidden;
            border-top-left-radius: 0.85rem;
            border-top-right-radius: 0.85rem;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 3rem;
            line-height: 1.5rem;
        }

        .text-price {
            color: #009ef7;
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* Pagination & Loading */
        .pagination {
            justify-content: center;
            margin-top: 2rem;
        }

        .page-link {
            border: 0;
            border-radius: 0.5rem;
            margin: 0 3px;
            color: #5e6278;
        }

        .page-item.active .page-link {
            background-color: #009ef7;
            color: #fff;
        }

        #loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        /* --- STYLING FOTO CUSTOM (MIRIP KTIMAGE) --- */
        .image-input {
            position: relative;
            display: inline-block;
            border-radius: 0.475rem;
            background-repeat: no-repeat;
            background-size: cover;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .image-input-wrapper {
            width: 125px;
            height: 125px;
            border-radius: 0.475rem;
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            border: 1px solid #e4e6ef;
            /* Border tipis */
            box-shadow: 0 0.1rem 1rem 0.25rem rgba(0, 0, 0, 0.05);
        }

        /* Placeholder default */
        .image-input-placeholder .image-input-wrapper {
            background-image: url('{{ asset('assets/media/svg/files/blank-image.svg') }}');
        }

        /* Tombol Edit/Hapus bulat */
        .btn-circle {
            border-radius: 50%;
            padding: 0;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075);
            background-color: #ffffff;
            position: absolute;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
        }

        .btn-circle:hover {
            background-color: #f4f6f8;
        }

        /* Posisi Tombol Edit (Pencil) */
        .btn-edit-photo {
            top: -10px;
            right: -10px;
        }

        /* Posisi Tombol Hapus (Silang) */
        .btn-remove-photo {
            bottom: -10px;
            right: -10px;
        }

        .text-danger-custom {
            color: #f1416c;
        }

        .text-primary-custom {
            color: #009ef7;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-layer-group text-primary me-2"></i>FOTOCOPY
            </a>

            <div class="d-flex align-items-center">
                @auth
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle fw-bold text-dark" type="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            @if (Auth::id() == 1)
                                <li>
                                    <a href="{{ route('dashboard.index') }}"
                                        class="dropdown-item text-primary">Dashboard</a>
                                </li>
                            @endif
                            <li>
                                <form action="{{ route('logout.katalog') }}" method="GET">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary fw-bold px-4">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="bg-white pb-2 pt-5 mb-5 border-bottom">
        <div class="container text-center">
            <h1 class="fw-bolder mb-4 text-dark">Cari Barang</h1>

            <div class="search-container mb-4">
                <input type="text" id="search-input" class="form-control form-control-solid"
                    placeholder="Ketik nama barang...">
                <i class="fas fa-search search-icon"></i>
            </div>

            @auth
                <button type="button" class="btn btn-sm btn-success mb-4" id="btn-add">
                    <i class="fas fa-plus me-1"></i> Tambah Barang Baru
                </button>
            @endauth
        </div>
    </div>

    <div class="container pb-5">
        <div id="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div id="items-container">
            @include('home.partials.item_grid')
        </div>
    </div>

    @auth
        <div class="modal fade" id="kt_modal_item" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="fw-bold" id="modal-title">Tambah Barang</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body mx-2 my-2">
                        <form id="kt_modal_form" class="form" action="#" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_method" value="POST">
                            <input type="hidden" name="item_id" value="">

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Nama Barang</label>
                                    <input type="text" name="item_name" class="form-control form-control-solid"
                                        placeholder="Contoh: Laptop Asus ROG" required/>
                                    <div class="invalid-feedback" id="item_name_error"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Harga Modal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" inputmode="numeric" name="buy_price"
                                            class="form-control rupiah-input" placeholder="0"
                                            value="0" />
                                    </div>
                                    <div class="invalid-feedback d-block" id="buy_price_error"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Harga Jual</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" inputmode="numeric" name="sell_price"
                                            class="form-control rupiah-input" placeholder="0" required/>
                                    </div>
                                    <div class="invalid-feedback d-block" id="sell_price_error"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Satuan</label>
                                    <select name="unit" class="form-select form-select-solid">
                                        <option value="Pcs" selected>Pcs</option>
                                        <option value="Pack">Pack</option>
                                        <option value="Lusin">Lusin</option>
                                        <option value="Sepasang">Sepasang</option>
                                        <option value="Kotak">Kotak</option>
                                        <option value="Lembar">Lembar</option>
                                    </select>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="fw-semibold fs-6 mb-2">Deskripsi</label>
                                    <textarea name="description" class="form-control form-control-solid" rows="3"
                                        placeholder="Keterangan detail barang..."></textarea>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="fw-semibold fs-6 mb-2">Foto Barang</label>
                                    <div class="text-muted fs-7 mb-4">
                                        Format: png, jpg, jpeg.
                                    </div>

                                    <div id="photo_container" class="d-flex flex-wrap gap-2"></div>

                                    <button type="button" class="btn btn-secondary btn-sm mt-4"
                                        id="btn_add_photo">
                                        <i class="fas fa-plus me-1"></i> Tambah Foto Lain
                                    </button>
                                    <div class="invalid-feedback d-block" id="foto_error"></div>
                                </div>
                            </div>

                            <div class="text-end pt-5">
                                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary" id="btn_submit">
                                    <span class="indicator-label">Simpan</span>
                                    <span class="indicator-progress" style="display:none">
                                        Please wait... <span
                                            class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endauth

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/plugins/custom/fslightbox/fslightbox.bundle.js') }}"></script>

    <script>
        $(document).ready(function() {
            let timer;

            function fetchItems(page = 1) {
                let search = $('#search-input').val();
                let url = "{{ route('home') }}?page=" + page + "&search=" + search;
                $('#items-container').css('opacity', '0.5');
                $('#loading-spinner').show();

                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(response) {
                        $('#items-container').html(response);
                        $('#items-container').css('opacity', '1');
                        $('#loading-spinner').hide();
                        if (typeof refreshFsLightbox === 'function') refreshFsLightbox();
                    },
                    error: function() {
                        alert('Gagal memuat data.');
                    }
                });
            }

            $('#search-input').on('keyup', function() {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    fetchItems(1);
                }, 500);
            });

            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                fetchItems(page);
            });
        });

        @auth
        const CONFIG = {
            urls: {
                store: "{{ route('items.store') }}",
                base: "{{ route('items.index') }}",
                storage: "{{ asset('storage') }}"
            }
        };

        const formatRupiah = (angka) => {
            if (!angka && angka !== 0) return '';
            let num = typeof angka === 'string' ? parseFloat(angka.toString().replace(/\./g, '').replace(',', '.')) :
                angka;
            if (isNaN(num)) return '';
            return new Intl.NumberFormat('id-ID').format(num);
        }

        // FIX: Create Photo Input dengan Style KTImage (Bulat & Shadow)
        function createPhotoInput(index, existingPath = null) {
            const imgUrl = existingPath ? `${CONFIG.urls.storage}/${existingPath}` : '';
            // Gunakan SVG blank jika tidak ada gambar
            const blankImg = "{{ asset('assets/media/svg/files/blank-image.svg') }}";
            const bgImage = existingPath ? `url('${imgUrl}')` : `url('${blankImg}')`;

            const hiddenInput = existingPath ? `<input type="hidden" name="saved_fotos[]" value="${existingPath}">` : '';

            return `
                <div class="image-input image-input-placeholder" id="kt_image_${index}">
                    ${hiddenInput}
                    <div class="image-input-wrapper shadow-sm" style="background-image: ${bgImage};"></div>

                    <label class="btn-circle btn-edit-photo shadow" data-bs-toggle="tooltip" title="Ubah Foto">
                        <i class="fas fa-pencil-alt fs-7 text-primary-custom"></i>
                        <input type="file" name="foto[]" accept=".png, .jpg, .jpeg" style="display:none" onchange="previewImage(this, ${index})" />
                        <input type="hidden" name="avatar_remove" />
                    </label>

                    <span class="btn-circle btn-remove-photo shadow remove-photo-slot" data-index="${index}" data-bs-toggle="tooltip" title="Hapus Slot">
                        <i class="fas fa-times fs-7 text-danger-custom"></i>
                    </span>
                </div>
            `;
        }

        window.previewImage = function(input, index) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $(`#kt_image_${index} .image-input-wrapper`).css('background-image', 'url(' + e.target.result +
                        ')');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        let photoIndex = 0;
        const modalEl = document.getElementById('kt_modal_item');
        const mainModal = new bootstrap.Modal(modalEl);

        function resetModalForm() {
            $('#kt_modal_form')[0].reset();
            $('#kt_modal_form').attr('action', CONFIG.urls.store);
            $('input[name="_method"]').val('POST');
            $('input[name="item_id"]').val('');
            $('input[name="buy_price"]').val('0');
            $('#modal-title').text('Tambah Barang');

            $('#photo_container').empty();
            photoIndex = 1;
            $('#photo_container').append(createPhotoInput(photoIndex));

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();

            // Reset Button State
            $('.indicator-label').show();
            $('.indicator-progress').hide();
            $('#btn_submit').prop('disabled', false);
        }

        $('#btn-add').click(function() {
            resetModalForm();
            mainModal.show();
        });

        $(document).on('click', '.btn-edit-item', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let url = `${CONFIG.urls.base}/${id}`;

            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    resetModalForm();
                    $('#modal-title').text('Edit Barang');
                    $('#kt_modal_form').attr('action', url);
                    $('input[name="_method"]').val('PUT');

                    $('input[name="item_id"]').val(data.item_id);
                    $('input[name="item_name"]').val(data.item_name);
                    $('input[name="buy_price"]').val(formatRupiah(data.buy_price));
                    $('input[name="sell_price"]').val(formatRupiah(data.sell_price));
                    $('select[name="unit"]').val(data.unit);
                    $('textarea[name="description"]').val(data.description);

                    $('#photo_container').empty();
                    if (data.foto && data.foto.length > 0) {
                        data.foto.forEach((path, i) => {
                            photoIndex = i + 1;
                            $('#photo_container').append(createPhotoInput(photoIndex, path));
                        });
                    } else {
                        $('#photo_container').append(createPhotoInput(1));
                    }
                    mainModal.show();
                },
                error: function() {
                    Swal.fire("Error", "Gagal load data", "error");
                }
            });
        });

        $(document).on('click', '.btn-delete-item', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            Swal.fire({
                title: "Hapus Barang?",
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `${CONFIG.urls.base}/${id}`,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function() {
                            Swal.fire("Berhasil", "Data dihapus", "success");
                            $('#search-input').trigger('keyup');
                        },
                        error: function() {
                            Swal.fire("Gagal", "Error sistem", "error");
                        }
                    });
                }
            });
        });

        $('#kt_modal_form').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            formData.set('buy_price', formData.get('buy_price').replace(/\./g, ''));
            formData.set('sell_price', formData.get('sell_price').replace(/\./g, ''));

            let btn = $('#btn_submit');
            let label = btn.find('.indicator-label');
            let progress = btn.find('.indicator-progress');

            // FIX: Loading State Logic
            btn.prop('disabled', true);
            label.hide();
            progress.show();

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    mainModal.hide();
                    Swal.fire("Sukses", "Data berhasil disimpan", "success");
                    $('#search-input').trigger('keyup');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            let cleanKey = key.split('.')[0];
                            let input = $(`[name="${cleanKey}"], [name^="${cleanKey}"]`);
                            input.addClass('is-invalid');
                            $(`#${cleanKey}_error`).text(errors[key][0]).show();
                        });
                    } else {
                        Swal.fire("Error", "Gagal menyimpan", "error");
                    }
                },
                complete: function() {
                    // FIX: Restore Button State
                    btn.prop('disabled', false);
                    label.show();
                    progress.hide();
                }
            });
        });

        $(document).on('keyup', '.rupiah-input', function() {
            let val = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(formatRupiah(val));
        });

        $('#btn_add_photo').click(function() {
            photoIndex++;
            $('#photo_container').append(createPhotoInput(photoIndex));
        });

        $(document).on('click', '.remove-photo-slot', function() {
            let idx = $(this).data('index');
            $(`#kt_image_${idx}`).remove();
        });
        @endauth
    </script>
</body>

</html>
