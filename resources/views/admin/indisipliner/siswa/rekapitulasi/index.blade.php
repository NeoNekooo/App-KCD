@extends('layouts.admin')

{{-- 
======================================================================
 BAGIAN STYLE KHUSUS (Avatar Inisial & Tombol Pulse)
======================================================================
--}}
@push('styles')
<style>
    /* Avatar Inisial Nama */
    .avatar-initials {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background-color: #f0f0f8;
        color: #696cff;
        font-size: 2.5rem;
        font-weight: 600;
    }

    /* Animasi Pulse Merah untuk Tombol SP */
    .pulse-button {
        animation: pulse-red 2s infinite;
        box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.7);
    }

    @keyframes pulse-red {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(255, 0, 0, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(255, 0, 0, 0);
        }
    }
</style>
@endpush

{{-- 
======================================================================
 BAGIAN CONTENT UTAMA
======================================================================
--}}
@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Indisipliner / Siswa /</span> Rekapitulasi Pelanggaran</h4>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible" role="alert">
        {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Form Pencarian Siswa --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="bx bx-search-alt me-2"></i>Cari Rapor Pelanggaran Siswa</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.indisipliner.siswa.rekapitulasi.index') }}" method="GET" id="form-filter-rekap">
            
            <div class="row g-3 align-items-end">
                
                {{-- 1. FILTER TINGKATAN --}}
                <div class="col-md-3">
                    <label for="filter_tingkat" class="form-label">Tingkatan</label>
                    <select name="filter_tingkat" id="filter_tingkat" class="form-select">
                        <option value="">Pilih Tingkatan...</option>
                        @foreach($tingkatList as $tingkat)
                            <option value="{{ $tingkat }}" {{ request('filter_tingkat') == $tingkat ? 'selected' : '' }}>
                                {{ $tingkat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 2. FILTER ROMBEL --}}
                <div class="col-md-3">
                    <label for="filter_rombel" class="form-label">Rombel</label>
                    <select name="filter_rombel" id="filter_rombel" class="form-select" 
                            {{ $rombelList->isEmpty() ? 'disabled' : '' }}>
                        <option value="">Pilih Rombel...</option>
                        @foreach($rombelList as $rombel)
                            <option value="{{ $rombel->id }}" {{ request('filter_rombel') == $rombel->id ? 'selected' : '' }}>
                                {{ $rombel->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. PILIH SISWA --}}
                <div class="col-md-4">
                    <label for="nis" class="form-label">Siswa</label>
                    <select name="nis" id="nis" class="form-select" 
                            required {{ $siswaList->isEmpty() ? 'disabled' : '' }}>
                        <option value="">Pilih Siswa...</option>
                        @foreach($siswaList as $s)
                            <option value="{{ $s->nipd }}" {{ request('nis') == $s->nipd ? 'selected' : '' }}>
                                {{ $s->nama }} (NIPD: {{ $s->nipd }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 4. TOMBOL CARI --}}
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100" 
                            {{ $siswaList->isEmpty() ? 'disabled' : '' }}>
                        <i class="bx bx-search"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- 
  Hasil Pencarian
--}}
@if($siswa)
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
@else
{{-- Pesan Awal Sebelum Pencarian --}}
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bx bx-user-search bx-lg text-primary mb-3"></i>
        <h5 class="text-primary">Mulai Pencarian</h5>
        <p class="text-muted">Silakan pilih Tingkatan dan Rombel untuk menampilkan daftar siswa.</p>
    </div>
</div>
@endif

@endsection

{{-- 
======================================================================
 BAGIAN JAVASCRIPT
======================================================================
--}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // Inisialisasi Tooltip Bootstrap (Penting agar tooltip di tombol cetak muncul)
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Ambil elemen-elemen form
        const form = document.getElementById('form-filter-rekap');
        const filterTingkat = document.getElementById('filter_tingkat');
        const filterRombel = document.getElementById('filter_rombel');
        const selectSiswa = document.getElementById('nis');

        // Buat fungsi untuk auto-submit
        function autoSubmitForm() {
            // Kosongkan pilihan siswa saat filter berubah
            if (selectSiswa) {
                selectSiswa.value = ""; 
            }
            
            // Submit form-nya
            if (form) {
                form.submit();
            }
        }

        // Pasang 'event listener' ke filter tingkat
        if (filterTingkat) {
            filterTingkat.addEventListener('change', autoSubmitForm);
        }
        
        // Pasang 'event listener' ke filter rombel
        if (filterRombel) {
            filterRombel.addEventListener('change', autoSubmitForm);
        }

    });
</script>
@endpush