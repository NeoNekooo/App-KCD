@extends('layouts.admin')

@section('content')
    {{-- 🔥 CSS PREMIUM 🔥 --}}
    <style>
        .rounded-4 {
            border-radius: 1rem !important;
        }

        .shadow-soft {
            box-shadow: 0 8px 25px rgba(105, 108, 255, 0.08) !important;
        }

        /* Premium Table Design - Excel Style */
        .table-custom {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #d9dee3;
            /* Border luar meniru grid Excel */
        }

        .table-custom th,
        .table-custom td {
            border: 1px solid #d9dee3;
            /* Grid border untuk semua cell */
            padding: 0.85rem 1rem;
            vertical-align: middle;
        }

        .table-custom th {
            background-color: #f1f4f8;
            /* Header abu-abu terang */
            color: #4b5563;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #c9d2db !important;
            text-align: center;
        }

        .table-custom td {
            color: #566a7f;
            transition: all 0.2s ease;
        }

        /* Hover effect cell agar lebih responsif */
        .table-custom tbody tr:hover td {
            background-color: rgba(105, 108, 255, 0.05);
            color: #4b5563;
        }

        /* Baris Grand Total (Footer) - Excel Style */
        .tfoot-grand-total td {
            background-color: #e2e8f0 !important;
            /* Warna Abu-abu gelap (ala fillType Solid Excel) */
            color: #1e293b !important;
            font-weight: 800;
            font-size: 1.05rem;
            border: 1px solid #cbd5e1 !important;
        }

        /* Number Badge Highlights */
        .angka-negeri {
            color: #28c76f;
            font-weight: 700;
        }

        .angka-swasta {
            color: #ff9f43;
            font-weight: 700;
        }

        .angka-total {
            color: #696cff;
            font-weight: 800;
        }

        /* Animation Keyframes */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- BREADCRUMB --}}
        <h4 class="fw-bold py-3 mb-4 animate-fade-in-up">
            <span class="text-muted fw-light">Monitoring / Satuan Pendidikan /</span> Rekapitulasi
        </h4>

        {{-- MAIN CARD --}}
        <div class="card border-0 shadow-soft rounded-4 animate-fade-in-up" style="animation-delay: 0.1s;">

            {{-- HEADER CARD (JUDUL & FILTER) --}}
            <div
                class="card-header bg-transparent py-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                {{-- Judul Kiri --}}
                <div class="d-flex align-items-center">
                    <div
                        class="avatar avatar-md me-3 bg-label-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                        <i class="bx bx-bar-chart-alt-2 fs-4"></i>
                    </div>
                    <div>
                        <h5 class="card-title fw-bold m-0 text-dark">Rekapitulasi Jumlah Sekolah</h5>
                        <small class="text-muted d-block mt-1">Pengelompokan per Wilayah Kabupaten/Kota</small>
                    </div>
                </div>

                {{-- Filter Jenjang Kanan --}}
                <div class="d-flex align-items-center gap-2">
                    <form action="{{ route('admin.sekolah.rekapitulasi') }}" method="GET"
                        class="d-flex align-items-center m-0" id="filterRekapForm">
                        <label class="fw-bold text-muted small me-2 text-nowrap">Filter Status (Jenjang):</label>
                        <select name="jenjang" class="form-select border shadow-sm fw-bold px-3 py-2 text-primary"
                            style="border-radius: 0.5rem; min-width: 150px; cursor: pointer;"
                            onchange="document.getElementById('filterRekapForm').submit()">
                            <option value="" class="text-muted">-- Semua Jenjang --</option>
                            @foreach ($listJenjang as $j)
                                <option value="{{ $j }}" {{ $jenjangTerpilih == $j ? 'selected' : '' }}>
                                    {{ $j }}</option>
                            @endforeach
                        </select>
                    </form>

                    {{-- Tombol Cetak Excel --}}
                    @php
                        $urlExport = route('admin.sekolah.rekapitulasi.export-excel');
                        if (!empty($jenjangTerpilih)) {
                            $urlExport .= '?jenjang=' . urlencode($jenjangTerpilih);
                        }
                    @endphp
                    <a href="{{ $urlExport }}" class="btn btn-outline-success rounded-pill fw-bold shadow-sm ms-2"
                        data-bs-toggle="tooltip" title="Export tabel ke Excel">
                        <i class="bx bx-spreadsheet fs-5 me-1"></i> Cetak Excel
                    </a>
                </div>
            </div>

            {{-- TABEL DATA --}}
            <div class="card-body p-0">
                <div class="table-responsive text-nowrap rounded-bottom">
                    <table class="table table-custom mb-0">
                        {{-- THEAD --}}
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%;">No</th>
                                <th style="width: 35%;">Kabupaten / Kota</th>
                                <th class="text-end" style="width: 20%;">Negeri</th>
                                <th class="text-end" style="width: 20%;">Swasta</th>
                                <th class="text-end" style="width: 20%;">Jumlah</th>
                            </tr>
                        </thead>

                        {{-- TBODY --}}
                        <tbody>
                            @forelse($rekapData as $index => $row)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="fw-bold text-dark">{{ str_replace(['Kab. ', 'Kota '], '', $row->kabupaten_kota) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end angka-negeri fs-6">{{ number_format($row->total_negeri) }}</td>
                                    <td class="text-end angka-swasta fs-6">{{ number_format($row->total_swasta) }}</td>
                                    <td class="text-end angka-total fs-6">
                                        <span
                                            class="badge bg-label-primary rounded-pill px-3 py-1 fs-6 shadow-sm">{{ number_format($row->total_keseluruhan) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div
                                            class="d-flex flex-column align-items-center justify-content-center text-muted">
                                            <i class="bx bx-info-circle fs-1 text-light mb-2" style="opacity: 0.5;"></i>
                                            <h6 class="fw-bold mb-0">Tidak Ada Data Sekolah</h6>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        {{-- TFOOT (GRAND TOTAL) --}}
                        @if ($rekapData->count() > 0)
                            <tfoot>
                                <tr class="tfoot-grand-total">
                                    <td colspan="2" class="text-center text-uppercase"
                                        style="letter-spacing: 1px; padding: 1.25rem !important;">
                                        <i class="bx bx-sum me-2 fs-5 align-middle"></i> Total Jumlah
                                    </td>
                                    <td class="text-end fs-5" style="padding: 1.25rem !important;">
                                        {{ number_format($grandTotalNegeri) }}</td>
                                    <td class="text-end fs-5" style="padding: 1.25rem !important;">
                                        {{ number_format($grandTotalSwasta) }}</td>
                                    <td class="text-end fs-4" style="padding: 1.25rem !important;">
                                        {{ number_format($grandTotalAkhir) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
