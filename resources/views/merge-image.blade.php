<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Merge Image</title>
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
            font-size: 0.8rem;
        }

        .card-panel {
            border: 0;
            border-radius: 0.85rem;
            box-shadow: 0px 0px 20px 0px rgba(76, 87, 125, 0.04);
            background-color: #ffffff;
        }

        .section-label {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }

        .text-danger-custom {
            color: #f1416c;
        }

        .text-primary-custom {
            color: #009ef7;
        }

        /* --- Area Drop / Paste --- */
        .drop-zone {
            border: 2px dashed #b5b5c3;
            border-radius: 0.85rem;
            background-color: #f8faff;
            padding: 2rem 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .drop-zone:hover,
        .drop-zone.dragover {
            border-color: #009ef7;
            background-color: #eef6ff;
        }

        .drop-zone i {
            font-size: 2.2rem;
            color: #009ef7;
        }

        kbd {
            background-color: #181c32;
            font-size: 0.75rem;
        }

        /* --- Thumbnail foto (sama seperti form barang) --- */
        #photo_grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            min-height: 20px;
        }

        .image-input {
            position: relative;
            display: inline-block;
            border-radius: 0.475rem;
            cursor: grab;
        }

        .image-input-wrapper {
            width: 125px;
            height: 125px;
            border-radius: 0.475rem;
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            background-color: #f9f9f9;
            border: 1px solid #e4e6ef;
            box-shadow: 0 0.1rem 1rem 0.25rem rgba(0, 0, 0, 0.05);
        }

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

        .btn-remove-photo {
            bottom: -10px;
            right: -10px;
        }

        .photo-number {
            position: absolute;
            top: 6px;
            left: 6px;
            background-color: rgba(24, 28, 50, 0.75);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 600;
            border-radius: 0.4rem;
            padding: 1px 7px;
        }

        .photo-warning {
            position: absolute;
            top: 6px;
            right: 6px;
            background-color: #ffc700;
            color: #181c32;
            font-size: 0.7rem;
            border-radius: 0.4rem;
            padding: 1px 6px;
        }

        .sortable-ghost {
            opacity: 0.4;
        }

        /* --- Pratinjau A4 --- */
        .a4-preview-wrap {
            display: flex;
            justify-content: center;
            padding: 1rem;
            background-color: #f5f8fa;
            border-radius: 0.65rem;
        }

        .a4-preview {
            position: relative;
            width: 150px;
            background-color: #ffffff;
            box-shadow: 0 0.3rem 1rem rgba(0, 0, 0, 0.12);
            transition: width 0.2s ease;
        }

        .a4-preview.landscape {
            width: 212px;
        }

        .a4-margin-area {
            position: absolute;
            border: 1px dashed #009ef7;
        }

        .a4-preview img,
        .a4-preview .a4-placeholder {
            position: absolute;
        }

        .a4-preview .a4-placeholder {
            background-color: #e4e6ef;
        }

        #loading-text {
            font-size: 0.85rem;
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
                            <li>
                                <a href="{{ route('home') }}" class="dropdown-item">Katalog</a>
                            </li>
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
                @endauth
            </div>
        </div>
    </nav>

    <div class="bg-white pb-2 pt-5 mb-5 border-bottom">
        <div class="container text-center">
            <h1 class="fw-bolder mb-2 text-dark">Merge Image</h1>
            <p class="text-muted mb-4">Gabungkan banyak foto menjadi satu PDF A4 siap cetak.</p>
            <a href="{{ route('home') }}" class="btn btn-sm btn-light mb-4">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Katalog
            </a>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row g-4">

            {{-- ===== Pengaturan ===== --}}
            <div class="col-lg-4">
                <div class="card card-panel p-4">
                    <h5 class="fw-bold mb-4"><i class="fas fa-sliders-h text-primary me-2"></i>Pengaturan</h5>

                    <div class="mb-4">
                        <div class="section-label">Nama File PDF</div>
                        <input type="text" id="file_name" class="form-control form-control-solid"
                            placeholder="Contoh: bu-rina" value="">
                    </div>

                    <div class="mb-4">
                        <div class="section-label">Margin (mm)</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="text-muted fs-7 mb-1">Atas</label>
                                <div class="input-group input-group-solid">
                                    <input type="number" id="margin_top" class="form-control form-control-solid"
                                        min="0" max="100" step="0.5" value="10">
                                    <span class="input-group-text">mm</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="text-muted fs-7 mb-1">Bawah</label>
                                <div class="input-group input-group-solid">
                                    <input type="number" id="margin_bottom" class="form-control form-control-solid"
                                        min="0" max="100" step="0.5" value="10">
                                    <span class="input-group-text">mm</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="text-muted fs-7 mb-1">Kiri</label>
                                <div class="input-group input-group-solid">
                                    <input type="number" id="margin_left" class="form-control form-control-solid"
                                        min="0" max="100" step="0.5" value="10">
                                    <span class="input-group-text">mm</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="text-muted fs-7 mb-1">Kanan</label>
                                <div class="input-group input-group-solid">
                                    <input type="number" id="margin_right" class="form-control form-control-solid"
                                        min="0" max="100" step="0.5" value="10">
                                    <span class="input-group-text">mm</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="section-label">Orientasi Halaman</div>
                        <select id="orientation" class="form-select form-select-solid">
                            <option value="auto" selected>Otomatis (ikut posisi foto)</option>
                            <option value="portrait">Selalu Portrait</option>
                            <option value="landscape">Selalu Landscape</option>
                        </select>
                    </div>

                    <div>
                        <div class="section-label">Pratinjau Halaman Pertama</div>
                        <div class="a4-preview-wrap">
                            <div class="a4-preview" id="a4_preview">
                                <div class="a4-margin-area" id="a4_margin"></div>
                                <div class="a4-placeholder" id="a4_placeholder"></div>
                                <img id="a4_img" alt="" style="display:none">
                            </div>
                        </div>
                        <div class="text-muted fs-7 mt-2 text-center">Garis putus-putus = batas margin</div>
                    </div>
                </div>
            </div>

            {{-- ===== Foto ===== --}}
            <div class="col-lg-8">
                <div class="card card-panel p-4">
                    <div class="drop-zone mb-4" id="drop_zone">
                        <i class="fas fa-cloud-upload-alt mb-2"></i>
                        <div class="fw-bold">Tempel foto dengan <kbd>Ctrl</kbd> + <kbd>V</kbd></div>
                        <div class="text-muted fs-7 mt-1">
                            atau seret banyak foto / file ZIP ke sini, atau klik untuk memilih file
                        </div>
                        <input type="file" id="file_input" multiple accept="image/*,.zip" style="display:none">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="fw-semibold">
                            <span id="photo_count">0</span> foto
                            <span class="text-muted fs-7 ms-1">(seret untuk mengubah urutan)</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-light text-danger" id="btn_clear" disabled>
                            <i class="fas fa-trash me-1"></i> Hapus Semua
                        </button>
                    </div>

                    <div id="empty_state" class="text-center text-muted py-4">
                        <img src="{{ asset('assets/media/svg/files/blank-image.svg') }}" height="70"
                            class="mb-2" style="opacity: 0.5">
                        <div>Belum ada foto.</div>
                    </div>

                    <div id="photo_grid" class="mb-2"></div>

                    <div id="low_res_note" class="alert alert-warning fs-7 mt-3 mb-0" style="display:none">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Foto bertanda kuning resolusinya rendah, hasil cetaknya bisa terlihat pecah.
                    </div>

                    <div class="d-flex justify-content-end align-items-center border-top pt-4 mt-4">
                        <span id="loading-text" class="text-muted me-3"></span>
                        <button type="button" class="btn btn-primary" id="btn_generate" disabled>
                            <span class="indicator-label"><i class="fas fa-file-pdf me-1"></i> Buat PDF</span>
                            <span class="indicator-progress" style="display:none">
                                Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <script>
        (function() {
            const STORAGE_KEY = 'mergeImageSettings';
            const TARGET_DPI = 300; // resolusi gambar di dalam PDF
            const MIN_DPI = 150; // di bawah ini diberi peringatan
            const IMAGE_EXT = /\.(jpe?g|png|webp|gif|bmp)$/i;

            const el = (id) => document.getElementById(id);
            const grid = el('photo_grid');

            let photos = []; // { id, url, w, h }
            let uid = 0;
            let busy = false;

            /* ---------- Pengaturan ---------- */
            function getSettings() {
                const num = (id) => Math.max(0, parseFloat(el(id).value) || 0);
                return {
                    top: num('margin_top'),
                    bottom: num('margin_bottom'),
                    left: num('margin_left'),
                    right: num('margin_right'),
                    orientation: el('orientation').value,
                    filename: el('file_name').value.trim() || 'cetak-foto'
                };
            }

            function saveSettings() {
                const s = getSettings();
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify({
                        top: s.top,
                        bottom: s.bottom,
                        left: s.left,
                        right: s.right,
                        orientation: s.orientation
                    }));
                } catch (e) {}
            }

            function loadSettings() {
                try {
                    const s = JSON.parse(localStorage.getItem(STORAGE_KEY));
                    if (!s) return;
                    el('margin_top').value = s.top;
                    el('margin_bottom').value = s.bottom;
                    el('margin_left').value = s.left;
                    el('margin_right').value = s.right;
                    el('orientation').value = s.orientation || 'auto';
                } catch (e) {}
            }

            /* ---------- Hitung posisi foto di A4 (satuan mm) ---------- */
            function pageLayout(w, h, s) {
                const landscape = s.orientation === 'auto' ? w > h : s.orientation === 'landscape';
                const pw = landscape ? 297 : 210;
                const ph = landscape ? 210 : 297;
                const aw = pw - s.left - s.right;
                const ah = ph - s.top - s.bottom;
                if (aw <= 0 || ah <= 0) return null;

                const scale = Math.min(aw / w, ah / h); // fit, proporsi tetap
                const dw = w * scale;
                const dh = h * scale;
                return {
                    landscape, pw, ph, dw, dh,
                    x: s.left + (aw - dw) / 2,
                    y: s.top + (ah - dh) / 2,
                    dpi: w / (dw / 25.4)
                };
            }

            function loadImage(src) {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.onload = () => resolve(img);
                    img.onerror = () => reject(new Error('Gagal membaca gambar'));
                    img.src = src;
                });
            }

            /* ---------- Tambah foto ---------- */
            function isZip(f) {
                return /zip/.test(f.type) || /\.zip$/i.test(f.name || '');
            }

            async function extractZip(file) {
                const zip = await JSZip.loadAsync(file);
                const entries = Object.values(zip.files)
                    .filter(e => !e.dir && IMAGE_EXT.test(e.name) && !e.name.startsWith('__MACOSX'))
                    .sort((a, b) => a.name.localeCompare(b.name, undefined, { numeric: true }));

                const files = [];
                for (const entry of entries) {
                    const blob = await entry.async('blob');
                    const ext = entry.name.split('.').pop().toLowerCase();
                    const type = 'image/' + (ext === 'jpg' ? 'jpeg' : ext);
                    files.push(new File([blob], entry.name.split('/').pop(), { type }));
                }
                return files;
            }

            async function addFiles(fileList) {
                if (busy) return;
                const files = [];
                try {
                    for (const f of fileList) {
                        if (isZip(f)) files.push(...await extractZip(f));
                        else if ((f.type || '').startsWith('image/') || IMAGE_EXT.test(f.name || '')) files.push(f);
                    }
                } catch (e) {
                    Swal.fire('Gagal', 'File ZIP tidak bisa dibuka.', 'error');
                    return;
                }

                if (!files.length) {
                    Swal.fire('Tidak ada gambar', 'Pilih file JPG, PNG, WEBP, atau ZIP berisi foto.', 'warning');
                    return;
                }

                let failed = 0;
                for (const f of files) {
                    const url = URL.createObjectURL(f);
                    try {
                        const img = await loadImage(url);
                        photos.push({ id: ++uid, url, w: img.naturalWidth, h: img.naturalHeight });
                    } catch (e) {
                        URL.revokeObjectURL(url);
                        failed++;
                    }
                }
                render();

                if (failed) {
                    Swal.fire('Perhatian', `${failed} file tidak bisa dibaca dan dilewati.`, 'warning');
                }
            }

            /* ---------- Tampilan ---------- */
            function render() {
                const s = getSettings();
                let lowRes = false;

                grid.innerHTML = photos.map((p, i) => {
                    const lay = pageLayout(p.w, p.h, s);
                    const low = lay && lay.dpi < MIN_DPI;
                    if (low) lowRes = true;
                    return `
                        <div class="image-input" data-id="${p.id}">
                            <div class="image-input-wrapper" style="background-image: url('${p.url}')"></div>
                            <span class="photo-number">${i + 1}</span>
                            ${low ? `<span class="photo-warning" title="Resolusi rendah (±${Math.round(lay.dpi)} DPI)"><i class="fas fa-exclamation-triangle"></i></span>` : ''}
                            <span class="btn-circle btn-remove-photo shadow" data-id="${p.id}" title="Hapus">
                                <i class="fas fa-times fs-7 text-danger-custom"></i>
                            </span>
                        </div>`;
                }).join('');

                el('photo_count').textContent = photos.length;
                el('empty_state').style.display = photos.length ? 'none' : 'block';
                el('low_res_note').style.display = lowRes ? 'block' : 'none';
                el('btn_generate').disabled = busy || !photos.length;
                el('btn_clear').disabled = busy || !photos.length;
                renderPreview(s);
            }

            function renderPreview(s) {
                const first = photos[0];
                const w = first ? first.w : 3;
                const h = first ? first.h : 4;
                const lay = pageLayout(w, h, s);
                const box = el('a4_preview');
                const landscape = s.orientation === 'auto' ? w > h : s.orientation === 'landscape';
                const pw = landscape ? 297 : 210;
                const ph = landscape ? 210 : 297;

                box.classList.toggle('landscape', landscape);
                box.style.aspectRatio = `${pw} / ${ph}`;

                const pct = (v, total) => (v / total * 100) + '%';
                const m = el('a4_margin');
                m.style.left = pct(s.left, pw);
                m.style.right = pct(s.right, pw);
                m.style.top = pct(s.top, ph);
                m.style.bottom = pct(s.bottom, ph);
                m.style.display = lay ? 'block' : 'none';

                const target = first ? el('a4_img') : el('a4_placeholder');
                el('a4_img').style.display = first && lay ? 'block' : 'none';
                el('a4_placeholder').style.display = !first && lay ? 'block' : 'none';
                if (first) el('a4_img').src = first.url;

                if (lay) {
                    target.style.left = pct(lay.x, pw);
                    target.style.top = pct(lay.y, ph);
                    target.style.width = pct(lay.dw, pw);
                    target.style.height = pct(lay.dh, ph);
                }
            }

            function setLoading(on, text = '') {
                busy = on;
                const btn = el('btn_generate');
                btn.querySelector('.indicator-label').style.display = on ? 'none' : 'inline';
                btn.querySelector('.indicator-progress').style.display = on ? 'inline' : 'none';
                el('loading-text').textContent = text;
                render();
            }

            /* ---------- Buat PDF ---------- */
            async function generatePdf() {
                if (!photos.length || busy) return;
                const s = getSettings();

                if (photos.some(p => !pageLayout(p.w, p.h, s))) {
                    Swal.fire('Margin terlalu besar', 'Tidak ada ruang tersisa untuk foto di kertas A4.', 'error');
                    return;
                }

                setLoading(true);
                try {
                    const { jsPDF } = window.jspdf;
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    let doc = null;

                    for (let i = 0; i < photos.length; i++) {
                        el('loading-text').textContent = `Memproses foto ${i + 1} dari ${photos.length}...`;
                        await new Promise(r => setTimeout(r, 0)); // beri jeda agar tampilan ter-update

                        // Gambar digambar ulang lewat canvas supaya rotasi foto HP ikut benar
                        const img = await loadImage(photos[i].url);
                        const lay = pageLayout(img.naturalWidth, img.naturalHeight, s);

                        let cw = Math.round(lay.dw / 25.4 * TARGET_DPI);
                        let ch = Math.round(lay.dh / 25.4 * TARGET_DPI);
                        if (cw > img.naturalWidth) { // jangan diperbesar melebihi aslinya
                            cw = img.naturalWidth;
                            ch = img.naturalHeight;
                        }

                        canvas.width = cw;
                        canvas.height = ch;
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, cw, ch);
                        ctx.imageSmoothingQuality = 'high';
                        ctx.drawImage(img, 0, 0, cw, ch);
                        const data = canvas.toDataURL('image/jpeg', 0.92);

                        const orient = lay.landscape ? 'landscape' : 'portrait';
                        if (!doc) {
                            doc = new jsPDF({ orientation: orient, unit: 'mm', format: 'a4', compress: true });
                        } else {
                            doc.addPage('a4', orient);
                        }
                        doc.addImage(data, 'JPEG', lay.x, lay.y, lay.dw, lay.dh);
                    }

                    const safeName = s.filename.replace(/[\\/:*?"<>|]+/g, '-');
                    doc.save(safeName + '.pdf');
                    Swal.fire('Berhasil', `PDF berisi ${photos.length} halaman sudah diunduh.`, 'success');
                } catch (err) {
                    Swal.fire('Gagal', err.message || 'Terjadi kesalahan saat membuat PDF.', 'error');
                } finally {
                    setLoading(false);
                }
            }

            /* ---------- Event ---------- */
            // Tempel (Ctrl+V) di mana saja pada halaman
            document.addEventListener('paste', (e) => {
                const items = e.clipboardData ? Array.from(e.clipboardData.items) : [];
                const files = items.filter(it => it.kind === 'file').map(it => it.getAsFile()).filter(Boolean);
                if (!files.length) return; // biarkan paste teks biasa ke input
                e.preventDefault();
                addFiles(files);
            });

            // Seret & lepas
            const dropZone = el('drop_zone');
            ['dragover', 'drop'].forEach(evt => window.addEventListener(evt, e => e.preventDefault()));
            dropZone.addEventListener('dragover', () => dropZone.classList.add('dragover'));
            dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
            dropZone.addEventListener('drop', (e) => {
                dropZone.classList.remove('dragover');
                addFiles(e.dataTransfer.files);
            });

            // Klik untuk pilih file
            dropZone.addEventListener('click', () => el('file_input').click());
            el('file_input').addEventListener('change', function() {
                addFiles(this.files);
                this.value = '';
            });

            // Hapus satu foto
            grid.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn-remove-photo');
                if (!btn || busy) return;
                const id = Number(btn.dataset.id);
                const p = photos.find(x => x.id === id);
                if (p) URL.revokeObjectURL(p.url);
                photos = photos.filter(x => x.id !== id);
                render();
            });

            // Hapus semua
            el('btn_clear').addEventListener('click', () => {
                Swal.fire({
                    title: 'Hapus semua foto?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (!result.isConfirmed) return;
                    photos.forEach(p => URL.revokeObjectURL(p.url));
                    photos = [];
                    render();
                });
            });

            // Ubah urutan dengan seret
            new Sortable(grid, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                filter: '.btn-remove-photo',
                preventOnFilter: false,
                onEnd: () => {
                    const order = Array.from(grid.querySelectorAll('.image-input')).map(n => Number(n.dataset.id));
                    photos.sort((a, b) => order.indexOf(a.id) - order.indexOf(b.id));
                    render();
                }
            });

            // Pengaturan berubah -> simpan & perbarui pratinjau
            ['margin_top', 'margin_bottom', 'margin_left', 'margin_right', 'orientation'].forEach(id => {
                el(id).addEventListener('input', () => {
                    saveSettings();
                    render();
                });
            });

            el('btn_generate').addEventListener('click', generatePdf);

            loadSettings();
            render();
        })();
    </script>
</body>

</html>