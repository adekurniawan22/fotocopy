{{-- Layout Utama --}}
@extends('layouts.app')

{{-- Breadcumbs --}}
@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-line fw-semibold fs-7 my-1">
        <li class="breadcrumb-item text-gray-600">
            <a href="{{ route('dashboard.index') }}" class="text-gray-600 text-hover-primary">Home</a>
        </li>
        <li class="breadcrumb-item text-gray-600">Dashboard</li>
    </ul>
@endsection

{{-- Title --}}
@section('title', 'Dashboard')
@section('pageTitle', 'Dashboard')

{{-- Content --}}
@section('content')
    <div class="container-xxl" id="kt_content_container">
        <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
            <div class="col-xl-6">
                <div class="card bg-primary text-white card-xl-stretch mb-xl-8">
                    <div class="card-body d-flex align-items-center p-0">
                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 px-5">
                            <span class="fw-bold text-white fs-4 mb-2">Total Barang</span>
                            <span class="fw-bolder fs-2x">{{ number_format($totalItems) }} <span
                                    class="fs-4 fw-bold">Unit</span></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center p-5" style="opacity: 0.8">
                            <i class="fas fa-box-open fa-5x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card bg-info text-white card-xl-stretch mb-xl-8">
                    <div class="card-body d-flex align-items-center p-0">
                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 px-5">
                            <span class="fw-bold fs-4 mb-2">Total Pengguna</span>
                            <span class="fw-bolder fs-2x">{{ number_format($totalUsers) }} <span
                                    class="fs-4 fw-bold">Orang</span></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center p-5" style="opacity: 0.8">
                            <i class="fas fa-users fa-5x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-5 g-xl-8">
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 text-gray-900">Top 5 Barang Termahal</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Berdasarkan harga jual tertinggi</span>
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th class="min-w-150px">Nama Barang</th>
                                        <th class="min-w-100px text-end">Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expensiveItems as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="d-flex justify-content-start flex-column">
                                                        <span
                                                            class="text-gray-900 fw-bold text-hover-primary fs-6">{{ $item->item_name }}</span>
                                                        <span
                                                            class="text-muted fw-semibold d-block fs-7">{{ $item->unit }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span class="text-primary fw-bold d-block fs-6">Rp
                                                    {{ number_format($item->sell_price, 0, ',', '.') }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">Belum ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 text-gray-900">Top 5 Barang Termurah</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Berdasarkan harga jual terendah</span>
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th class="min-w-150px">Nama Barang</th>
                                        <th class="min-w-100px text-end">Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cheapestItems as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="d-flex justify-content-start flex-column">
                                                        <span
                                                            class="text-gray-900 fw-bold text-hover-primary fs-6">{{ $item->item_name }}</span>
                                                        <span
                                                            class="text-muted fw-semibold d-block fs-7">{{ $item->unit }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span class="text-success fw-bold d-block fs-6">Rp
                                                    {{ number_format($item->sell_price, 0, ',', '.') }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">Belum ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
