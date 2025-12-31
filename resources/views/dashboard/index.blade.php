{{-- Layout Utama --}}
@extends('layouts.app')

{{-- CSS --}}
@push('styles')
    <style>
        .card-shortcut ul {
            padding-left: 1rem;
            margin-top: 10px;
            margin-bottom: 0;
        }

        .card-shortcut li {
            margin-bottom: 5px;
        }

        .card-shortcut a {
            font-size: 0.9rem;
            font-weight: 600;
        }
    </style>
@endpush

{{-- Title --}}
@section('title', 'Dashboard')

{{-- Page Title --}}
@section('pageTitle', 'Dashboard')

{{-- Breadcumbs --}}
@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-line fw-semibold fs-7 my-1">
        <li class="breadcrumb-item text-gray-600">Home</li>
    </ul>
@endsection

{{-- Content --}}
@section('content')
    @php
        $role = session('user_data.short_role_name');
        $now = \Carbon\Carbon::now();

        $dates = [
            'curr' => [
                's' => $now->copy()->startOfMonth()->format('Y-m-d'),
                'e' => $now->copy()->endOfMonth()->format('Y-m-d'),
                'n' => $now->translatedFormat('F'),
            ],
            'p1' => [
                's' => $now->copy()->subMonth(1)->startOfMonth()->format('Y-m-d'),
                'e' => $now->copy()->subMonth(1)->endOfMonth()->format('Y-m-d'),
                'n' => $now->copy()->subMonth(1)->translatedFormat('F'),
            ],
            'p2' => [
                's' => $now->copy()->subMonth(2)->startOfMonth()->format('Y-m-d'),
                'e' => $now->copy()->subMonth(2)->endOfMonth()->format('Y-m-d'),
                'n' => $now->copy()->subMonth(2)->translatedFormat('F'),
            ],
            'n1' => [
                's' => $now->copy()->addMonth(1)->startOfMonth()->format('Y-m-d'),
                'e' => $now->copy()->addMonth(1)->endOfMonth()->format('Y-m-d'),
                'n' => $now->copy()->addMonth(1)->translatedFormat('F'),
            ],
            'n2' => [
                's' => $now->copy()->addMonth(2)->startOfMonth()->format('Y-m-d'),
                'e' => $now->copy()->addMonth(2)->endOfMonth()->format('Y-m-d'),
                'n' => $now->copy()->addMonth(2)->translatedFormat('F'),
            ],
        ];
    @endphp

    <div class="container-xxl" id="kt_content_container">
        <div class="row g-5 g-xl-8">
            <div class="col-xl-12">
                <div class="row mb-5 mb-xl-8 g-5 g-xl-8">

                    <div class="col-6">
                        <a class="card flex-column justify-content-start align-items-start text-start w-100 text-gray-800 text-hover-primary p-10 h-100"
                            href="{{ route($role . '.work-plan.index') }}">
                            <i class="ki-duotone ki-gift fs-2tx mb-5 ms-n1 text-gray-500"><span class="path1"></span><span
                                    class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                            <span class="fs-4 fw-bold">Rencana Kerja</span>
                        </a>
                    </div>

                    @php
                        $shortcuts = [
                            [
                                'title' => 'SPKI',
                                'icon' => 'ki-technology-2',
                                'route' => '.spki.index',
                                'type' => 'past',
                            ],
                            [
                                'title' => 'Laporan Pekerjaan',
                                'icon' => 'ki-fingerprint-scanning',
                                'route' => '.laporan-pekerjaan.index',
                                'type' => 'past',
                            ],
                            [
                                'title' => 'Anomali Gardu Induk',
                                'icon' => 'ki-abstract-26',
                                'route' => '.gardu-induk.index',
                                'type' => 'future',
                            ],
                            [
                                'title' => 'Anomali Jaringan',
                                'icon' => 'ki-basket',
                                'route' => '.jaringan.index',
                                'type' => 'future',
                            ],
                            [
                                'title' => 'Riwayat Gudang',
                                'icon' => 'ki-rocket',
                                'route' => '.history-tool.index',
                                'type' => 'past',
                            ],
                        ];
                    @endphp

                    @foreach ($shortcuts as $sc)
                        <div class="col-6">
                            <div
                                class="card card-shortcut flex-column justify-content-start align-items-start text-start w-100 p-10 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone {{ $sc['icon'] }} fs-2tx ms-n1 text-gray-500 me-3">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="path{{ $i }}"></span>
                                        @endfor
                                    </i>
                                    <span class="fs-4 fw-bold text-gray-800">{{ $sc['title'] }}</span>
                                </div>
                                <ul class="text-gray-600">
                                    <li><a href="{{ route($role . $sc['route']) }}"
                                            onclick="setFilterStorage('{{ $dates['curr']['s'] }}', '{{ $dates['curr']['e'] }}')"
                                            class="text-hover-primary">Bulan Ini ({{ $dates['curr']['n'] }})</a></li>
                                    @if ($sc['type'] == 'past')
                                        <li><a href="{{ route($role . $sc['route']) }}"
                                                onclick="setFilterStorage('{{ $dates['p1']['s'] }}', '{{ $dates['p1']['e'] }}')"
                                                class="text-hover-primary">{{ $dates['p1']['n'] }}</a></li>
                                        <li><a href="{{ route($role . $sc['route']) }}"
                                                onclick="setFilterStorage('{{ $dates['p2']['s'] }}', '{{ $dates['p2']['e'] }}')"
                                                class="text-hover-primary">{{ $dates['p2']['n'] }}</a></li>
                                    @else
                                        <li><a href="{{ route($role . $sc['route']) }}"
                                                onclick="setFilterStorage('{{ $dates['n1']['s'] }}', '{{ $dates['n1']['e'] }}')"
                                                class="text-hover-primary">{{ $dates['n1']['n'] }}</a></li>
                                        <li><a href="{{ route($role . $sc['route']) }}"
                                                onclick="setFilterStorage('{{ $dates['n2']['s'] }}', '{{ $dates['n2']['e'] }}')"
                                                class="text-hover-primary">{{ $dates['n2']['n'] }}</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function setFilterStorage(start, end) {
            sessionStorage.setItem('pending_filter_start', start);
            sessionStorage.setItem('pending_filter_end', end);
        }
    </script>
@endpush
