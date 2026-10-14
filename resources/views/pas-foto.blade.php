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
            padding: 1.25rem 1rem;
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
            font-size: 1.8rem;
            color: #009ef7;
        }

        kbd {
            background-color: #181c32;
            font-size: 0.75rem;
        }

        /* --- Daftar Foto --- */
        #photo_list {
            max-height: 340px;
            overflow-y: auto;
        }

        .photo-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.6rem;
            border-radius: 0.6rem;
            background-color: #f5f8fa;
            border: 2px solid transparent;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .photo-item:hover {
            background-color: #eef3f7;
        }

        .photo-item.active {
            border-color: #009ef7;
            background-color: #eef6ff;
        }

        .photo-thumb {
            width: 45px;
            height: 60px;
            border-radius: 0.3rem;
            box-shadow: 0 0.1rem 0.5rem rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
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
            position: relative;
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

        .editor-overlay {
            position: absolute;
            left: 50%;
            bottom: 1.5rem;
            transform: translateX(-50%);
            background-color: rgba(24, 28, 50, 0.8);
            color: #fff;
            font-size: 0.8rem;
            padding: 0.35rem 0.8rem;
            border-radius: 2rem;
            white-space: nowrap;
        }

        .size-tab.active {
            background-color: #009ef7;
            color: #fff;
            border-color: #009ef7;
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
                        <i class="fas fa-user-plus mb-2"></i>
                        <div class="fw-bold">Tempel foto dengan <kbd>Ctrl</kbd> + <kbd>V</kbd></div>
                        <div class="text-muted fs-7 mt-1">atau seret beberapa foto sekaligus, atau klik untuk memilih
                        </div>
                        <input type="file" id="file_input" accept="image/*" multiple style="display:none">
                    </div>

                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="auto_remove" checked>
                        <label class="form-check-label" for="auto_remove">
                            Hapus background otomatis untuk foto baru
                        </label>
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

                    <div id="photo_list" class="mt-3"></div>
                </div>

                {{-- 2. Foto Terpilih --}}
                <div class="card card-panel p-4 mb-4">
                    <div class="card-title-sm"><span class="step-badge">2</span>Pengaturan <span
                            id="active_title">Foto</span></div>

                    <div id="no_photo_note" class="text-muted fs-7">Tambahkan foto terlebih dahulu.</div>

                    <div id="photo_settings" style="display:none">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="remove_bg">
                            <label class="form-check-label fw-semibold" for="remove_bg">Hapus background</label>
                            <span class="text-muted fs-7 ms-1" id="remove_bg_status"></span>
                        </div>

                        <div id="color_section" class="mb-4">
                            <div class="section-label">Warna Latar</div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <button type="button" class="swatch" data-color="#1565c0" title="Biru"
                                    style="background:#1565c0"></button>
                                <button type="button" class="swatch" data-color="#d32f2f" title="Merah"
                                    style="background:#d32f2f"></button>
                                <button type="button" class="swatch" data-color="#ffffff" title="Putih"
                                    style="background:#ffffff"></button>
                                <input type="color" id="bg_color" class="form-control form-control-color"
                                    value="#1565c0" title="Warna lain">
                                <span class="text-muted fs-7 ms-1">Kotak terakhir untuk warna lain</span>
                            </div>
                        </div>

                        <div class="section-label">Ukuran &amp; Jumlah</div>
                        <div id="size_list"></div>

                        <button type="button" class="btn btn-sm btn-light w-100 mb-3" id="btn_copy_qty"
                            style="display:none">
                            <i class="fas fa-copy me-1"></i> Samakan jumlah ini ke semua foto
                        </button>

                        <div class="section-label">Tambah ukuran lain (cm)</div>
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
                </div>

                {{-- 3. Pengaturan Cetak --}}
                <div class="card card-panel p-4">
                    <div class="card-title-sm"><span class="step-badge">3</span>Pengaturan Cetak</div>

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
                            Posisi <span class="text-muted fw-normal" id="editor_title"></span></div>
                        <div class="btn-group btn-group-sm" id="size_tabs"></div>
                    </div>

                    <div class="editor-wrap mb-3">
                        <canvas id="editor"></canvas>
                        <div class="editor-overlay" id="editor_overlay" style="display:none"></div>
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
                        <summary class="text-muted fs-7" style="cursor:pointer">Patokan posisi otomatis (foto
                            yang dihapus background-nya)</summary>
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
                            menunjukkan patokan ini. Foto tanpa hapus background diposisikan di tengah dan
                            memenuhi bingkai.</div>
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

            /*
             * Satu foto:
             * { id, blob, url, img, w, h,           -> foto asli (sudah dirapikan rotasinya)
             *   cutout, head, edges,                -> hasil hapus background
             *   removeBg, status, color, qty,       -> pengaturan per foto
             *   baseK, zoom, rotate, pos }          -> posisi di bingkai
             * status: 'ready' | 'queued' | 'processing' | 'error'
             */
            let photos = [];
            let activeId = null;
            let uid = 0;
            let sizes = [];
            let activeSizeId = '3x4';
            let lastColor = '#1565c0';
            let bgModule = null;
            let queueRunning = false;
            let busy = false;
            let previewTimer = null;

            const activePhoto = () => photos.find(p => p.id === activeId) || null;
            const hasCutout = (p) => !!(p && p.removeBg && p.cutout && p.head);
            const totalQty = (p) => sizes.reduce((sum, s) => sum + (p.qty[s.id] || 0), 0);
            const needsAi = (p) => p.removeBg && !p.cutout && p.status !== 'error';

            /* =============== Pengaturan umum =============== */
            function num(id, fallback = 0) {
                const v = parseFloat(el(id).value);
                return isNaN(v) ? fallback : Math.max(0, v);
            }

            function getSettings() {
                return {
                    margin: num('paper_margin', 3),
                    border: num('white_border', 2),
                    cutStyle: el('cut_style').value,
                    allowRotate: el('allow_rotate').checked,
                    autoRemove: el('auto_remove').checked,
                    headPct: num('head_pct', 50),
                    topPct: num('top_pct', 10),
                    filename: el('file_name').value.trim() || 'pas-foto'
                };
            }

            function saveSettings() {
                const s = getSettings();
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify({
                        color: lastColor,
                        margin: s.margin,
                        border: s.border,
                        cutStyle: s.cutStyle,
                        allowRotate: s.allowRotate,
                        autoRemove: s.autoRemove,
                        headPct: s.headPct,
                        topPct: s.topPct,
                        customSizes: sizes.filter(x => x.custom).map(x => ({ w: x.w, h: x.h }))
                    }));
                } catch (e) {}
            }

            function loadSettings() {
                sizes = DEFAULT_SIZES.map(s => ({ ...s, custom: false }));
                let saved = null;
                try {
                    saved = JSON.parse(localStorage.getItem(STORAGE_KEY));
                } catch (e) {}
                if (!saved) return;

                if (saved.color) lastColor = saved.color;
                if (saved.margin !== undefined) el('paper_margin').value = saved.margin;
                if (saved.border !== undefined) el('white_border').value = saved.border;
                if (saved.cutStyle) el('cut_style').value = saved.cutStyle;
                if (saved.allowRotate !== undefined) el('allow_rotate').checked = saved.allowRotate;
                if (saved.autoRemove !== undefined) el('auto_remove').checked = saved.autoRemove;
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
                sizes.push({ id, w, h, custom: true });
            }

            /* =============== Membaca foto =============== */
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

            async function addFiles(fileList) {
                const files = Array.from(fileList || []).filter(f => (f.type || '').startsWith('image/'));
                if (!files.length) {
                    Swal.fire('Bukan gambar', 'Pilih file foto (JPG/PNG).', 'warning');
                    return;
                }

                const s = getSettings();
                let firstNew = null;
                let failed = 0;

                for (const file of files) {
                    try {
                        const blob = await normalizeImage(file);
                        const url = URL.createObjectURL(blob);
                        const img = await loadImage(url);
                        const p = {
                            id: ++uid,
                            blob, url, img,
                            w: img.naturalWidth,
                            h: img.naturalHeight,
                            cutout: null,
                            head: null,
                            edges: null,
                            removeBg: s.autoRemove,
                            status: s.autoRemove ? 'queued' : 'ready',
                            color: lastColor,
                            qty: {},
                            baseK: 1,
                            zoom: 100,
                            rotate: 0,
                            pos: { ox: 0, oy: 0.5 }
                        };
                        autoPosition(p);
                        photos.push(p);
                        if (!firstNew) firstNew = p;
                    } catch (e) {
                        failed++;
                    }
                }

                if (firstNew) selectPhoto(firstNew.id);
                if (failed) Swal.fire('Perhatian', `${failed} file tidak bisa dibaca dan dilewati.`, 'warning');
                runQueue();
            }

            function removePhoto(id) {
                const idx = photos.findIndex(p => p.id === id);
                if (idx < 0) return;
                const p = photos[idx];
                if (p.status === 'processing') {
                    Swal.fire('Tunggu sebentar', 'Foto ini sedang diproses. Hapus setelah selesai.', 'info');
                    return;
                }
                URL.revokeObjectURL(p.url);
                if (p.cutout) URL.revokeObjectURL(p.cutout.src);
                photos.splice(idx, 1);
                if (activeId === id) {
                    const next = photos[idx] || photos[idx - 1];
                    activeId = next ? next.id : null;
                }
                selectPhoto(activeId);
            }

            /* =============== Hapus background (AI), satu per satu =============== */
            function setAiStatus(text, pct = null) {
                el('ai_status').style.display = text ? 'block' : 'none';
                el('ai_status_text').textContent = text || '';
                el('ai_status_pct').textContent = pct === null ? '' : pct + '%';
                el('ai_progress').style.width = (pct === null ? 100 : pct) + '%';
                el('ai_progress').classList.toggle('progress-bar-animated', pct === null);
            }

            async function runQueue() {
                if (queueRunning) return;
                queueRunning = true;
                let errors = 0;

                try {
                    while (true) {
                        const p = photos.find(needsAi);
                        if (!p) break;

                        const left = photos.filter(needsAi).length;
                        const label = `Menghapus background Foto ${photos.indexOf(p) + 1}` +
                            (left > 1 ? ` (sisa ${left} foto)` : '') + '...';
                        p.status = 'processing';
                        setAiStatus(label);
                        refreshAll();

                        try {
                            if (!bgModule) {
                                setAiStatus('Memuat AI penghapus background...');
                                bgModule = await import(BG_REMOVAL_URL);
                            }
                            const result = await bgModule.removeBackground(p.blob, {
                                model: 'isnet_fp16',
                                output: { format: 'image/png' },
                                progress: (key, current, total) => {
                                    if (key.startsWith('fetch') && total) {
                                        setAiStatus('Mengunduh model AI (sekali saja)...',
                                            Math.round(current / total * 100));
                                    } else {
                                        setAiStatus(label);
                                    }
                                }
                            });

                            if (!photos.includes(p)) continue; // foto sudah dihapus
                            p.cutout = await loadImage(URL.createObjectURL(result));
                            Object.assign(p, analyzeCutout(p.cutout));
                            p.status = 'ready';
                            if (p.removeBg) autoPosition(p);
                        } catch (err) {
                            console.error(err);
                            errors++;
                            p.status = 'error';
                            p.removeBg = false;
                            autoPosition(p);
                        }
                        if (p.id === activeId) syncControls();
                        refreshAll();
                    }
                } finally {
                    queueRunning = false;
                    setAiStatus('');
                    refreshAll();
                }

                if (errors) {
                    Swal.fire('Sebagian gagal',
                        `${errors} foto tidak bisa dihapus background-nya. Foto tersebut dipakai apa adanya. ` +
                        'Periksa koneksi internet, lalu nyalakan lagi "Hapus background" untuk mencoba ulang.',
                        'warning');
                }
            }

            /* =============== Deteksi posisi kepala =============== */
            function analyzeCutout(cutout) {
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

                const top = W.findIndex(w => w >= 3);
                const bottom = SH - 1 - [...W].reverse().findIndex(w => w >= 3);
                if (top < 0) {
                    return { head: { cx: iw / 2, cy: ih * 0.3, h: ih * 0.4 }, edges: null };
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

                return {
                    head: { cx: cx / sf, cy: (top + headH / 2) / sf, h: headH / sf },
                    edges: {
                        bottom: W[SH - 1] > 0,
                        left: L.some(v => v === 0),
                        right: R.some(v => v === SW - 1)
                    }
                };
            }

            /* =============== Posisi =============== */
            // pos & ukuran dalam satuan "tinggi pas foto"
            function autoPosition(p) {
                const s = getSettings();
                if (hasCutout(p)) {
                    const target = s.headPct / 100;
                    p.baseK = target / p.head.h;
                    p.pos = { ox: 0, oy: s.topPct / 100 + target / 2 };
                } else {
                    // Tanpa hapus background: foto di tengah & memenuhi bingkai
                    const used = sizes.filter(x => (p.qty[x.id] || 0) > 0);
                    const list = used.length ? used : sizes;
                    const maxRatio = Math.max(...list.map(x => x.w / x.h));
                    p.baseK = Math.max(maxRatio / p.w, 1 / p.h);
                    p.pos = { ox: 0, oy: 0.5 };
                }
                p.zoom = 100;
                p.rotate = 0;
            }

            function drawPhoto(ctx, Wpx, Hpx, p) {
                const cut = hasCutout(p);
                ctx.fillStyle = cut ? p.color : '#ffffff';
                ctx.fillRect(0, 0, Wpx, Hpx);

                const img = cut ? p.cutout : p.img;
                const cx = cut ? p.head.cx : p.w / 2;
                const cy = cut ? p.head.cy : p.h / 2;
                const k = p.baseK * p.zoom / 100 * Hpx;

                ctx.save();
                ctx.beginPath();
                ctx.rect(0, 0, Wpx, Hpx);
                ctx.clip();
                ctx.translate(Wpx / 2 + p.pos.ox * Hpx, p.pos.oy * Hpx);
                ctx.rotate(p.rotate * Math.PI / 180);
                ctx.scale(k, k);
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(img, -cx, -cy);
                ctx.restore();
            }

            /* =============== Tampilan: daftar foto =============== */
            function renderPhotoList() {
                const list = el('photo_list');
                list.innerHTML = photos.map((p, i) => {
                    const total = totalQty(p);
                    let status = '';
                    if (p.status === 'processing') {
                        status = '<span class="spinner-border spinner-border-sm text-primary" title="Menghapus background"></span>';
                    } else if (p.status === 'queued' && p.removeBg) {
                        status = '<span class="badge bg-light text-muted">Antri</span>';
                    } else if (p.status === 'error') {
                        status = '<span class="badge bg-danger">Gagal</span>';
                    } else if (!p.removeBg) {
                        status = '<span class="badge bg-light text-muted">Asli</span>';
                    }
                    return `
                        <div class="photo-item ${p.id === activeId ? 'active' : ''}" data-id="${p.id}">
                            <canvas class="photo-thumb" width="90" height="120" data-id="${p.id}"></canvas>
                            <div class="flex-grow-1" style="min-width:0">
                                <div class="fw-semibold">Foto ${i + 1}</div>
                                <div class="fs-7 ${total ? 'text-muted' : 'text-warning'}">
                                    ${total ? `${total} lembar` : 'Jumlah belum diisi'}
                                </div>
                            </div>
                            ${status}
                            <button type="button" class="btn btn-sm btn-light text-danger remove-photo" data-id="${p.id}" title="Hapus foto">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>`;
                }).join('');

                list.querySelectorAll('canvas.photo-thumb').forEach(c => {
                    const p = photos.find(x => x.id === Number(c.dataset.id));
                    if (p) drawPhoto(c.getContext('2d'), c.width, c.height, p);
                });
            }

            /* =============== Tampilan: pengaturan foto terpilih =============== */
            function syncControls() {
                const p = activePhoto();
                const idx = p ? photos.indexOf(p) + 1 : 0;
                el('active_title').textContent = p ? `Foto ${idx}` : 'Foto';
                el('editor_title').textContent = p ? `· Foto ${idx}` : '';
                el('photo_settings').style.display = p ? 'block' : 'none';
                el('no_photo_note').style.display = p ? 'none' : 'block';
                if (!p) return;

                el('remove_bg').checked = p.removeBg;
                el('color_section').style.display = p.removeBg ? 'block' : 'none';
                el('bg_color').value = p.color;
                el('zoom').value = p.zoom;
                el('rotate').value = p.rotate;

                const st = {
                    processing: '(sedang diproses...)',
                    queued: p.removeBg ? '(menunggu antrian)' : '',
                    error: '(gagal, coba nyalakan lagi)'
                };
                el('remove_bg_status').textContent = st[p.status] || '';
            }

            function renderSizes() {
                const p = activePhoto();
                el('size_list').innerHTML = sizes.map(s => {
                    const q = p ? (p.qty[s.id] || 0) : 0;
                    return `
                    <div class="size-row ${q > 0 ? 'has-qty' : ''}">
                        <span class="size-label">${sizeLabel(s)}</span>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group input-group-sm qty-group">
                                <button class="btn btn-light qty-btn" type="button" data-id="${s.id}" data-step="-1"><i class="fas fa-minus"></i></button>
                                <input type="number" class="form-control qty-input" min="0" max="200" value="${q}" data-id="${s.id}">
                                <button class="btn btn-light qty-btn" type="button" data-id="${s.id}" data-step="1"><i class="fas fa-plus"></i></button>
                            </div>
                            ${s.custom ? `<button type="button" class="btn btn-sm btn-light text-danger remove-size" data-id="${s.id}" title="Hapus ukuran"><i class="fas fa-trash"></i></button>` : ''}
                        </div>
                    </div>`;
                }).join('');
                el('btn_copy_qty').style.display = photos.length > 1 ? 'block' : 'none';
                renderTabs();
            }

            function renderTabs() {
                const p = activePhoto();
                const used = p ? sizes.filter(s => (p.qty[s.id] || 0) > 0) : [];
                const list = used.length ? used : sizes;
                if (!list.some(s => s.id === activeSizeId)) activeSizeId = list[0].id;

                el('size_tabs').innerHTML = list.map(s => `
                    <button type="button" class="btn btn-outline-secondary size-tab ${s.id === activeSizeId ? 'active' : ''}" data-id="${s.id}">
                        ${sizeLabel(s).replace(' cm', '')}
                    </button>`).join('');
            }

            function selectPhoto(id) {
                activeId = id;
                syncControls();
                renderSizes();
                refreshAll();
            }

            /* =============== Editor =============== */
            function activeSize() {
                return sizes.find(x => x.id === activeSizeId) || sizes[0];
            }

            function renderEditor() {
                const s = getSettings();
                const p = activePhoto();
                const size = activeSize();
                const cssH = window.innerWidth < 576 ? 300 : 380;
                const cssW = cssH * size.w / size.h;
                const dpr = window.devicePixelRatio || 1;

                editor.style.width = cssW + 'px';
                editor.style.height = cssH + 'px';
                editor.width = Math.round(cssW * dpr);
                editor.height = Math.round(cssH * dpr);

                const ctx = editor.getContext('2d');
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

                if (!p) {
                    ctx.fillStyle = '#e4e6ef';
                    ctx.fillRect(0, 0, cssW, cssH);
                    ctx.fillStyle = '#7e8299';
                    ctx.font = '14px Poppins, sans-serif';
                    ctx.textAlign = 'center';
                    ctx.fillText('Belum ada foto', cssW / 2, cssH / 2);
                    el('editor_overlay').style.display = 'none';
                    el('edge_warning').style.display = 'none';
                    return;
                }

                drawPhoto(ctx, cssW, cssH, p);

                // Garis bantu
                ctx.save();
                ctx.setLineDash([5, 5]);
                ctx.lineWidth = 1;
                ctx.strokeStyle = 'rgba(255,255,255,0.8)';
                ctx.beginPath();
                ctx.moveTo(cssW / 2, 0);
                ctx.lineTo(cssW / 2, cssH);
                if (hasCutout(p)) {
                    const yTop = cssH * s.topPct / 100;
                    const yNeck = cssH * (s.topPct + s.headPct) / 100;
                    ctx.moveTo(0, yTop);
                    ctx.lineTo(cssW, yTop);
                    ctx.moveTo(0, yNeck);
                    ctx.lineTo(cssW, yNeck);
                }
                ctx.stroke();
                ctx.restore();

                const overlay = el('editor_overlay');
                const msg = p.status === 'processing' ? 'Menghapus background...' :
                    (p.status === 'queued' && p.removeBg) ? 'Menunggu giliran hapus background...' : '';
                overlay.textContent = msg;
                overlay.style.display = msg ? 'block' : 'none';

                checkEdges(cssW / cssH, p);
            }

            function checkEdges(ratio, p) {
                const k = p.baseK * p.zoom / 100;
                const msgs = [];
                let text = '';

                if (hasCutout(p)) {
                    // Potongan badan di tepi foto asli terlihat di dalam pas foto
                    if (p.edges) {
                        const left = ratio / 2 + p.pos.ox - p.head.cx * k;
                        const right = ratio / 2 + p.pos.ox + (p.cutout.naturalWidth - p.head.cx) * k;
                        const bottom = p.pos.oy + (p.cutout.naturalHeight - p.head.cy) * k;
                        if (p.edges.bottom && bottom < 0.995) msgs.push('bagian bawah badan');
                        if (p.edges.left && left > 0.005) msgs.push('sisi kiri');
                        if (p.edges.right && right < ratio - 0.005) msgs.push('sisi kanan');
                    }
                    if (msgs.length) text = `Potongan foto asli terlihat di ${msgs.join(', ')}. Perbesar zoom atau geser foto.`;
                } else if (p.status !== 'processing' && !(p.status === 'queued' && p.removeBg)) {
                    // Foto asli harus menutupi seluruh bingkai
                    const left = ratio / 2 + p.pos.ox - (p.w / 2) * k;
                    const right = ratio / 2 + p.pos.ox + (p.w / 2) * k;
                    const top = p.pos.oy - (p.h / 2) * k;
                    const bottom = p.pos.oy + (p.h / 2) * k;
                    if (left > 0.005) msgs.push('kiri');
                    if (right < ratio - 0.005) msgs.push('kanan');
                    if (top > 0.005) msgs.push('atas');
                    if (bottom < 0.995) msgs.push('bawah');
                    if (msgs.length) text = `Ada area kosong (putih) di sisi ${msgs.join(', ')}. Perbesar zoom atau geser foto.`;
                }

                el('edge_warning').style.display = text ? 'block' : 'none';
                el('edge_warning_text').textContent = text;
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
                    placed.push({
                        photo: it.photo,
                        size: it.size,
                        x: best.x,
                        y: best.y,
                        cw: best.w,
                        ch: best.h,
                        rot: best.rot
                    });
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
                photos.forEach(photo => {
                    sizes.forEach(size => {
                        const q = photo.qty[size.id] || 0;
                        for (let i = 0; i < q; i++) {
                            items.push({ photo, size, w: size.w * 10 + s.border * 2, h: size.h * 10 + s.border * 2 });
                        }
                    });
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

            function renderPhotoCanvas(photo, size, pxPerMm, rotated = false) {
                const c = document.createElement('canvas');
                c.width = Math.max(1, Math.round(size.w * 10 * pxPerMm));
                c.height = Math.max(1, Math.round(size.h * 10 * pxPerMm));
                drawPhoto(c.getContext('2d'), c.width, c.height, photo);
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

            const cellKey = (cell) => `p${cell.photo.id}_${cell.size.id}${cell.rot ? '_r' : ''}`;

            function renderPreviews() {
                const s = getSettings();
                const wrap = el('page_previews');
                wrap.innerHTML = '';

                const layout = buildLayout(s);
                el('empty_preview').style.display = layout.total ? 'none' : 'block';

                if (layout.error) {
                    el('layout_summary').innerHTML = `<span class="text-danger">${layout.error}</span>`;
                    return;
                }
                el('layout_summary').textContent = layout.total ?
                    `${layout.total} foto · ${layout.pages.length} lembar A4` : '';
                if (!layout.total) return;

                const P = PREVIEW_PX_PER_MM;
                const cache = {};
                const cut = CUT_STYLES[s.cutStyle];
                layout.pages.forEach((page, i) => {
                    const c = document.createElement('canvas');
                    c.width = A4_W * P;
                    c.height = A4_H * P;
                    const ctx = c.getContext('2d');
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, c.width, c.height);

                    page.forEach(cell => {
                        const key = cellKey(cell);
                        if (!cache[key]) cache[key] = renderPhotoCanvas(cell.photo, cell.size, P, cell.rot);
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
                if (busy || queueRunning) return;
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
                            const key = cellKey(cell);
                            if (!images[key]) {
                                images[key] = renderPhotoCanvas(cell.photo, cell.size, pxPerMm, cell.rot)
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

                const p = activePhoto();
                const anyQty = photos.some(x => totalQty(x) > 0);
                btn.disabled = busy || queueRunning || !anyQty;
                el('loading-text').textContent = generating ? 'Membuat PDF...' :
                    (queueRunning && anyQty ? 'Tunggu hapus background selesai...' : '');

                const editable = !!p && !busy;
                el('btn_auto').disabled = !editable;
                el('zoom').disabled = !editable;
                el('rotate').disabled = !editable;
            }

            function updateSliderLabels() {
                el('zoom_val').textContent = el('zoom').value + '%';
                el('rotate_val').textContent = el('rotate').value + '°';
                el('head_pct_val').textContent = el('head_pct').value + '%';
                el('top_pct_val').textContent = el('top_pct').value + '%';
            }

            function updateSwatches() {
                const p = activePhoto();
                const color = p ? p.color.toLowerCase() : '';
                document.querySelectorAll('.swatch').forEach(sw => {
                    sw.classList.toggle('active', sw.dataset.color === color);
                });
            }

            function refreshAll() {
                updateSliderLabels();
                updateSwatches();
                updateButtons();
                renderPhotoList();
                renderEditor();
                schedulePreviews();
            }

            /* =============== Event =============== */
            // Tempel foto (bisa beberapa sekaligus)
            document.addEventListener('paste', (e) => {
                const items = e.clipboardData ? Array.from(e.clipboardData.items) : [];
                const files = items.filter(it => it.kind === 'file' && it.type.startsWith('image/'))
                    .map(it => it.getAsFile()).filter(Boolean);
                if (!files.length) return;
                e.preventDefault();
                addFiles(files);
            });

            // Seret & pilih file
            const dropZone = el('drop_zone');
            ['dragover', 'drop'].forEach(evt => window.addEventListener(evt, e => e.preventDefault()));
            dropZone.addEventListener('dragover', () => dropZone.classList.add('dragover'));
            dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
            dropZone.addEventListener('drop', (e) => {
                dropZone.classList.remove('dragover');
                addFiles(e.dataTransfer.files);
            });
            dropZone.addEventListener('click', () => el('file_input').click());
            el('file_input').addEventListener('change', function() {
                addFiles(this.files);
                this.value = '';
            });

            // Daftar foto: pilih / hapus
            el('photo_list').addEventListener('click', (e) => {
                const rm = e.target.closest('.remove-photo');
                if (rm) {
                    removePhoto(Number(rm.dataset.id));
                    return;
                }
                const item = e.target.closest('.photo-item');
                if (item) selectPhoto(Number(item.dataset.id));
            });

            el('auto_remove').addEventListener('change', saveSettings);

            // Hapus background per foto
            el('remove_bg').addEventListener('change', function() {
                const p = activePhoto();
                if (!p) return;
                p.removeBg = this.checked;
                if (p.removeBg && !p.cutout && p.status !== 'processing') p.status = 'queued';
                if (!p.removeBg && p.status === 'error') p.status = 'ready';
                autoPosition(p);
                syncControls();
                refreshAll();
                if (p.removeBg) runQueue();
            });

            // Warna
            function setColor(color) {
                const p = activePhoto();
                if (!p) return;
                p.color = color;
                lastColor = color;
                el('bg_color').value = color;
                saveSettings();
                refreshAll();
            }
            document.querySelectorAll('.swatch').forEach(sw => {
                sw.addEventListener('click', () => setColor(sw.dataset.color));
            });
            el('bg_color').addEventListener('input', function() {
                setColor(this.value);
            });

            // Jumlah & ukuran (per foto)
            function setQty(sizeId, qty) {
                const p = activePhoto();
                if (!p) return;
                p.qty[sizeId] = Math.max(0, Math.min(200, Math.round(qty) || 0));
                if (p.qty[sizeId] > 0) activeSizeId = sizeId;
                renderSizes();
                refreshAll();
            }

            el('size_list').addEventListener('click', (e) => {
                const btn = e.target.closest('.qty-btn');
                const p = activePhoto();
                if (btn && p) {
                    setQty(btn.dataset.id, (p.qty[btn.dataset.id] || 0) + Number(btn.dataset.step));
                    return;
                }
                const rm = e.target.closest('.remove-size');
                if (rm) {
                    sizes = sizes.filter(x => x.id !== rm.dataset.id);
                    photos.forEach(x => delete x.qty[rm.dataset.id]);
                    saveSettings();
                    renderSizes();
                    refreshAll();
                }
            });
            el('size_list').addEventListener('change', (e) => {
                if (e.target.classList.contains('qty-input')) setQty(e.target.dataset.id, parseFloat(e.target.value));
            });

            el('btn_copy_qty').addEventListener('click', () => {
                const p = activePhoto();
                if (!p) return;
                photos.forEach(x => {
                    if (x !== p) x.qty = { ...p.qty };
                });
                refreshAll();
                Swal.fire({
                    icon: 'success',
                    title: 'Jumlah disamakan',
                    text: `Semua foto sekarang memakai jumlah yang sama dengan Foto ${photos.indexOf(p) + 1}.`,
                    timer: 1800,
                    showConfirmButton: false
                });
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

            // Slider zoom & putar (foto terpilih)
            el('zoom').addEventListener('input', function() {
                const p = activePhoto();
                if (p) p.zoom = parseFloat(this.value) || 100;
                updateSliderLabels();
                renderEditor();
                schedulePreviews();
            });
            el('rotate').addEventListener('input', function() {
                const p = activePhoto();
                if (p) p.rotate = parseFloat(this.value) || 0;
                updateSliderLabels();
                renderEditor();
                schedulePreviews();
            });
            ['zoom', 'rotate'].forEach(id => el(id).addEventListener('change', renderPhotoList));

            // Patokan posisi otomatis (berlaku untuk foto yang dihapus background-nya)
            ['head_pct', 'top_pct'].forEach(id => {
                el(id).addEventListener('input', () => {
                    saveSettings();
                    updateSliderLabels();
                    renderEditor();
                });
                el(id).addEventListener('change', () => {
                    const p = activePhoto();
                    if (p && hasCutout(p)) {
                        autoPosition(p);
                        syncControls();
                    }
                    refreshAll();
                });
            });

            el('btn_auto').addEventListener('click', () => {
                const p = activePhoto();
                if (!p) return;
                autoPosition(p);
                syncControls();
                refreshAll();
            });

            // Geser foto di editor
            let drag = null;
            editor.addEventListener('pointerdown', (e) => {
                if (!activePhoto() || busy) return;
                drag = { x: e.clientX, y: e.clientY };
                editor.setPointerCapture(e.pointerId);
                editor.classList.add('dragging');
            });
            editor.addEventListener('pointermove', (e) => {
                const p = activePhoto();
                if (!drag || !p) return;
                const h = editor.getBoundingClientRect().height;
                p.pos.ox += (e.clientX - drag.x) / h;
                p.pos.oy += (e.clientY - drag.y) / h;
                drag = { x: e.clientX, y: e.clientY };
                renderEditor();
            });
            const endDrag = () => {
                if (!drag) return;
                drag = null;
                editor.classList.remove('dragging');
                renderPhotoList();
                schedulePreviews();
            };
            editor.addEventListener('pointerup', endDrag);
            editor.addEventListener('pointercancel', endDrag);

            // Zoom dengan scroll
            editor.addEventListener('wheel', (e) => {
                const p = activePhoto();
                if (!p || busy) return;
                e.preventDefault();
                const z = el('zoom');
                z.value = Math.max(+z.min, Math.min(+z.max, +z.value + (e.deltaY < 0 ? 5 : -5)));
                p.zoom = parseFloat(z.value);
                updateSliderLabels();
                renderEditor();
                schedulePreviews();
            }, { passive: false });

            window.addEventListener('resize', renderEditor);
            el('btn_generate').addEventListener('click', generatePdf);

            // Mulai
            loadSettings();
            el('bg_color').value = lastColor;
            selectPhoto(null);
        })();
    </script>
</body>

</html>