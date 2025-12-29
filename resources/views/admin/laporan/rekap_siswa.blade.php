@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 m-0"><span class="text-muted fw-light">Laporan /</span> Rekapitulasi Siswa</h4>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="bx bx-printer me-1"></i> Cetak Laporan
        </button>
    </div>

    <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
        <div><span class="badge bg-info">Active: {{ number_format($totalActive ?? 0) }}</span></div>
        <div><span class="badge bg-success">Matched: {{ number_format($matchedCount ?? 0) }}</span></div>
        <div><span class="badge bg-warning text-dark">Unmatched: {{ number_format($unmatchedCount ?? 0) }}</span></div>
        @if(!empty($unmatchedCount) && $unmatchedCount > 0)
            <div class="ms-auto"><small class="text-muted">Tip: {{ $unmatchedCount }} active siswa are not linked to any Rombel.</small></div>
        @endif
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <div class="row align-items-center gy-2">
                <div class="col-md-6">
                    <h5 class="fw-bold text-uppercase mb-0">REKAPITULASI JUMLAH PESERTA DIDIK</h5>
                    <small class="text-muted">TAHUN PELAJARAN 2025/2026</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <form class="d-flex justify-content-md-end gap-2" method="GET" action="{{ route('admin.laporan.rekap_siswa') }}">
                        <select class="form-select form-select-sm" name="tingkat" style="width:140px;">
                            <option value="">Semua Tingkat</option>
                            <option value="X" @selected(request('tingkat') == 'X')>Kelas X</option>
                            <option value="XI" @selected(request('tingkat') == 'XI')>Kelas XI</option>
                            <option value="XII" @selected(request('tingkat') == 'XII')>Kelas XII</option>
                        </select>
                        <input type="search" name="q" class="form-control form-control-sm" placeholder="Cari jurusan..." value="{{ request('q') }}" style="min-width:240px;">
                        <button class="btn btn-primary btn-sm" type="submit">Filter</button>
                        <a href="{{ route('admin.laporan.rekap_siswa') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive text-nowrap" style="max-height:62vh; overflow:auto;">
                <table class="table table-sm table-striped table-hover table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="fw-bold border-bottom">NO</th>
                        <th rowspan="2" class="fw-bold border-bottom text-start" style="min-width: 300px;">PROGRAM KEAHLIAN / JURUSAN</th>
                        <th colspan="3" class="fw-bold border-bottom bg-label-info">KELAS X</th>
                        <th colspan="3" class="fw-bold border-bottom bg-label-warning">KELAS XI</th>
                        <th colspan="3" class="fw-bold border-bottom bg-label-success">KELAS XII</th>
                        <th rowspan="2" class="fw-bold border-bottom bg-label-primary">TOTAL</th>
                    </tr>
                    <tr>
                        <th class="py-2 text-primary">L</th>
                        <th class="py-2 text-danger">P</th>
                        <th class="py-2 fw-bold">JML</th>

                        <th class="py-2 text-primary">L</th>
                        <th class="py-2 text-danger">P</th>
                        <th class="py-2 fw-bold">JML</th>

                        <th class="py-2 text-primary">L</th>
                        <th class="py-2 text-danger">P</th>
                        <th class="py-2 fw-bold">JML</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $jurusan)
                        <tr>
                            <td class="fw-bold">{{ $loop->iteration }}</td>
                            <td class="text-start fw-semibold text-dark">{{ $jurusan['nama'] }}</td>

                            {{-- KELAS X --}}
                            <td class="text-primary">{{ $jurusan['data']['X']['L'] }}</td>
                            <td class="text-danger">{{ $jurusan['data']['X']['P'] }}</td>
                            <td class="fw-bold bg-light">{{ $jurusan['data']['X']['L'] + $jurusan['data']['X']['P'] }}</td>

                            {{-- KELAS XI --}}
                            <td class="text-primary">{{ $jurusan['data']['XI']['L'] }}</td>
                            <td class="text-danger">{{ $jurusan['data']['XI']['P'] }}</td>
                            <td class="fw-bold bg-light">{{ $jurusan['data']['XI']['L'] + $jurusan['data']['XI']['P'] }}</td>

                            {{-- KELAS XII --}}
                            <td class="text-primary">{{ $jurusan['data']['XII']['L'] }}</td>
                            <td class="text-danger">{{ $jurusan['data']['XII']['P'] }}</td>
                            <td class="fw-bold bg-light">{{ $jurusan['data']['XII']['L'] + $jurusan['data']['XII']['P'] }}</td>

                            {{-- TOTAL BARIS --}}
                            <td class="fw-bold bg-label-primary">{{ number_format($jurusan['total_jurusan']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-5 text-muted">
                                <i class="bx bx-info-circle mb-2 d-block fs-1"></i>
                                Belum ada data siswa aktif yang terdaftar di rombel.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <td colspan="2" class="text-center fw-bold">TOTAL SELURUHNYA</td>

                        {{-- GRAND TOTAL X --}}
                        <td>{{ number_format($grandTotal['X']['L']) }}</td>
                        <td>{{ number_format($grandTotal['X']['P']) }}</td>
                        <td class="bg-secondary text-white">{{ number_format($grandTotal['X']['JML']) }}</td>

                        {{-- GRAND TOTAL XI --}}
                        <td>{{ number_format($grandTotal['XI']['L']) }}</td>
                        <td>{{ number_format($grandTotal['XI']['P']) }}</td>
                        <td class="bg-secondary text-white">{{ number_format($grandTotal['XI']['JML']) }}</td>

                        {{-- GRAND TOTAL XII --}}
                        <td>{{ number_format($grandTotal['XII']['L']) }}</td>
                        <td>{{ number_format($grandTotal['XII']['P']) }}</td>
                        <td class="bg-secondary text-white">{{ number_format($grandTotal['XII']['JML']) }}</td>

                        <td class="bg-primary text-white fw-bold">{{ number_format($grandTotal['ALL']) }}</td>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>

        <div class="card-footer text-muted">
            <small>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</small>
        </div>
    </div>
</div>

<style>
    /* Layout / accessibility tweaks */
    .table-responsive { overflow:auto; }
    .table thead th { position: sticky; top: 0; z-index: 2; background: #fff; }
    .table tbody tr:hover { background-color: rgba(13,110,253,0.04); }
    .card-header .form-select-sm, .card-header .form-control-sm { height: calc(1.5em + 0.6rem); }

    @media (max-width: 768px) {
        .card-header .form-select-sm, .card-header .form-control-sm { width: 100% !important; }
        .card-header form { flex-direction: column; gap: .5rem; }
    }

    @media print {
        .btn, .layout-navbar, .layout-footer, .layout-menu-toggle {
            display: none !important;
        }
        .container-p-y {
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .bg-label-primary, .bg-label-info, .bg-label-success, .bg-label-warning {
            background-color: #f0f2f5 !important;
            color: #000 !important;
        }
        .table thead th { position: static; }
    }
</style>
@endsection
