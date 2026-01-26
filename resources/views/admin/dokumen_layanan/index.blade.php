@extends('layouts.admin')

@section('title', 'Arsip Dokumen Layanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- 1. HEADER PAGE --}}
    <div class="d-flex justify-content-between align-items-center py-3 mb-2">
        <div>
            <h4 class="fw-bold m-0 text-primary">
                <span class="text-muted fw-light">Layanan /</span>
                Arsip Dokumen
            </h4>
            <small class="text-muted">
                <i class="bx bx-archive me-1"></i> Daftar pengajuan yang memiliki dokumen yang diarsipkan
            </small>
        </div>
    </div>

    {{-- 2. MAIN TABLE CARD --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header border-bottom bg-white py-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <h5 class="m-0 fw-bold"><i class="bx bx-list-ul me-2 text-primary"></i>Arsip Pengajuan</h5>

                <form action="{{ route('admin.dokumen-layanan.index') }}" method="GET" class="d-flex flex-column flex-sm-row align-items-sm-center gap-2">
                    <div class="input-group input-group-merge shadow-none border rounded-pill bg-light px-2 overflow-hidden" style="width: 250px;">
                        <span class="input-group-text bg-transparent border-0"><i class="bx bx-filter-alt text-muted"></i></span>
                        <select name="kategori" class="form-select border-0 bg-transparent shadow-none small fw-bold" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoris as $kategori)
                            <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                                {{ ucwords(str_replace('-', ' ', $kategori)) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group input-group-merge shadow-none border rounded-pill bg-light px-2 overflow-hidden" style="width: 300px;">
                        <span class="input-group-text bg-transparent border-0"><i class="bx bx-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none small" placeholder="Cari sekolah, guru, judul..." value="{{ request('search') }}">
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-4 text-uppercase small fw-bold text-muted">Pemohon</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Judul Pengajuan</th>
                        <th class="py-3 text-center text-uppercase small fw-bold text-muted">Status</th>
                        <th class="py-3 text-center text-uppercase small fw-bold text-muted">Jml. Dokumen</th>
                        <th class="py-3 pe-4 text-end text-uppercase small fw-bold text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($pengajuans as $pengajuan)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-secondary fw-bold">{{ substr($pengajuan->nama_sekolah, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="fw-bold d-block text-dark small">{{ $pengajuan->nama_sekolah }}</div>
                                    <small class="text-muted d-block">{{ $pengajuan->nama_guru }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-secondary mb-1" style="font-size: 0.65rem;">
                                {{ strtoupper(str_replace('-', ' ', $pengajuan->kategori)) }}
                            </span>
                            <div class="text-wrap small text-dark fw-semibold" style="max-width: 350px;">
                                {{ $pengajuan->judul }}
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill px-3 bg-label-primary">{{ $pengajuan->status }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $pengajuan->dokumen_layanan_count }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalDokumen{{ $pengajuan->id }}">
                                <i class="bx bx-show"></i> Lihat Dokumen
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bx bx-info-circle bx-lg text-muted mb-3 d-block"></i>
                            <h5 class="mb-2">
                                @if(request('search'))
                                    Pengajuan tidak ditemukan.
                                @else
                                    Belum ada pengajuan dengan dokumen yang diarsipkan.
                                @endif
                            </h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($pengajuans->hasPages())
        <div class="px-4 py-3 border-top d-flex justify-content-center">
            {{ $pengajuans->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODALS CONTAINER --}}
@foreach ($pengajuans as $pengajuan)
<div class="modal fade" id="modalDokumen{{ $pengajuan->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3 px-4">
                <div class="d-flex align-items-center">
                    <i class='bx bx-archive fs-3 me-2'></i>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">Daftar Dokumen Terarsip</h5>
                        <small class="opacity-75">{{ $pengajuan->nama_sekolah }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close bg-white opacity-100" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th class="small text-muted text-uppercase">Nama Dokumen</th>
                                <th class="text-center small text-muted text-uppercase">Tanggal Arsip</th>
                                <th class="text-end small text-muted text-uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuan->dokumenLayanan as $dokumen)
                            <tr>
                                <td class="align-middle fw-semibold text-dark">{{ $dokumen->nama_dokumen }}</td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-label-secondary">{{ $dokumen->created_at->format('d M Y, H:i') }}</span>
                                </td>
                                <td class="text-end align-middle">
                                    <a href="{{ Storage::url($dokumen->path_dokumen) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bx bx-link-external"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Tidak ada dokumen terarsip untuk pengajuan ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
