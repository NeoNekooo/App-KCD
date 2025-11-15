@extends('layouts.admin') {{-- Sesuaikan dengan layout Sneat Admin --}}

@section('title', 'Data Ekstrakurikuler')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- 🔹 Breadcrumb & Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Akademik /</span> Ekstrakurikuler
            </h4>
            <p class="text-muted mb-0">Daftar kegiatan ekstrakurikuler yang tersedia di sekolah.</p>
        </div>
    </div>

    {{-- 🔹 Card Utama --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bx bx-medal me-2"></i>Data Ekstrakurikuler</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th>Nama Ekstrakurikuler</th>
                            <th>Alias</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ekskul as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <i class="bx bx-trophy text-primary me-1"></i>
                                    <strong>{{ $item['nama'] }}</strong>
                                </td>
                                <td><span class="badge bg-label-info px-3 py-2">{{ $item['alias'] ?? '-' }}</span></td>
                                <td>{{ $item['keterangan'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bx bx-calendar-event bx-lg text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Belum ada data ekstrakurikuler yang tercatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
