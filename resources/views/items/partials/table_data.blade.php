<div class="table-responsive">
    <table class="table align-middle table-row-dashed fs-6 gy-5">
        <thead>
            <tr class="text-start text-gray-800 fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-250px">Nama Barang</th>
                <th class="min-w-150px">Harga</th>
                <th class="text-end min-w-100px">Aksi</th>
            </tr>
        </thead>
        <tbody class="fw-semibold text-gray-600">
            @forelse ($items as $item)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            @php
                                $photos = $item->foto;
                                $hasPhoto = is_array($photos) && count($photos) > 0;
                                $firstPhotoPath = $hasPhoto ? $photos[0] : null;
                            @endphp

                            @if ($hasPhoto)
                                <a class="d-block overlay me-3" data-fslightbox="gallery-{{ $item->item_id }}"
                                    href="{{ asset('storage/' . $firstPhotoPath) }}">
                                    <div class="overlay-wrapper bgi-no-repeat bgi-position-center bgi-size-cover card-rounded w-50px h-50px"
                                        style="background-image:url('{{ asset('storage/' . $firstPhotoPath) }}')">
                                    </div>
                                    <div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow w-50px h-50px">
                                        <i class="bi bi-eye-fill text-white fs-3"></i>
                                    </div>
                                </a>

                                @foreach (array_slice($photos, 1) as $nextPhoto)
                                    <a class="d-none" data-fslightbox="gallery-{{ $item->item_id }}"
                                        href="{{ asset('storage/' . $nextPhoto) }}"></a>
                                @endforeach
                            @else
                                <div class="symbol symbol-50px me-3">
                                    <div class="symbol-label fs-2 fw-semibold bg-light-primary text-primary">
                                        {{ strtoupper(substr($item->item_name, 0, 1)) }}
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex flex-column">
                                <span class="text-gray-800 mb-1 fw-bold">
                                    {{ $item->item_name }}
                                </span>

                                @if (!empty($item->description))
                                    <div class="text-muted fs-7 lh-sm cursor-pointer" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="{{ $item->description }}">
                                        {{ Str::limit($item->description, 40) }}
                                    </div>
                                @else
                                    <div class="text-muted fs-7 lh-sm">
                                        Tidak ada deskripsi
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td>
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 fw-bold">
                                Rp {{ number_format($item->sell_price, 0, ',', '.') }} / {{ $item->unit }}
                            </span>

                            <span class="text-muted fs-7">
                                Modal: Rp {{ number_format($item->buy_price, 0, ',', '.') }}
                            </span>
                        </div>
                    </td>

                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm"
                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi <i class="ki-duotone ki-down fs-5 ms-1"></i>
                        </a>

                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                            data-kt-menu="true">

                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 btn-edit" data-id="{{ $item->item_id }}">
                                    <i class="ki-duotone ki-notepad-edit me-2 fs-3"><span class="path1"></span><span
                                            class="path2"></span></i>
                                    Edit
                                </a>
                            </div>

                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 btn-delete text-danger"
                                    data-id="{{ $item->item_id }}">
                                    <i class="ki-duotone ki-trash me-2 fs-3 text-danger">
                                        <span class="path1"></span><span class="path2"></span><span
                                            class="path3"></span><span class="path4"></span><span
                                            class="path5"></span>
                                    </i>
                                    Hapus
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center p-5">
                        <div class="d-flex flex-column align-items-center">
                            <img src="{{ asset('assets/media/svg/files/blank-image.svg') }}" class="h-75px mb-2"
                                style="opacity: 0.5">
                            <span class="text-muted fw-bold">Data Barang tidak ditemukan.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $items->links() }}
