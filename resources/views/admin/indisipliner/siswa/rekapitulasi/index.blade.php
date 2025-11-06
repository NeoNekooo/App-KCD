@extends('layouts.admin')

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
        <form action="{{ route('admin.indisipliner.siswa.rekapitulasi.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-10">
                    <label for="nis" class="form-label">Pilih Siswa</label>
                    <select name="nis" id="nis" class="form-select" required>
                        <option value="">Ketik atau pilih nama siswa...</option>
                        @foreach($siswaList as $s)
                            <option value="{{ $s->nipd }}" {{ request('nis') == $s->nipd ? 'selected' : '' }}>
                                {{ $s->nama }} (NIPD: {{ $s->nipd }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-search"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Hasil Pencarian --}}
@if($siswa)
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bx bx-user-circle me-2"></i>Rapor Pelanggaran: <strong>{{ $siswa->nama }}</strong>
        </h5>
        <button class="btn btn-secondary btn-sm"><i class="bx bx-printer me-1"></i> Cetak Laporan</button>
    </div>
    <div class="card-body">
        <div class="row">
            {{-- Kolom Kiri: Informasi Siswa --}}
            <div class="col-md-7 border-end">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-shrink-0 me-3">
                         <img src="https://placehold.co/100x100/696cff/white?text={{ substr($siswa->nama, 0, 1) }}" alt="Avatar" class="d-block rounded-circle" height="100" width="100">
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">{{ $siswa->nama }}</h5>
                        <p class="text-muted mb-0">{{ $siswa->nama_rombel ?? 'Belum ada kelas' }}</p>
                    </div>
                </div>
                <h6><i class="bx bx-detail me-2"></i>Data Diri</h6>
                <table class="table table-sm table-borderless">
                     <tr>
                        <td style="width: 120px;">NIPD</td>
                        <td>: {{ $siswa->nipd }}</td>
                    </tr>
                    <tr>
                        <td>NISN</td>
                        <td>: {{ $siswa->nisn }}</td>
                    </tr>
                </table>
            </div>

            {{-- Kolom Kanan: Poin dan Sanksi --}}
            <div class="col-md-5 d-flex flex-column justify-content-center align-items-center p-4">
                <h6 class="text-muted mb-3">Total Akumulasi Poin</h6>
                <div class="position-relative">
                    <div class="display-3 fw-bold text-danger">{{ $totalPoin }}</div>
                </div>
                 @if($sanksiAktif)
                    <div class="text-center mt-3">
                        <p class="mb-1 text-muted">Sanksi Aktif:</p>
                        <span class="badge bg-label-danger fs-6">{{ $sanksiAktif->nama }}</span>
                    </div>
                @else
                    <div class="text-center mt-3">
                         <span class="badge bg-label-success fs-6">Tidak Ada Sanksi</span>
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
                        <td style="white-space: normal;">{{ $pelanggaran->detailPoin->nama ?? '-' }}</td>
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
        <p class="text-muted">Silakan pilih siswa di atas untuk menampilkan rekapitulasi pelanggaran.</p>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#nis').select2({
            theme: "bootstrap-5",
            placeholder: "Ketik atau pilih nama siswa...",
        });
    });
</script>
@endpush

