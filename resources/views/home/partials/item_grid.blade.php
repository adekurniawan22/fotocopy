<div class="row g-4">
    @forelse($items as $item)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card card-custom h-100 position-relative">
                @auth
                    <div class="position-absolute top-0 end-0 p-2" style="z-index: 10;">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light btn-icon shadow-sm rounded-circle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><a class="dropdown-item btn-edit-item" href="#" data-id="{{ $item->item_id }}"><i
                                            class="fas fa-edit text-warning me-2"></i> Edit</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item btn-delete-item text-danger" href="#"
                                        data-id="{{ $item->item_id }}"><i class="fas fa-trash me-2"></i> Hapus</a></li>
                            </ul>
                        </div>
                    </div>
                @endauth

                <div class="card-img-wrapper">
                    @php
                        $photos = $item->foto;
                        $hasPhoto = is_array($photos) && count($photos) > 0;
                        $firstPhoto = $hasPhoto ? $photos[0] : null;
                    @endphp

                    @if ($hasPhoto)
                        <a class="d-block w-100 h-100" data-fslightbox="gallery-{{ $item->item_id }}"
                            href="{{ asset('storage/' . $firstPhoto) }}">
                            <img src="{{ asset('storage/' . $firstPhoto) }}" alt="{{ $item->item_name }}">
                        </a>
                        @foreach (array_slice($photos, 1) as $nextPhoto)
                            <a class="d-none" data-fslightbox="gallery-{{ $item->item_id }}"
                                href="{{ asset('storage/' . $nextPhoto) }}"></a>
                        @endforeach
                    @else
                        <img src="{{ asset('assets/media/svg/files/blank-image.svg') }}" alt="No Image">
                    @endif
                </div>

                <div class="card-body d-flex flex-column p-3">
                    <h6 class="fw-bold mb-3 text-dark lh-sm">
                        {{ $item->item_name }}
                    </h6>

                    <div class="mt-auto">
                        <div class="text-price mb-1">
                            Rp {{ number_format($item->sell_price, 0, ',', '.') }}
                            <small class="text-muted fw-normal fs-7">/ {{ $item->unit }}</small>
                        </div>

                        @auth
                            <div class="border-top pt-2 mt-2 border-dashed">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Modal:</small>
                                <span class="text-danger fw-bold" style="font-size: 0.9rem;">
                                    Rp {{ number_format($item->buy_price, 0, ',', '.') }}
                                    <small class="text-muted fw-normal fs-7">/ {{ $item->unit }}</small>
                                </span>
                            </div>
                        @endauth
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
            </div>
        </div>
    @endforelse
</div>

{{ $items->links() }}
