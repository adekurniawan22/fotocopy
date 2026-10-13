<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pas Foto</title>
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

        .card-title-sm {
            font-weight: 700;
            font-size: 1.05rem;
            margin-bottom: 1rem;
        }

        .step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background-color: #009ef7;
            color: #fff;
            font-size: 0.8rem;
            margin-right: 0.5rem;
        }

        .section-label {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }

        /* --- Area Drop / Paste --- */
        .drop-zone {
            border: 2px dashed #b5b5c3;
            border-radius: 0.85rem;
            background-color: #f8faff;
            padding: 1.5rem 1rem;
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
            font-size: 2rem;
            color: #009ef7;
        }

        kbd {
            background-color: #181c32;
            font-size: 0.75rem;
        }

        /* --- Warna --- */
        .swatch {
            width: 42px;
            height: 42px;
            border-radius: 0.6rem;
            border: 2px solid #e4e6ef;
            cursor: pointer;
            transition: transform 0.15s ease;
        }

        .swatch:hover {
            transform: scale(1.06);
        }

        .swatch.active {
            border-color: #181c32;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.35);
        }

        .form-control-color {
            width: 42px;
            height: 42px;
            padding: 3px;
            border-radius: 0.6rem;
        }

        /* --- Ukuran --- */
        .size-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.55rem 0.75rem;
            border-radius: 0.6rem;
            background-color: #f5f8fa;
            margin-bottom: 0.5rem;
        }

        .size-row.has-qty {
            background-color: #eef6ff;
        }

        .size-row .size-label {
            font-weight: 600;
        }

        .qty-group {
            width: 130px;
        }

        .qty-group input {
            text-align: center;
        }

        /* --- Editor --- */
        .editor-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1.25rem;
            background-color: #f5f8fa;
            border-radius: 0.65rem;
            min-height: 300px;
        }

        #editor {
            box-shadow: 0 0.3rem 1rem rgba(0, 0, 0, 0.15);
            cursor: grab;
            touch-action: none;
            max-width: 100%;
        }

        #editor.dragging {
            cursor: grabbing;
        }

        .size-tab.active {
            background-color: #009ef7;
            color: #fff;
            border-color: #009ef7;
        }

        .form-range::-webkit-slider-thumb {
            background-color: #009ef7;
        }

        /* --- Pratinjau A4 --- */
        #page_previews {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }

        .page-preview {
            text-align: center;
        }

        .page-preview canvas {
            width: 210px;
            box-shadow: 0 0.3rem 1rem rgba(0, 0, 0, 0.12);
            background-color: #fff;
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

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('merge-image.index') }}" class="btn btn-outline-primary fw-bold">
                    <i class="fas fa-images"></i>
                    <span class="d-none d-sm-inline ms-1">Merge Image</span>
                </a>

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
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary fw-bold px-4">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="bg-white pb-2 pt-5 mb-5 border-bottom">
        <div class="container text-center">
            <h1 class="fw-bolder mb-2 text-dark">Pas Foto</h1>
            <p class="text-muted mb-4">Hapus background, atur posisi, lalu susun di kertas A4 siap cetak.</p>
            <a href="{{ route('home') }}" class="btn btn-sm btn-light mb-4">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Katalog
            </a>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row g-4">

            {{-- ================= KIRI ================= --}}
            <div class="col-lg-5">

                {{-- 1. Foto --}}
                <div class="card card-panel p-4 mb-4">
                    <div class="card-title-sm"><span class="step-badge">1</span>Foto</div>

                    <div class="drop-zone" id="drop_zone">
                        <i class="fas fa-user-circle mb-2"></i>
                        <div class="fw-bold">Tempel foto dengan <kbd>Ctrl</kbd> + <kbd>V</kbd></div>
                        <div class="text-muted fs-7 mt-1">atau seret foto ke sini, atau klik untuk memilih</div>
                        <input type="file" id="file_input" accept="image/*" style="display:none">
                    </div>

                    <div id="ai_status" class="mt-3" style="display:none">
                        <div class="d-flex justify-content-between fs-7 mb-1">
                            <span id="ai_status_text">Memproses...</span>
                            <span id="ai_status_pct"></span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" id="ai_progress"
                                style="width: 100%"></div>
                        </div>
                    </div>
                </div>

                {{-- 2. Warna --}}
                <div class="card card-panel p-4 mb-4">
                    <div class="card-title-sm"><span class="step-badge">2</span>Warna Latar</div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="swatch" data-color="#1565c0" title="Biru"
                            style="background:#1565c0"></button>
                        <button type="button" class="swatch" data-color="#d32f2f" title="Merah"
                            style="background:#d32f2f"></button>
                        <button type="button" class="swatch" data-color="#ffffff" title="Putih"
                            style="background:#ffffff"></button>
                        <input type="color" id="bg_color" class="form-control form-control-color"
                            value="#1565c0" title="Warna lain">
                        <span class="text-muted fs-7 ms-1">Klik kotak terakhir untuk warna lain</span>
                    </div>
                </div>

                {{-- 3. Ukuran & Jumlah --}}
                <div class="card card-panel p-4 mb-4">
                    <div class="card-title-sm"><span class="step-badge">3</span>Ukuran &amp; Jumlah</div>

                    <div id="size_list"></div>

                    <div class="section-label mt-3">Tambah ukuran lain (cm)</div>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="number" id="custom_w" class="form-control form-control-solid" min="1"
                            max="20" step="0.1" placeholder="Lebar">
                        <span class="text-muted">×</span>
                        <input type="number" id="custom_h" class="form-control form-control-solid" min="1"
                            max="28" step="0.1" placeholder="Tinggi">
                        <button type="button" class="btn btn-light text-nowrap" id="btn_add_size">
                            <i class="fas fa-plus me-1"></i> Tambah
                        </button>
                    </div>
                </div>

                {{-- 4. Pengaturan Cetak --}}
                <div class="card card-panel p-4">
                    <div class="card-title-sm"><span class="step-badge">4</span>Pengaturan Cetak</div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="section-label">Margin Kertas</div>
                            <div class="input-group input-group-solid">
                                <input type="number" id="paper_margin" class="form-control form-control-solid"
                                    min="0" max="30" step="0.5" value="3">
                                <span class="input-group-text">mm</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="section-label">Border Putih</div>
                            <div class="input-group input-group-solid">
                                <input type="number" id="white_border" class="form-control form-control-solid"
                                    min="0" max="10" step="0.5" value="2">
                                <span class="input-group-text">mm</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="allow_rotate" checked>
                                <label class="form-check-label" for="allow_rotate">
                                    Boleh putar foto 90° agar hemat kertas
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="section-label">Garis Potong</div>
                            <select id="cut_style" class="form-select form-select-solid">
                                <option value="sangat">Sangat samar</option>
                                <option value="samar" selected>Samar</option>
                                <option value="jelas">Jelas</option>
                                <option value="tidak">Tanpa garis potong</option>
                            </select>
                            <div class="text-muted fs-7 mt-1">Garis berada di luar border putih.</div>
                        </div>
                        <div class="col-12">
                            <div class="section-label">Nama File PDF</div>
                            <input type="text" id="file_name" class="form-control form-control-solid"
                                placeholder="Contoh: pasfoto-budi">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= KANAN ================= --}}
            <div class="col-lg-7">

                {{-- Atur Posisi --}}
                <div class="card card-panel p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="card-title-sm mb-0"><i class="fas fa-crop-alt text-primary me-2"></i>Atur
                            Posisi</div>
                        <div class="btn-group btn-group-sm" id="size_tabs"></div>
                    </div>

                    <div class="editor-wrap mb-3">
                        <canvas id="editor"></canvas>
                    </div>

                    <div id="edge_warning" class="alert alert-warning fs-7 py-2" style="display:none">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <span id="edge_warning_text"></span>
                    </div>

                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-primary btn-sm" id="btn_auto" disabled>
                            <i class="fas fa-magic me-1"></i> Posisikan Otomatis
                        </button>
                        <span class="text-muted fs-7 align-self-center">Seret foto untuk menggeser, scroll untuk
                            zoom</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="section-label d-flex justify-content-between">
                                <span>Zoom</span><span class="text-muted fw-normal" id="zoom_val">100%</span>
                            </label>
                            <input type="range" class="form-range" id="zoom" min="40" max="500"
                                step="1" value="100" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="section-label d-flex justify-content-between">
                                <span>Luruskan (putar)</span><span class="text-muted fw-normal"
                                    id="rotate_val">0°</span>
                            </label>
                            <input type="range" class="form-range" id="rotate" min="-15" max="15"
                                step="0.5" value="0" disabled>
                        </div>
                    </div>

                    <details class="mt-2">
                        <summary class="text-muted fs-7" style="cursor:pointer">Patokan posisi otomatis</summary>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="section-label d-flex justify-content-between">
                                    <span>Besar kepala</span><span class="text-muted fw-normal"
                                        id="head_pct_val"></span>
                                </label>
                                <input type="range" class="form-range" id="head_pct" min="30" max="75"
                                    step="1" value="50">
                            </div>
                            <div class="col-md-6">
                                <label class="section-label d-flex justify-content-between">
                                    <span>Jarak atas kepala</span><span class="text-muted fw-normal"
                                        id="top_pct_val"></span>
                                </label>
                                <input type="range" class="form-range" id="top_pct" min="2" max="25"
                                    step="1" value="10">
                            </div>
                        </div>
                        <div class="text-muted fs-7">Diukur dari tinggi pas foto. Garis putus-putus di foto
                            menunjukkan patokan ini. Pengaturan tersimpan otomatis.</div>
                    </details>
                </div>

                {{-- Pratinjau Cetak --}}
                <div class="card card-panel p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="card-title-sm mb-0"><i class="fas fa-print text-primary me-2"></i>Pratinjau
                            Cetak (A4)</div>
                        <span class="text-muted fs-7" id="layout_summary"></span>
                    </div>

                    <div id="page_previews"></div>
                    <div id="empty_preview" class="text-center text-muted py-4">
                        Masukkan foto dan isi jumlah ukuran untuk melihat susunan cetak.
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-4 mt-4 flex-wrap gap-2">
                        <span class="text-muted fs-7">
                            <i class="fas fa-info-circle me-1"></i>Saat mencetak, pilih skala <b>100% / Actual
                                size</b>.
                        </span>
                        <div class="d-flex align-items-center">
                            <span id="loading-text" class="text-muted me-3"></span>
                            <button type="button" class="btn btn-primary" id="btn_generate" disabled>
                                <span class="indicator-label"><i class="fas fa-file-pdf me-1"></i> Buat PDF</span>
                                <span class="indicator-progress" style="display:none">
                                    Please wait... <span
                                        class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        (function() {
            const BG_REMOVAL_URL = 'https://cdn.jsdelivr.net/npm/@imgly/background-removal@1.7.0/+esm';
            const STORAGE_KEY = 'pasFotoSettings';
            const A4_W = 210;
            const A4_H = 297;
            const PDF_DPI = 300;
            const PREVIEW_PX_PER_MM = 2;
            const MAX_INPUT_SIZE = 1600; // foto diperkecil dulu agar AI lebih cepat

            const DEFAULT_SIZES = [
                { id: '2x3', w: 2, h: 3 },
                { id: '3x4', w: 3, h: 4 },
                { id: '4x6', w: 4, h: 6 }
            ];

            const el = (id) => document.getElementById(id);
            const editor = el('editor');

            let bgModule = null;
            let cutout = null; // foto tanpa background (HTMLImageElement)
            let head = null; // { cx, cy, h } posisi kepala dalam piksel foto
            let edges = null; // bagian foto yang menyentuh tepi gambar
            let baseK = 0; // skala hasil posisi otomatis
            let pos = { ox: 0, oy: 0.35 }; // posisi kepala dalam satuan "tinggi pas foto"
            let sizes = [];
            let activeSizeId = '3x4';
            let busy = false;
            let previewTimer = null;

            /* =============== Pengaturan =============== */
            function num(id, fallback = 0) {
                const v = parseFloat(el(id).value);
                return isNaN(v) ? fallback : Math.max(0, v);
            }

            function getSettings() {
                return {
                    color: el('bg_color').value,
                    margin: num('paper_margin', 3),
                    border: num('white_border', 2),
                    cutStyle: el('cut_style').value,
                    allowRotate: el('allow_rotate').checked,
                    headPct: num('head_pct', 50),
                    topPct: num('top_pct', 10),
                    zoom: num('zoom', 100),
                    rotate: parseFloat(el('rotate').value) || 0,
                    filename: el('file_name').value.trim() || 'pas-foto'
                };
            }

            function saveSettings() {
                const s = getSettings();
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify({
                        color: s.color,
                        margin: s.margin,
                        border: s.border,
                        cutStyle: s.cutStyle,
                        allowRotate: s.allowRotate,
                        headPct: s.headPct,
                        topPct: s.topPct,
                        customSizes: sizes.filter(x => x.custom).map(x => ({ w: x.w, h: x.h }))
                    }));
                } catch (e) {}
            }

            function loadSettings() {
                let saved = null;
                try {
                    saved = JSON.parse(localStorage.getItem(STORAGE_KEY));
                } catch (e) {}

                sizes = DEFAULT_SIZES.map(s => ({ ...s, qty: 0, custom: false }));
                if (!saved) return;

                if (saved.color) el('bg_color').value = saved.color;
                if (saved.margin !== undefined) el('paper_margin').value = saved.margin;
                if (saved.border !== undefined) el('white_border').value = saved.border;
                if (saved.cutStyle) el('cut_style').value = saved.cutStyle;
                if (saved.allowRotate !== undefined) el('allow_rotate').checked = saved.allowRotate;
                if (saved.headPct) el('head_pct').value = saved.headPct;
                if (saved.topPct) el('top_pct').value = saved.topPct;
                (saved.customSizes || []).forEach(c => addSize(c.w, c.h, false));
            }

            /* =============== Ukuran =============== */
            function sizeLabel(s) {
                const f = (v) => String(v).replace('.', ',');
                return `${f(s.w)} × ${f(s.h)} cm`;
            }

            function addSize(w, h, notify = true) {
                const id = `${w}x${h}`;
                if (sizes.some(s => s.id === id)) {
                    if (notify) Swal.fire('Sudah ada', 'Ukuran ini sudah ada di daftar.', 'info');
                    return;
                }
                sizes.push({ id, w, h, qty: 0, custom: true });
            }

            function renderSizes() {
                el('size_list').innerHTML = sizes.map(s => `
                    <div class="size-row ${s.qty > 0 ? 'has-qty' : ''}">
                        <span class="size-label">${sizeLabel(s)}</span>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group input-group-sm qty-group">
                                <button class="btn btn-light qty-btn" type="button" data-id="${s.id}" data-step="-1"><i class="fas fa-minus"></i></button>
                                <input type="number" class="form-control qty-input" min="0" max="200" value="${s.qty}" data-id="${s.id}">
                                <button class="btn btn-light qty-btn" type="button" data-id="${s.id}" data-step="1"><i class="fas fa-plus"></i></button>
                            </div>
                            ${s.custom ? `<button type="button" class="btn btn-sm btn-light text-danger remove-size" data-id="${s.id}" title="Hapus ukuran"><i class="fas fa-trash"></i></button>` : ''}
                        </div>
                    </div>`).join('');
                renderTabs();
            }

            function renderTabs() {
                const used = sizes.filter(s => s.qty > 0);
                const list = used.length ? used : sizes;
                if (!list.some(s => s.id === activeSizeId)) activeSizeId = list[0].id;

                el('size_tabs').innerHTML = list.map(s => `
                    <button type="button" class="btn btn-outline-secondary size-tab ${s.id === activeSizeId ? 'active' : ''}" data-id="${s.id}">
                        ${sizeLabel(s).replace(' cm', '')}
                    </button>`).join('');
            }

            /* =============== Hapus background (AI) =============== */
            function setAiStatus(text, pct = null) {
                el('ai_status').style.display = text ? 'block' : 'none';
                el('ai_status_text').textContent = text || '';
                el('ai_status_pct').textContent = pct === null ? '' : pct + '%';
                el('ai_progress').style.width = (pct === null ? 100 : pct) + '%';
                el('ai_progress').classList.toggle('progress-bar-animated', pct === null);
            }

            function loadImage(src) {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.onload = () => resolve(img);
                    img.onerror = () => reject(new Error('Gagal membaca gambar'));
                    img.src = src;
                });
            }

            // Gambar ulang lewat canvas: rotasi foto HP ikut benar & ukurannya diperkecil
            async function normalizeImage(file) {
                const url = URL.createObjectURL(file);
                try {
                    const img = await loadImage(url);
                    const scale = Math.min(1, MAX_INPUT_SIZE / Math.max(img.naturalWidth, img.naturalHeight));
                    const c = document.createElement('canvas');
                    c.width = Math.round(img.naturalWidth * scale);
                    c.height = Math.round(img.naturalHeight * scale);
                    c.getContext('2d').drawImage(img, 0, 0, c.width, c.height);
                    return await new Promise(r => c.toBlob(r, 'image/jpeg', 0.95));
                } finally {
                    URL.revokeObjectURL(url);
                }
            }

            async function processFile(file) {
                if (busy) return;
                if (!file || !(file.type || '').startsWith('image/')) {
                    Swal.fire('Bukan gambar', 'Pilih file foto (JPG/PNG).', 'warning');
                    return;
                }

                busy = true;
                updateButtons();
                try {
                    setAiStatus('Menyiapkan foto...');
                    const input = await normalizeImage(file);

                    if (!bgModule) {
                        setAiStatus('Memuat AI penghapus background...');
                        bgModule = await import(BG_REMOVAL_URL);
                    }

                    const result = await bgModule.removeBackground(input, {
                        model: 'isnet_fp16',
                        output: { format: 'image/png' },
                        progress: (key, current, total) => {
                            if (key.startsWith('fetch') && total) {
                                setAiStatus('Mengunduh model AI (sekali saja)...', Math.round(current / total * 100));
                            } else {
                                setAiStatus('Menghapus background...');
                            }
                        }
                    });

                    setAiStatus('Mengatur posisi...');
                    if (cutout) URL.revokeObjectURL(cutout.src);
                    cutout = await loadImage(URL.createObjectURL(result));
                    analyzeCutout();
                    autoPosition();
                    setAiStatus('');
                    el('drop_zone').querySelector('.fw-bold').innerHTML =
                        'Ganti foto: tempel <kbd>Ctrl</kbd> + <kbd>V</kbd>';
                } catch (err) {
                    console.error(err);
                    setAiStatus('');
                    Swal.fire('Gagal', 'Background tidak bisa dihapus. Periksa koneksi internet lalu coba lagi.', 'error');
                } finally {
                    busy = false;
                    updateButtons();
                    refreshAll();
                }
            }

            /* =============== Deteksi posisi kepala =============== */
            function analyzeCutout() {
                const iw = cutout.naturalWidth;
                const ih = cutout.naturalHeight;
                const SW = 200;
                const sf = SW / iw;
                const SH = Math.max(1, Math.round(ih * sf));

                const c = document.createElement('canvas');
                c.width = SW;
                c.height = SH;
                const ctx = c.getContext('2d');
                ctx.drawImage(cutout, 0, 0, SW, SH);
                const data = ctx.getImageData(0, 0, SW, SH).data;

                const L = new Array(SH).fill(-1);
                const R = new Array(SH).fill(-1);
                const W = new Array(SH).fill(0);
                for (let y = 0; y < SH; y++) {
                    for (let x = 0; x < SW; x++) {
                        if (data[(y * SW + x) * 4 + 3] > 128) {
                            if (L[y] < 0) L[y] = x;
                            R[y] = x;
                        }
                    }
                    if (L[y] >= 0) W[y] = R[y] - L[y] + 1;
                }

                let top = W.findIndex(w => w >= 3);
                let bottom = SH - 1 - [...W].reverse().findIndex(w => w >= 3);
                if (top < 0) {
                    head = { cx: iw / 2, cy: ih * 0.3, h: ih * 0.4 };
                    edges = null;
                    return;
                }

                // Cari bahu: lebar tiba-tiba melonjak dibanding lebar kepala
                let headW = 0;
                let shoulderY = -1;
                for (let y = top; y <= bottom; y++) {
                    if (headW > 0 && y - top > headW * 0.7 && W[y] > headW * 1.5) {
                        shoulderY = y;
                        break;
                    }
                    if (W[y] > headW) headW = W[y];
                }

                // Leher = bagian tersempit antara kepala dan bahu
                let neckY;
                if (shoulderY > 0) {
                    let minW = Infinity;
                    neckY = shoulderY;
                    for (let y = Math.round(top + headW * 0.6); y < shoulderY; y++) {
                        if (W[y] > 0 && W[y] < minW) {
                            minW = W[y];
                            neckY = y;
                        }
                    }
                } else {
                    neckY = top + headW * 1.3;
                }

                let headH = neckY - top;
                if (headH < headW * 0.9 || headH > headW * 1.8) headH = headW * 1.3;
                headH = Math.min(headH, bottom - top + 1);

                // Titik tengah kepala (patokan kiri-kanan)
                let sum = 0, n = 0;
                for (let y = top; y < top + headH * 0.8 && y <= bottom; y++) {
                    if (W[y] > 0) {
                        sum += (L[y] + R[y]) / 2;
                        n++;
                    }
                }
                const cx = n ? sum / n : SW / 2;

                head = {
                    cx: cx / sf,
                    cy: (top + headH / 2) / sf,
                    h: headH / sf
                };

                edges = {
                    bottom: W[SH - 1] > 0,
                    left: L.some(v => v === 0),
                    right: R.some(v => v === SW - 1)
                };
            }

            function autoPosition() {
                if (!cutout || !head) return;
                const s = getSettings();
                const target = s.headPct / 100;
                baseK = target / head.h;
                pos = { ox: 0, oy: s.topPct / 100 + target / 2 };
                el('zoom').value = 100;
                el('rotate').value = 0;
                updateSliderLabels();
                refreshAll();
            }

            /* =============== Menggambar pas foto =============== */
            // Hpx = tinggi pas foto dalam piksel; semua posisi relatif terhadap tinggi
            function drawPhoto(ctx, Wpx, Hpx, s) {
                ctx.fillStyle = s.color;
                ctx.fillRect(0, 0, Wpx, Hpx);
                if (!cutout || !head) return;

                const k = baseK * s.zoom / 100 * Hpx;
                ctx.save();
                ctx.beginPath();
                ctx.rect(0, 0, Wpx, Hpx);
                ctx.clip();
                ctx.translate(Wpx / 2 + pos.ox * Hpx, pos.oy * Hpx);
                ctx.rotate(s.rotate * Math.PI / 180);
                ctx.scale(k, k);
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(cutout, -head.cx, -head.cy);
                ctx.restore();
            }

            function activeSize() {
                return sizes.find(x => x.id === activeSizeId) || sizes[0];
            }

            function renderEditor() {
                const s = getSettings();
                const size = activeSize();
                const cssH = Math.min(380, window.innerWidth < 576 ? 300 : 380);
                const cssW = cssH * size.w / size.h;
                const dpr = window.devicePixelRatio || 1;

                editor.style.width = cssW + 'px';
                editor.style.height = cssH + 'px';
                editor.width = Math.round(cssW * dpr);
                editor.height = Math.round(cssH * dpr);

                const ctx = editor.getContext('2d');
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                drawPhoto(ctx, cssW, cssH, s);

                // Garis bantu
                ctx.save();
                ctx.setLineDash([5, 5]);
                ctx.lineWidth = 1;
                ctx.strokeStyle = 'rgba(255,255,255,0.8)';
                ctx.beginPath();
                ctx.moveTo(cssW / 2, 0);
                ctx.lineTo(cssW / 2, cssH);
                const yTop = cssH * s.topPct / 100;
                const yNeck = cssH * (s.topPct + s.headPct) / 100;
                ctx.moveTo(0, yTop);
                ctx.lineTo(cssW, yTop);
                ctx.moveTo(0, yNeck);
                ctx.lineTo(cssW, yNeck);
                ctx.stroke();
                ctx.restore();

                if (!cutout) {
                    ctx.fillStyle = 'rgba(255,255,255,0.9)';
                    ctx.font = '14px Poppins, sans-serif';
                    ctx.textAlign = 'center';
                    ctx.fillText('Belum ada foto', cssW / 2, cssH / 2);
                }

                checkEdges(cssW / cssH, s);
            }

            // Peringatan bila potongan badan di tepi foto asli terlihat di dalam pas foto
            function checkEdges(ratio, s) {
                const msgs = [];
                if (cutout && head && edges) {
                    const k = baseK * s.zoom / 100;
                    const left = ratio / 2 + pos.ox - head.cx * k;
                    const right = ratio / 2 + pos.ox + (cutout.naturalWidth - head.cx) * k;
                    const bottom = pos.oy + (cutout.naturalHeight - head.cy) * k;
                    if (edges.bottom && bottom < 0.995) msgs.push('bagian bawah badan');
                    if (edges.left && left > 0.005) msgs.push('sisi kiri');
                    if (edges.right && right < ratio - 0.005) msgs.push('sisi kanan');
                }
                el('edge_warning').style.display = msgs.length ? 'block' : 'none';
                el('edge_warning_text').textContent = msgs.length ?
                    `Potongan foto asli terlihat di ${msgs.join(', ')}. Perbesar zoom atau geser foto.` : '';
            }

            /* =============== Susunan di A4 (MaxRects, boleh diputar) =============== */
            const EPS = 0.01;

            function containsRect(b, a) {
                return a.x >= b.x - EPS && a.y >= b.y - EPS &&
                    a.x + a.w <= b.x + b.w + EPS && a.y + a.h <= b.y + b.h + EPS;
            }

            // Kurangi ruang kosong setelah satu foto ditempatkan
            function splitFree(free, r) {
                const out = [];
                for (const f of free) {
                    const overlap = r.x < f.x + f.w - EPS && r.x + r.w > f.x + EPS &&
                        r.y < f.y + f.h - EPS && r.y + r.h > f.y + EPS;
                    if (!overlap) {
                        out.push(f);
                        continue;
                    }
                    if (r.x > f.x + EPS) out.push({ x: f.x, y: f.y, w: r.x - f.x, h: f.h });
                    if (r.x + r.w < f.x + f.w - EPS) out.push({ x: r.x + r.w, y: f.y, w: f.x + f.w - r.x - r.w, h: f.h });
                    if (r.y > f.y + EPS) out.push({ x: f.x, y: f.y, w: f.w, h: r.y - f.y });
                    if (r.y + r.h < f.y + f.h - EPS) out.push({ x: f.x, y: r.y + r.h, w: f.w, h: f.y + f.h - r.y - r.h });
                }
                return out.filter((a, i) => !out.some((b, j) => j !== i && containsRect(b, a) &&
                    (!containsRect(a, b) || j < i)));
            }

            function packOnePage(items, s, heuristic, allowRotate) {
                let free = [{ x: s.margin, y: s.margin, w: A4_W - s.margin * 2, h: A4_H - s.margin * 2 }];
                const placed = [];
                const rest = [];

                for (const it of items) {
                    const orients = [[it.w, it.h, false]];
                    if (allowRotate && Math.abs(it.w - it.h) > EPS) orients.push([it.h, it.w, true]);

                    let best = null;
                    for (const f of free) {
                        for (const [w, h, rot] of orients) {
                            if (w > f.w + EPS || h > f.h + EPS) continue;
                            let s1, s2;
                            if (heuristic === 'top') {
                                s1 = f.y + h;
                                s2 = f.x;
                            } else if (heuristic === 'side') {
                                s1 = Math.min(f.w - w, f.h - h);
                                s2 = Math.max(f.w - w, f.h - h);
                            } else {
                                s1 = f.w * f.h - w * h;
                                s2 = Math.min(f.w - w, f.h - h);
                            }
                            const better = !best || s1 < best.s1 - EPS ||
                                (Math.abs(s1 - best.s1) <= EPS && (s2 < best.s2 - EPS ||
                                    (Math.abs(s2 - best.s2) <= EPS && !rot && best.rot)));
                            if (better) best = { x: f.x, y: f.y, w, h, rot, s1, s2 };
                        }
                    }

                    if (!best) {
                        rest.push(it);
                        continue;
                    }
                    placed.push({ size: it.size, x: best.x, y: best.y, cw: best.w, ch: best.h, rot: best.rot });
                    free = splitFree(free, best);
                }
                return { placed, rest };
            }

            function packAll(items, s, heuristic, allowRotate) {
                const pages = [];
                let remaining = items;
                while (remaining.length) {
                    const { placed, rest } = packOnePage(remaining, s, heuristic, allowRotate);
                    if (!placed.length) return null;
                    pages.push(placed);
                    remaining = rest;
                }
                return pages;
            }

            function scoreLayout(pages) {
                const last = pages[pages.length - 1];
                return [
                    pages.length,
                    Math.round(Math.max(...last.map(c => c.y + c.ch)) * 10),
                    pages.flat().filter(c => c.rot).length
                ];
            }

            function buildLayout(s) {
                const items = [];
                sizes.forEach(size => {
                    for (let i = 0; i < size.qty; i++) {
                        items.push({ size, w: size.w * 10 + s.border * 2, h: size.h * 10 + s.border * 2 });
                    }
                });
                if (!items.length) return { pages: [], total: 0 };

                const maxW = A4_W - s.margin * 2;
                const maxH = A4_H - s.margin * 2;
                const fits = (it) => (it.w <= maxW + EPS && it.h <= maxH + EPS) ||
                    (s.allowRotate && it.h <= maxW + EPS && it.w <= maxH + EPS);
                const tooBig = items.find(it => !fits(it));
                if (tooBig) return { error: `Ukuran ${sizeLabel(tooBig.size)} terlalu besar untuk kertas A4.` };

                // Coba beberapa cara menyusun, ambil yang paling hemat kertas
                const orders = [
                    (a, b) => b.w * b.h - a.w * a.h || b.h - a.h,
                    (a, b) => Math.max(b.w, b.h) - Math.max(a.w, a.h) || b.w * b.h - a.w * a.h,
                    (a, b) => b.h - a.h || b.w - a.w
                ];
                let best = null;
                for (const order of orders) {
                    const sorted = [...items].sort(order);
                    for (const heuristic of ['top', 'side', 'area']) {
                        for (const rot of s.allowRotate ? [false, true] : [false]) {
                            const pages = packAll(sorted, s, heuristic, rot);
                            if (!pages) continue;
                            const score = scoreLayout(pages);
                            const better = !best || score[0] < best.score[0] ||
                                (score[0] === best.score[0] && (score[1] < best.score[1] ||
                                    (score[1] === best.score[1] && score[2] < best.score[2])));
                            if (better) best = { pages, score };
                        }
                    }
                }
                if (!best) return { error: 'Foto tidak bisa disusun di kertas A4.' };
                return { pages: best.pages, total: items.length };
            }

            // Warna & ketebalan garis potong (warna abu-abu: makin tinggi makin samar)
            const CUT_STYLES = {
                sangat: { gray: 232, width: 0.08 },
                samar: { gray: 210, width: 0.1 },
                jelas: { gray: 150, width: 0.15 }
            };

            function renderPhotoCanvas(size, pxPerMm, s, rotated = false) {
                const c = document.createElement('canvas');
                c.width = Math.max(1, Math.round(size.w * 10 * pxPerMm));
                c.height = Math.max(1, Math.round(size.h * 10 * pxPerMm));
                drawPhoto(c.getContext('2d'), c.width, c.height, s);
                if (!rotated) return c;

                // Putar 90° searah jarum jam
                const r = document.createElement('canvas');
                r.width = c.height;
                r.height = c.width;
                const ctx = r.getContext('2d');
                ctx.translate(r.width, 0);
                ctx.rotate(Math.PI / 2);
                ctx.drawImage(c, 0, 0);
                return r;
            }

            function renderPreviews() {
                const s = getSettings();
                const wrap = el('page_previews');
                wrap.innerHTML = '';

                const layout = buildLayout(s);
                const hasQty = sizes.some(x => x.qty > 0);
                el('empty_preview').style.display = cutout && hasQty ? 'none' : 'block';

                if (layout.error) {
                    el('layout_summary').innerHTML = `<span class="text-danger">${layout.error}</span>`;
                    return;
                }
                el('layout_summary').textContent = layout.total ?
                    `${layout.total} foto · ${layout.pages.length} lembar A4` : '';
                if (!cutout || !layout.total) return;

                const P = PREVIEW_PX_PER_MM;
                const cache = {};
                layout.pages.forEach((page, i) => {
                    const c = document.createElement('canvas');
                    c.width = A4_W * P;
                    c.height = A4_H * P;
                    const ctx = c.getContext('2d');
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, c.width, c.height);

                    const cut = CUT_STYLES[s.cutStyle];
                    page.forEach(cell => {
                        const key = cell.size.id + (cell.rot ? '_r' : '');
                        if (!cache[key]) cache[key] = renderPhotoCanvas(cell.size, P, s, cell.rot);
                        ctx.drawImage(cache[key], (cell.x + s.border) * P, (cell.y + s.border) * P,
                            (cell.cw - s.border * 2) * P, (cell.ch - s.border * 2) * P);
                        if (cut) {
                            ctx.strokeStyle = `rgb(${cut.gray},${cut.gray},${cut.gray})`;
                            ctx.lineWidth = Math.max(0.6, cut.width * P);
                            ctx.strokeRect(cell.x * P, cell.y * P, cell.cw * P, cell.ch * P);
                        }
                    });

                    const item = document.createElement('div');
                    item.className = 'page-preview';
                    item.appendChild(c);
                    const label = document.createElement('div');
                    label.className = 'text-muted fs-7 mt-2';
                    label.textContent = `Lembar ${i + 1}`;
                    item.appendChild(label);
                    wrap.appendChild(item);
                });
            }

            function schedulePreviews() {
                clearTimeout(previewTimer);
                previewTimer = setTimeout(renderPreviews, 150);
            }

            /* =============== Buat PDF =============== */
            async function generatePdf() {
                if (busy || !cutout) return;
                const s = getSettings();
                const layout = buildLayout(s);
                if (layout.error) {
                    Swal.fire('Tidak muat', layout.error, 'error');
                    return;
                }
                if (!layout.total) {
                    Swal.fire('Jumlah kosong', 'Isi jumlah cetak minimal satu ukuran.', 'warning');
                    return;
                }

                busy = true;
                updateButtons(true);
                el('loading-text').textContent = 'Membuat PDF...';
                await new Promise(r => setTimeout(r, 30));

                try {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4', compress: true });
                    const pxPerMm = PDF_DPI / 25.4;
                    const images = {};
                    const cut = CUT_STYLES[s.cutStyle];

                    layout.pages.forEach((page, i) => {
                        if (i > 0) doc.addPage('a4', 'portrait');
                        page.forEach(cell => {
                            const key = cell.size.id + (cell.rot ? '_r' : '');
                            if (!images[key]) {
                                images[key] = renderPhotoCanvas(cell.size, pxPerMm, s, cell.rot)
                                    .toDataURL('image/jpeg', 0.95);
                            }
                            // alias yang sama -> foto disimpan sekali saja di PDF
                            doc.addImage(images[key], 'JPEG', cell.x + s.border, cell.y + s.border,
                                cell.cw - s.border * 2, cell.ch - s.border * 2, 'pasfoto_' + key);
                        });

                        // Garis potong digambar setelah semua foto
                        if (cut) {
                            doc.setDrawColor(cut.gray);
                            doc.setLineWidth(cut.width);
                            page.forEach(cell => doc.rect(cell.x, cell.y, cell.cw, cell.ch, 'S'));
                        }
                    });

                    doc.save(s.filename.replace(/[\\/:*?"<>|]+/g, '-') + '.pdf');
                    Swal.fire('Berhasil', `PDF ${layout.pages.length} lembar sudah diunduh.`, 'success');
                } catch (err) {
                    console.error(err);
                    Swal.fire('Gagal', 'Terjadi kesalahan saat membuat PDF.', 'error');
                } finally {
                    busy = false;
                    el('loading-text').textContent = '';
                    updateButtons(false);
                }
            }

            /* =============== Tampilan umum =============== */
            function updateButtons(generating = false) {
                const btn = el('btn_generate');
                btn.querySelector('.indicator-label').style.display = generating ? 'none' : 'inline';
                btn.querySelector('.indicator-progress').style.display = generating ? 'inline' : 'none';
                const ready = !!cutout && !busy;
                btn.disabled = !ready || !sizes.some(x => x.qty > 0);
                el('btn_auto').disabled = !ready;
                el('zoom').disabled = !ready;
                el('rotate').disabled = !ready;
            }

            function updateSliderLabels() {
                el('zoom_val').textContent = el('zoom').value + '%';
                el('rotate_val').textContent = el('rotate').value + '°';
                el('head_pct_val').textContent = el('head_pct').value + '%';
                el('top_pct_val').textContent = el('top_pct').value + '%';
            }

            function updateSwatches() {
                const color = el('bg_color').value.toLowerCase();
                document.querySelectorAll('.swatch').forEach(sw => {
                    sw.classList.toggle('active', sw.dataset.color === color);
                });
            }

            function refreshAll() {
                updateSliderLabels();
                updateSwatches();
                updateButtons();
                renderEditor();
                schedulePreviews();
            }

            /* =============== Event =============== */
            // Tempel foto
            document.addEventListener('paste', (e) => {
                const items = e.clipboardData ? Array.from(e.clipboardData.items) : [];
                const file = items.filter(it => it.kind === 'file' && it.type.startsWith('image/'))
                    .map(it => it.getAsFile())[0];
                if (!file) return;
                e.preventDefault();
                processFile(file);
            });

            // Seret & pilih file
            const dropZone = el('drop_zone');
            ['dragover', 'drop'].forEach(evt => window.addEventListener(evt, e => e.preventDefault()));
            dropZone.addEventListener('dragover', () => dropZone.classList.add('dragover'));
            dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
            dropZone.addEventListener('drop', (e) => {
                dropZone.classList.remove('dragover');
                processFile(e.dataTransfer.files[0]);
            });
            dropZone.addEventListener('click', () => el('file_input').click());
            el('file_input').addEventListener('change', function() {
                processFile(this.files[0]);
                this.value = '';
            });

            // Warna
            document.querySelectorAll('.swatch').forEach(sw => {
                sw.addEventListener('click', () => {
                    el('bg_color').value = sw.dataset.color;
                    saveSettings();
                    refreshAll();
                });
            });
            el('bg_color').addEventListener('input', () => {
                saveSettings();
                refreshAll();
            });

            // Jumlah & ukuran
            function setQty(id, qty) {
                const size = sizes.find(x => x.id === id);
                if (!size) return;
                size.qty = Math.max(0, Math.min(200, Math.round(qty) || 0));
                if (size.qty > 0) activeSizeId = id;
                renderSizes();
                refreshAll();
            }

            el('size_list').addEventListener('click', (e) => {
                const btn = e.target.closest('.qty-btn');
                if (btn) {
                    const size = sizes.find(x => x.id === btn.dataset.id);
                    setQty(btn.dataset.id, size.qty + Number(btn.dataset.step));
                    return;
                }
                const rm = e.target.closest('.remove-size');
                if (rm) {
                    sizes = sizes.filter(x => x.id !== rm.dataset.id);
                    saveSettings();
                    renderSizes();
                    refreshAll();
                }
            });
            el('size_list').addEventListener('change', (e) => {
                if (e.target.classList.contains('qty-input')) setQty(e.target.dataset.id, parseFloat(e.target.value));
            });

            el('btn_add_size').addEventListener('click', () => {
                const w = Math.round(parseFloat(el('custom_w').value) * 10) / 10;
                const h = Math.round(parseFloat(el('custom_h').value) * 10) / 10;
                if (!(w > 0) || !(h > 0)) {
                    Swal.fire('Ukuran belum diisi', 'Isi lebar dan tinggi dalam cm.', 'warning');
                    return;
                }
                addSize(w, h);
                el('custom_w').value = '';
                el('custom_h').value = '';
                saveSettings();
                renderSizes();
                refreshAll();
            });

            el('size_tabs').addEventListener('click', (e) => {
                const tab = e.target.closest('.size-tab');
                if (!tab) return;
                activeSizeId = tab.dataset.id;
                renderTabs();
                renderEditor();
            });

            // Pengaturan cetak
            ['paper_margin', 'white_border', 'cut_style', 'allow_rotate'].forEach(id => {
                el(id).addEventListener('input', () => {
                    saveSettings();
                    schedulePreviews();
                });
            });

            // Slider
            ['zoom', 'rotate'].forEach(id => el(id).addEventListener('input', refreshAll));
            ['head_pct', 'top_pct'].forEach(id => {
                el(id).addEventListener('input', () => {
                    saveSettings();
                    updateSliderLabels();
                    renderEditor();
                });
                el(id).addEventListener('change', autoPosition);
            });
            el('btn_auto').addEventListener('click', autoPosition);

            // Geser foto di editor
            let drag = null;
            editor.addEventListener('pointerdown', (e) => {
                if (!cutout || busy) return;
                drag = { x: e.clientX, y: e.clientY };
                editor.setPointerCapture(e.pointerId);
                editor.classList.add('dragging');
            });
            editor.addEventListener('pointermove', (e) => {
                if (!drag) return;
                const h = editor.getBoundingClientRect().height;
                pos.ox += (e.clientX - drag.x) / h;
                pos.oy += (e.clientY - drag.y) / h;
                drag = { x: e.clientX, y: e.clientY };
                renderEditor();
            });
            const endDrag = () => {
                if (!drag) return;
                drag = null;
                editor.classList.remove('dragging');
                schedulePreviews();
            };
            editor.addEventListener('pointerup', endDrag);
            editor.addEventListener('pointercancel', endDrag);

            // Zoom dengan scroll
            editor.addEventListener('wheel', (e) => {
                if (!cutout || busy) return;
                e.preventDefault();
                const z = el('zoom');
                z.value = Math.max(+z.min, Math.min(+z.max, +z.value + (e.deltaY < 0 ? 5 : -5)));
                refreshAll();
            }, { passive: false });

            window.addEventListener('resize', renderEditor);
            el('btn_generate').addEventListener('click', generatePdf);

            // Mulai
            loadSettings();
            renderSizes();
            refreshAll();
        })();
    </script>
</body>

</html>