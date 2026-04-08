@extends('layouts.admin')
@section('title', 'Kelola Unduhan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bolder m-0 text-dark">Kelola Unduhan</h4>
        <span class="text-muted small">Kelola file unduhan publik (formulir, peraturan, panduan, dll).</span>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAdd">
        <i class='bx bx-plus me-1'></i> Tambah File
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class='bx bx-check-circle me-1'></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="py-3">Judul</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3">Unduhan</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unduhan as $i => $item)
                    <tr>
                        <td class="px-4">{{ $i+1 }}</td>
                        <td>
                            <span class="fw-bold">{{ Str::limit($item->judul, 50) }}</span>
                            @if($item->deskripsi)<br><small class="text-muted">{{ Str::limit($item->deskripsi, 60) }}</small>@endif
                        </td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">{{ $item->kategori }}</span></td>
                        <td><span class="badge bg-light text-dark"><i class='bx bx-download me-1'></i>{{ $item->jumlah_unduhan }}x</span></td>
                        <td class="text-muted small">{{ $item->created_at->format('d M Y') }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ Storage::url($item->file) }}" target="_blank" class="btn btn-sm btn-icon btn-text-info"><i class='bx bx-download'></i></a>
                            <button class="btn btn-sm btn-icon btn-text-primary" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}"><i class='bx bx-edit'></i></button>
                            <form action="{{ route('admin.website.unduhan.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?');">@csrf @method('DELETE')<button class="btn btn-sm btn-icon btn-text-danger"><i class='bx bx-trash'></i></button></form>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4 border-0 shadow">
                        <form action="{{ route('admin.website.unduhan.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <div class="modal-header border-0 pb-0"><h5 class="modal-title fw-bold">Edit Unduhan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <div class="mb-3"><label class="form-label small fw-bold">Judul <span class="text-danger">*</span></label><input type="text" name="judul" class="form-control" value="{{ $item->judul }}" required></div>
                                <div class="mb-3"><label class="form-label small fw-bold">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2">{{ $item->deskripsi }}</textarea></div>
                                <div class="row">
                                    <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Ganti File</label><input type="file" name="file" class="form-control"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Kategori</label><select name="kategori" class="form-select"><option value="Peraturan" {{ $item->kategori=='Peraturan'?'selected':'' }}>Peraturan</option><option value="Formulir" {{ $item->kategori=='Formulir'?'selected':'' }}>Formulir</option><option value="Panduan" {{ $item->kategori=='Panduan'?'selected':'' }}>Panduan</option><option value="Laporan" {{ $item->kategori=='Laporan'?'selected':'' }}>Laporan</option><option value="Umum" {{ $item->kategori=='Umum'?'selected':'' }}>Umum</option></select></div>
                                </div>
                            </div>
                            <div class="modal-footer border-0"><button type="submit" class="btn btn-primary w-100 rounded-pill">Simpan</button></div>
                        </form>
                    </div></div></div>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada file unduhan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalAdd" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4 border-0 shadow">
    <form action="{{ route('admin.website.unduhan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header border-0 pb-0"><h5 class="modal-title fw-bold">Tambah File Unduhan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label small fw-bold">Judul <span class="text-danger">*</span></label><input type="text" name="judul" class="form-control" required></div>
            <div class="mb-3"><label class="form-label small fw-bold">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2"></textarea></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label small fw-bold">File <span class="text-danger">*</span></label><input type="file" name="file" class="form-control" required></div>
                <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Kategori</label><select name="kategori" class="form-select"><option value="Peraturan">Peraturan</option><option value="Formulir">Formulir</option><option value="Panduan">Panduan</option><option value="Laporan">Laporan</option><option value="Umum" selected>Umum</option></select></div>
            </div>
        </div>
        <div class="modal-footer border-0"><button type="submit" class="btn btn-primary w-100 rounded-pill">Tambah</button></div>
    </form>
</div></div></div>
@endsection
