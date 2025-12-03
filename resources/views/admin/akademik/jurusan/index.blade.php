@extends('layouts.admin') {{-- Sesuai Layout Sneat --}}

@section('title', 'Data Jurusan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- 🔹 Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Akademik /</span> Konsentrasi Keahlian
        </h4>
    </div>

    {{-- 🔹 Card Utama --}}
    <div class="card shadow-sm border-0">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bx bx-book-content me-2"></i>Daftar Konsentrasi Keahlian</h5>
            <span class="badge bg-primary text-white">{{ $jurusan->count() }} Data</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th style="width: 5%">#</th>
                            <th style="width: 15%">Kode</th>
                            <th>Nama Konsentrasi Keahlian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jurusan as $key => $j)
                            <tr>
                                <td class="text-center fw-semibold">{{ $key + 1 }}</td>
                                <td class="text-center">
                                    <span class="badge bg-label-info fs-6 px-3 py-2">{{ $j->kode }}</span>
                                </td>
                                <td class="fw-semibold text-dark">
                                    <i class="bx bx-chevron-right text-primary"></i> {{ $j->jurusan_id_str }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <i class="bx bx-book-open bx-lg text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Belum ada data Konsentrasi Keahlian.</p>
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
