@extends('layouts.admin')
@section('title', 'Kelola Galeri')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bolder m-0 text-dark">Kelola Galeri Kegiatan</h4>
        <span class="text-muted small">Kelola album foto kegiatan organisasi.</span>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAdd">
        <i class='bx bx-plus me-1'></i> Tambah Album
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class='bx bx-check-circle me-1'></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4">
    @forelse($galeri as $album)
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            @if($album->foto)
            <img src="{{ Storage::url($album->foto) }}" class="card-img-top" style="height:200px;object-fit:cover;">
            @else
            <div class="bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="height:200px;">
                <i class='bx bx-images text-info' style="font-size:3rem;opacity:0.3;"></i>
            </div>
            @endif
            <div class="card-body">
                <h6 class="fw-bold text-dark mb-1">{{ $album->judul }}</h6>
                <p class="text-muted small mb-2">{{ Str::limit($album->deskripsi, 80) }}</p>
                <div class="d-flex gap-2 text-muted small">
                    <span><i class='bx bx-images me-1'></i>{{ $album->items_count ?? 0 }} Foto</span>
                    @if($album->tanggal)<span><i class='bx bx-calendar me-1'></i>{{ $album->tanggal->format('d M Y') }}</span>@endif
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 d-flex gap-2 p-3">
                <button class="btn btn-sm btn-outline-primary rounded-pill flex-fill" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $album->id }}"><i class='bx bx-edit me-1'></i>Edit</button>
                <form action="{{ route('admin.website.galeri.destroy', $album->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('Hapus album ini beserta semua fotonya?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger rounded-pill w-100"><i class='bx bx-trash me-1'></i>Hapus</button>
                </form>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="modalEdit{{ $album->id }}" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.website.galeri.update', $album->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0"><h5 class="modal-title fw-bold">Edit Album</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label small fw-bold">Judul Album <span class="text-danger">*</span></label><input type="text" name="judul" class="form-control" value="{{ $album->judul }}" required></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Tanggal</label><input type="date" name="tanggal" class="form-control" value="{{ $album->tanggal?->format('Y-m-d') }}"></div>
                        <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Cover Album</label><input type="file" name="foto" class="form-control" accept="image/*"></div>
                    </div>
                    <div class="mb-3"><label class="form-label small fw-bold">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2">{{ $album->deskripsi }}</textarea></div>
                    <div class="mb-3"><label class="form-label small fw-bold">Tambah Foto Baru</label><input type="file" name="items[]" class="form-control" accept="image/*" multiple></div>
                    @if($album->items->count() > 0)
                    <label class="form-label small fw-bold">Foto Saat Ini</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($album->items as $foto)
                        <div class="position-relative" style="width:80px;height:80px;">
                            <img src="{{ Storage::url($foto->file) }}" class="w-100 h-100 rounded object-fit-cover border">
                            <form action="{{ route('admin.website.galeri.item.destroy', $foto->id) }}" method="POST" class="position-absolute top-0 end-0" onsubmit="return confirm('Hapus foto?');">@csrf @method('DELETE')<button class="btn btn-danger btn-sm px-1 py-0 rounded-circle" style="font-size:10px;line-height:1;"><i class='bx bx-x'></i></button></form>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-0"><button type="submit" class="btn btn-primary w-100 rounded-pill">Simpan</button></div>
            </form>
        </div></div></div>
    </div>
    @empty
    <div class="col-12"><div class="text-center text-muted py-5"><i class='bx bx-images' style="font-size:3rem;opacity:0.25;"></i><p class="mt-2">Belum ada album galeri.</p></div></div>
    @endforelse
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalAdd" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content rounded-4 border-0 shadow">
    <form action="{{ route('admin.website.galeri.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header border-0 pb-0"><h5 class="modal-title fw-bold">Tambah Album Galeri</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label small fw-bold">Judul Album <span class="text-danger">*</span></label><input type="text" name="judul" class="form-control" required></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Tanggal</label><input type="date" name="tanggal" class="form-control"></div>
                <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Cover Album</label><input type="file" name="foto" class="form-control" accept="image/*"></div>
            </div>
            <div class="mb-3"><label class="form-label small fw-bold">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2"></textarea></div>
            <div class="mb-3"><label class="form-label small fw-bold">Upload Foto (Multi-select)</label><input type="file" name="items[]" class="form-control" accept="image/*" multiple></div>
        </div>
        <div class="modal-footer border-0"><button type="submit" class="btn btn-primary w-100 rounded-pill">Tambah Album</button></div>
    </form>
</div></div></div>
@endsection
