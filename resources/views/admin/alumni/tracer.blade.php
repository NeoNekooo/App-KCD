@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Landing /</span> Penelusuran Kerja</h4>

    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="mb-1 text-primary">{{ $stats['bekerja'] ?? 0 }}</h2>
                    <span class="text-muted fw-semibold">Bekerja</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="mb-1 text-info">{{ $stats['kuliah'] ?? 0 }}</h2>
                    <span class="text-muted fw-semibold">Kuliah</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="mb-1 text-warning">{{ $stats['wirausaha'] ?? 0 }}</h2>
                    <span class="text-muted fw-semibold">Wirausaha</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="mb-1 text-danger">{{ $stats['mencari'] ?? 0 }}</h2>
                    <span class="text-muted fw-semibold">Mencari Kerja</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Sebaran Alumni</h5>
            <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bx bx-printer me-1"></i> Cetak</button>
        </div>
        <div class="table-responsive text-nowrap p-3">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nama Alumni</th>
                        <th>Status</th>
                        <th>Instansi / Kampus</th>
                        <th>Posisi / Jurusan</th>
                        <th>Thn Mulai</th>
                        <th>Linieritas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->siswa->nama ?? 'Nama Tidak Ditemukan' }}</strong><br>
                            <span class="text-xs text-muted">{{ $item->siswa->nisn ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge 
                                @if($item->status_kegiatan == 'Bekerja') bg-label-primary 
                                @elif($item->status_kegiatan == 'Kuliah') bg-label-info 
                                @elif($item->status_kegiatan == 'Wirausaha') bg-label-warning 
                                @else bg-label-secondary @endif">
                                {{ $item->status_kegiatan }}
                            </span>
                        </td>
                        <td>{{ $item->nama_instansi ?? '-' }}</td>
                        <td>{{ $item->bidang_jabatan ?? '-' }}</td> {{-- Pastikan nama kolom sesuai DB --}}
                        <td>{{ $item->tahun_mulai ?? '-' }}</td>
                        <td>
                            @if($item->linieritas == 'Ya')
                                <span class="badge bg-success"><i class="bx bx-check"></i> Ya</span>
                            @elseif($item->linieritas == 'Tidak')
                                <span class="badge bg-danger"><i class="bx bx-x"></i> Tidak</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">Belum ada data tracer study masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection