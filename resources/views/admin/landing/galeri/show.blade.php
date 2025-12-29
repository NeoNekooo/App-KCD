@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header: Tombol Kembali & Judul --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.landing.galeri.index') }}" class="text-muted mb-2 d-inline-block text-decoration-none">
                <i class='bx bx-arrow-back'></i> Kembali ke Daftar Album
            </a>
            <h4 class="fw-bold m-0 text-primary">{{ $galeri->judul }}</h4>
            <small class="text-muted"><i class='bx bx-calendar'></i> {{ $galeri->tanggal ? $galeri->tanggal->format('d M Y') : '-' }}</small>
        </div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalUploadItem">
            <i class='bx bx-cloud-upload me-1'></i> Upload Foto/Video
        </button>
    </div>

    {{-- Tabs Filter (Semua, Foto, Video) --}}
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-all">
                <i class='bx bx-grid-alt me-1'></i> Semua
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-foto">
                <i class='bx bx-image me-1'></i> Foto
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-video">
                <i class='bx bx-video me-1'></i> Video
            </button>
        </li>
    </ul>

    {{-- Content Tabs --}}
    <div class="tab-content p-0 bg-transparent shadow-none">
        
        {{-- TAB 1: SEMUA --}}
        <div class="tab-pane fade show active" id="tab-all">
            <div class="row g-3">
                @forelse($galeri->items as $item)
                    @include('admin.landing.galeri.partials.item-card', ['item' => $item])
                @empty
                    <div class="col-12 text-center py-5 text-muted">Belum ada item di album ini.</div>
                @endforelse
            </div>
        </div>

        {{-- TAB 2: FOTO --}}
        <div class="tab-pane fade" id="tab-foto">
            <div class="row g-3">
                @forelse($galeri->items->where('jenis', 'foto') as $item)
                    @include('admin.landing.galeri.partials.item-card', ['item' => $item])
                @empty
                    <div class="col-12 text-center py-5 text-muted">Belum ada foto.</div>
                @endforelse
            </div>
        </div>

        {{-- TAB 3: VIDEO --}}
        <div class="tab-pane fade" id="tab-video">
            <div class="row g-3">
                @forelse($galeri->items->where('jenis', 'video') as $item)
                    @include('admin.landing.galeri.partials.item-card', ['item' => $item])
                @empty
                    <div class="col-12 text-center py-5 text-muted">Belum ada video.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- MODAL UPLOAD MULTIPLE --}}
<div class="modal fade" id="modalUploadItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.landing.galeri.item.store', $galeri->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload ke Album</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih File (Bisa banyak sekaligus)</label>
                        {{-- Atribut 'multiple' penting agar bisa pilih banyak file --}}
                        <input type="file" class="form-control" name="files[]" multiple accept="image/*,video/*" required>
                        <div class="form-text">Support JPG, PNG, MP4. Max 20MB per file.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Mulai Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection