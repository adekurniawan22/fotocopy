<div class="row g-4">
    @forelse($items as $item)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card card-custom h-100">

                {{-- BAGIAN GAMBAR DENGAN LIGHTBOX --}}
                <div class="card-img-wrapper">
                    @php
                        $photos = $item->foto;
                        $hasPhoto = is_array($photos) && count($photos) > 0;
                        $firstPhoto = $hasPhoto ? $photos[0] : null;
                    @endphp

                    @if ($hasPhoto)
                        {{-- 1. Gambar Utama (Bisa diklik untuk Lightbox) --}}
                        <a class="d-block w-100 h-100" data-fslightbox="gallery-{{ $item->item_id }}"
                            href="{{ asset('storage/' . $firstPhoto) }}">
                            <img src="{{ asset('storage/' . $firstPhoto) }}" alt="{{ $item->item_name }}">
                        </a>

                        {{-- 2. Gambar Sisanya (Hidden, tapi masuk ke slide Lightbox yang sama) --}}
                        @foreach (array_slice($photos, 1) as $nextPhoto)
                            <a class="d-none" data-fslightbox="gallery-{{ $item->item_id }}"
                                href="{{ asset('storage/' . $nextPhoto) }}"></a>
                        @endforeach
                    @else
                        {{-- Jika tidak ada foto, tampilkan placeholder (Tanpa Lightbox) --}}
                        <img src="{{ asset('assets/media/svg/files/blank-image.svg') }}" alt="No Image">
                    @endif
                </div>

                <div class="card-body d-flex flex-column">
                    <div class="mb-2">
                        <span class="badge badge-custom">{{ $item->unit }}</span>
                    </div>

                    <h5 class="fw-bold mb-1 text-dark text-truncate" title="{{ $item->item_name }}">
                        {{ $item->item_name }}
                    </h5>

                    @if ($item->location)
                        <small class="text-muted d-block mb-3">
                            <i class="fas fa-map-marker-alt me-1"></i> {{ $item->location }}
                        </small>
                    @else
                        <small class="text-muted d-block mb-3">
                            <i class="fas fa-map-marker-alt me-1"></i> Tidak ada informasi
                        </small>
                    @endif

                    <div class="mt-auto d-flex flex-column">
                        {{-- Harga Jual --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-price">
                                Rp {{ number_format($item->sell_price, 0, ',', '.') }}
                                <small class="text-muted" style="font-size: 0.7em;">
                                    / {{ $item->unit }}
                                </small>
                            </div>
                        </div>

                        {{-- LOGIC HARGA BELI (Hanya User ID 1) --}}
                        @if (Auth::check() && Auth::id() == 1)
                            <div class="text-start mt-1 border-top pt-1 border-dashed">
                                <small class="text-danger fw-bold" style="font-size: 0.8rem;">
                                    Beli: Rp {{ number_format($item->buy_price, 0, ',', '.') }}
                                    {{-- PERUBAHAN DI SINI: Unit diperkecil sedikit lagi dari teks induk --}}
                                    <span style="font-size: 0.8em; opacity: 0.8;">
                                        / {{ $item->unit }}
                                    </span>
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="my-5">
                <img src="{{ asset('assets/media/svg/files/blank-image.svg') }}" height="100px" class="mb-3"
                    style="opacity: 0.5">
                <h4 class="text-gray-600">Tidak ada barang ditemukan</h4>
                <p class="text-muted">Coba kata kunci pencarian yang lain.</p>
            </div>
        </div>
    @endforelse
</div>

{{ $items->links() }}
