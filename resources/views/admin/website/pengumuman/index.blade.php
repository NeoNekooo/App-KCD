@extends('layouts.admin')
@section('title', 'Kelola Pengumuman')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bolder m-0 text-dark">Kelola Pengumuman</h4>
        <span class="text-muted small">Kelola pengumuman resmi untuk ditampilkan di Frontend.</span>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAdd">
        <i class='bx bx-plus me-1'></i> Tambah Pengumuman
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
                        <th class="py-3">Prioritas</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Terbit</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengumuman as $i => $item)
                    <tr>
                        <td class="px-4">{{ $i+1 }}</td>
                        <td><span class="fw-bold">{{ Str::limit($item->judul, 50) }}</span></td>
                        <td>
                            @if($item->prioritas == 'urgent') <span class="badge bg-danger rounded-pill">Urgent</span>
                            @elseif($item->prioritas == 'penting') <span class="badge bg-warning rounded-pill">Penting</span>
                            @else <span class="badge bg-info rounded-pill">Biasa</span>
                            @endif
                        </td>
                        <td><span class="badge {{ $item->status=='publish'?'bg-success':'bg-secondary' }} rounded-pill">{{ ucfirst($item->status) }}</span></td>
                        <td class="text-muted small">{{ $item->tanggal_terbit ? $item->tanggal_terbit->format('d M Y') : '-' }}</td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-icon btn-text-primary" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}"><i class='bx bx-edit'></i></button>
                            <form action="{{ route('admin.website.pengumuman.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?');">@csrf @method('DELETE')<button class="btn btn-sm btn-icon btn-text-danger"><i class='bx bx-trash'></i></button></form>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content rounded-4 border-0 shadow">
                        <form action="{{ route('admin.website.pengumuman.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <div class="modal-header border-0 pb-0"><h5 class="modal-title fw-bold">Edit Pengumuman</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <div class="mb-3"><label class="form-label small fw-bold">Judul <span class="text-danger">*</span></label><input type="text" name="judul" class="form-control" value="{{ $item->judul }}" required></div>
                                <div class="mb-3"><label class="form-label small fw-bold">Isi <span class="text-danger">*</span></label><textarea name="isi" class="form-control" rows="5" required>{{ $item->isi }}</textarea></div>
                                <div class="row">
                                    <div class="col-md-4 mb-3"><label class="form-label small fw-bold">Prioritas</label><select name="prioritas" class="form-select"><option value="biasa" {{ $item->prioritas=='biasa'?'selected':'' }}>Biasa</option><option value="penting" {{ $item->prioritas=='penting'?'selected':'' }}>Penting</option><option value="urgent" {{ $item->prioritas=='urgent'?'selected':'' }}>Urgent</option></select></div>
                                    <div class="col-md-4 mb-3"><label class="form-label small fw-bold">Tanggal Terbit</label><input type="date" name="tanggal_terbit" class="form-control" value="{{ $item->tanggal_terbit?->format('Y-m-d') }}"></div>
                                    <div class="col-md-4 mb-3"><label class="form-label small fw-bold">Tanggal Berakhir</label><input type="date" name="tanggal_berakhir" class="form-control" value="{{ $item->tanggal_berakhir?->format('Y-m-d') }}"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Lampiran File</label><input type="file" name="lampiran" class="form-control"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Status</label><select name="status" class="form-select"><option value="draft" {{ $item->status=='draft'?'selected':'' }}>Draft</option><option value="publish" {{ $item->status=='publish'?'selected':'' }}>Publish</option></select></div>
                                </div>
                            </div>
                            <div class="modal-footer border-0"><button type="submit" class="btn btn-primary w-100 rounded-pill">Simpan</button></div>
                        </form>
                    </div></div></div>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pengumuman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalAdd" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content rounded-4 border-0 shadow">
    <form action="{{ route('admin.website.pengumuman.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header border-0 pb-0"><h5 class="modal-title fw-bold">Tambah Pengumuman</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label small fw-bold">Judul <span class="text-danger">*</span></label><input type="text" name="judul" class="form-control" required></div>
            <div class="mb-3"><label class="form-label small fw-bold">Isi <span class="text-danger">*</span></label><textarea name="isi" class="form-control" rows="5" required></textarea></div>
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label small fw-bold">Prioritas</label><select name="prioritas" class="form-select"><option value="biasa">Biasa</option><option value="penting">Penting</option><option value="urgent">Urgent</option></select></div>
                <div class="col-md-4 mb-3"><label class="form-label small fw-bold">Tanggal Terbit</label><input type="date" name="tanggal_terbit" class="form-control"></div>
                <div class="col-md-4 mb-3"><label class="form-label small fw-bold">Tanggal Berakhir</label><input type="date" name="tanggal_berakhir" class="form-control"></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Lampiran</label><input type="file" name="lampiran" class="form-control"></div>
                <div class="col-md-6 mb-3"><label class="form-label small fw-bold">Status</label><select name="status" class="form-select"><option value="draft">Draft</option><option value="publish" selected>Publish</option></select></div>
            </div>
        </div>
        <div class="modal-footer border-0"><button type="submit" class="btn btn-primary w-100 rounded-pill">Tambah</button></div>
    </form>
</div></div></div>
@endsection
