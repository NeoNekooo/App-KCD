@extends('layouts.admin')

@section('title', 'Manajemen Instrumen PKKS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0"><span class="text-muted fw-light">PKKS /</span> Instrumen</h4>
        <a href="{{ route('admin.pkks.instrumen.create') }}" class="btn btn-primary shadow-sm">
            <i class="bx bx-plus me-1"></i> Buat Paket Baru
        </a>
    </div>

    <div class="row">
        @forelse($instrumens as $item)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-none border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-label-primary rounded-pill">Tahun {{ $item->tahun }}</span>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <form action="{{ route('admin.pkks.instrumen.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus instrumen ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"><i class="bx bx-trash me-2"></i>Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold mb-3">{{ $item->nama }}</h5>
                    <div class="d-flex align-items-center gap-3 text-muted small mb-4">
                        <span><i class="bx bx-list-ul me-1"></i> {{ $item->kompetensis_count }} Kompetensi</span>
                        <span><i class="bx bx-check-shield me-1"></i> Skala 1-{{ $item->skor_maks }}</span>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.pkks.instrumen.manage', $item->id) }}" class="btn btn-outline-primary">
                            <i class="bx bx-cog me-1"></i> Kelola Soal
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow-none border text-center py-5">
                <div class="card-body">
                    <i class="bx bx-file text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h5>Belum ada instrumen</h5>
                    <p class="text-muted">Klik "Buat Paket Baru" untuk memulai.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
