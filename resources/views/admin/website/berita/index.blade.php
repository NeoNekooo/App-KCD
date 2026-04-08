@extends('layouts.admin')
@section('title', 'Kelola Berita')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bolder m-0 text-dark">Kelola Berita</h4>
        <span class="text-muted small">Tambah, edit, atau hapus berita untuk ditampilkan di Frontend.</span>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAdd">
        <i class='bx bx-plus me-1'></i> Tambah Berita
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class='bx bx-check-circle me-1'></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4">
    @forelse($berita as $item)
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            @if($item->gambar)
            <img src="{{ Storage::url($item->gambar) }}" class="card-img-top" style="height:180px;object-fit:cover;">
            @else
            <div class="bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="height:180px;">
                <i class='bx bx-news text-primary' style="font-size:3rem;opacity:0.3;"></i>
            </div>
            @endif
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge {{ $item->status == 'publish' ? 'bg-success' : 'bg-secondary' }} rounded-pill">{{ ucfirst($item->status) }}</span>
                    <small class="text-muted">{{ $item->created_at->format('d M Y') }}</small>
                </div>
                <h6 class="fw-bold text-dark mb-1">{{ Str::limit($item->judul, 60) }}</h6>
                <p class="text-muted small mb-0">{{ Str::limit($item->ringkasan ?? strip_tags($item->isi), 100) }}</p>
            </div>
            <div class="card-footer bg-transparent border-top-0 d-flex gap-2 p-3">
                <button class="btn btn-sm btn-outline-primary rounded-pill flex-fill" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}"><i class='bx bx-edit me-1'></i>Edit</button>
                <form action="{{ route('admin.website.berita.destroy', $item->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('Hapus berita ini?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger rounded-pill w-100"><i class='bx bx-trash me-1'></i>Hapus</button>
                </form>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.website.berita.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0"><h5 class="modal-title fw-bold">Edit Berita</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label small fw-bold">Judul <span class="text-danger">*</span></label><input type="text" name="judul" class="form-control" value="{{ $item->judul }}" required></div>
                    <div class="mb-3"><label class="form-label small fw-bold">Ringkasan</label><textarea name="ringkasan" class="form-control" rows="2">{{ $item->ringkasan }}</textarea></div>
                    <div class="mb-3"><label class="form-label small fw-bold">Isi Berita <span class="text-danger">*</span></label><textarea name="isi" class="form-control" rows="6" required>{{ $item->isi }}</textarea></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Gambar</label><input type="file" name="gambar" class="form-control" accept="image/*"></div>
                        <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Status</label><select name="status" class="form-select"><option value="draft" {{ $item->status=='draft'?'selected':'' }}>Draft</option><option value="publish" {{ $item->status=='publish'?'selected':'' }}>Publish</option></select></div>
                    </div>
                </div>
                <div class="modal-footer border-0"><button type="submit" class="btn btn-primary w-100 rounded-pill">Simpan</button></div>
            </form>
        </div></div></div>
    </div>
    @empty
    <div class="col-12"><div class="text-center text-muted py-5"><i class='bx bx-news' style="font-size:3rem;opacity:0.25;"></i><p class="mt-2">Belum ada berita. Klik "Tambah Berita" untuk memulai.</p></div></div>
    @endforelse
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalAdd" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content rounded-4 border-0 shadow">
    <form action="{{ route('admin.website.berita.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header border-0 pb-0"><h5 class="modal-title fw-bold">Tambah Berita Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label small fw-bold">Judul <span class="text-danger">*</span></label><input type="text" name="judul" class="form-control" required></div>
            <div class="mb-3"><label class="form-label small fw-bold">Ringkasan</label><textarea name="ringkasan" class="form-control" rows="2" placeholder="Opsional. Akan ditampilkan sebagai cuplikan."></textarea></div>
            <div class="mb-3"><label class="form-label small fw-bold">Isi Berita <span class="text-danger">*</span></label><textarea name="isi" class="form-control" rows="6" required></textarea></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Gambar Cover</label><input type="file" name="gambar" class="form-control" accept="image/*"></div>
                <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Status</label><select name="status" class="form-select"><option value="draft">Draft</option><option value="publish" selected>Publish</option></select></div>
            </div>
        </div>
        <div class="modal-footer border-0"><button type="submit" class="btn btn-primary w-100 rounded-pill">Tambah Berita</button></div>
    </form>
</div></div></div>
@endsection
