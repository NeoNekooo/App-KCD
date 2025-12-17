@extends('layouts.admin')

@section('content')
<div class="card">
    {{-- 
       UPDATE DI SINI: MENAMBAHKAN TOMBOL CETAK SP 
    --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bx bx-user-circle me-2"></i>Rapor Pelanggaran: <strong>{{ $siswa->nama }}</strong>
        </h5>
        
        <div class="d-flex gap-2">
            <!-- 1. Tombol Cetak Laporan Biasa (Selalu Ada) -->
            <a href="{{ route('admin.indisipliner.siswa.rekapitulasi.cetak', $siswa->nipd) }}" 
               target="_blank" 
               class="btn btn-secondary" 
               data-bs-toggle="tooltip" title="Cetak Rekapitulasi Lengkap">
                <i class="bx bx-printer me-1"></i> Rekap
            </a>

            <!-- 2. Tombol Cetak SP/Sanksi (HANYA JIKA ADA SANKSI AKTIF) -->
            @if($sanksiAktif)
                <a href="{{ route('admin.indisipliner.siswa.rekapitulasi.cetak-sp', ['nipd' => $siswa->nipd, 'sanksiId' => $sanksiAktif->ID]) }}" 
                   target="_blank" 
                   class="btn btn-danger pulse-button" 
                   data-bs-toggle="tooltip" title="Cetak Surat Peringatan: {{ $sanksiAktif->nama }}">
                    <i class="bx bx-envelope me-1"></i> Cetak {{ $sanksiAktif->nama }}
                </a>
            @endif
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            {{-- Kolom Kiri: Informasi Siswa --}}
            <div class="col-md-7 border-end">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-shrink-0 me-3">
                         <div class="avatar-initials">
                             {{ substr($siswa->nama, 0, 1) }}
                         </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">{{ $siswa->nama }}</h5>
                        <p class="text-muted mb-0">{{ $siswa->rombel->nama ?? 'Belum ada kelas' }}</p>
                    </div>
                </div>
                
                <h6 class="mb-3"><i class="bx bx-detail me-2"></i>Data Diri</h6>
                
                <div class="d-flex mb-2">
                    <span class="fw-semibold me-2" style="width: 80px;">NIPD</span>
                    <span>: {{ $siswa->nipd }}</span>
                </div>
                <div class="d-flex">
                    <span class="fw-semibold me-2" style="width: 80px;">NISN</span>
                    <span>: {{ $siswa->nisn }}</span>
                </div>
            </div>

            {{-- Kolom Kanan: Poin dan Sanksi --}}
            <div class="col-md-5 d-flex flex-column justify-content-center align-items-center p-4">
                <h6 class="text-muted mb-3">Total Akumulasi Poin</h6>
                <div class="text-center bg-light-danger p-4 rounded-3 w-100">
                    <div class="display-3 fw-bold text-danger">{{ $totalPoin }}</div>
                </div>
                
                @if($sanksiAktif)
                    <div class="text-center mt-3">
                        <p class="mb-1 text-muted">Sanksi Aktif:</p>
                        <span class="badge bg-label-danger fs-5">{{ $sanksiAktif->nama }}</span>
                    </div>
                @else
                    <div class="text-center mt-3">
                        <span class="badge bg-label-success fs-5">Tidak Ada Sanksi</span>
                    </div>
                @endif
            </div>
        </div>

        <hr class="my-4">

        {{-- Tabel Riwayat Pelanggaran --}}
        <h6 class="mb-3"><i class="bx bx-list-ul me-2"></i>Riwayat Pelanggaran</h6>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pelanggaran</th>
                        <th>Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggaranSiswa as $key => $pelanggaran)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d M Y') }}</td>
                        <td style="white-space: normal;">{{ $pelanggaran->detailPoinSiswa->nama ?? '-' }}</td>
                        <td><span class="badge bg-danger rounded-pill">{{ $pelanggaran->poin }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <i class="bx bx-check-circle bx-sm text-success"></i>
                            <p class="text-muted mt-2 mb-0">Siswa ini tidak memiliki riwayat pelanggaran.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection