<div class="table-responsive">
    <table class="table align-middle table-row-dashed fs-6 gy-5">
        <thead>
            <tr class="text-start text-gray-800 fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-125px">Nama Alat</th>
                <th class="min-w-125px">Jenis</th>
                <th class="min-w-100px">Jumlah</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($tools as $tool)
                <tr>
                    <td>{{ $tool->nama }}</td>
                    <td>{{ $tool->jenis }}</td>
                    <td >
                        @if ($tool->stok_real == 0)
                            <span class="badge badge-light-danger fw-bold fs-7">Habis</span>
                        @else
                            <span class="badge badge-light-success fw-bold fs-7">{{ $tool->stok_real }}
                                {{ $tool->satuan }}</span>
                        @endif

                        @if ($tool->stok_dipinjam > 0)
                            <div class="text-muted fs-8 mt-1 fw-bold text-danger">
                                (Dipinjam: {{ $tool->stok_dipinjam }})
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Tidak ada alat di gudang ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $tools->links() }}
