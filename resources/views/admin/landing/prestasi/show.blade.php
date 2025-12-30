@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.landing.prestasi.index') }}" class="text-muted mb-2 d-inline-block"><i class='bx bx-arrow-back'></i> Kembali</a>
            <h4 class="fw-bold m-0 text-primary">{{ $prestasi->judul }}</h4>
            <small class="text-muted">Pemenang: {{ $prestasi->nama_pemenang }} ({{ $prestasi->tingkat }})</small>
        </div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalUpload">
            <i class='bx bx-cloud-upload me-1'></i> Upload Foto Dokumentasi
        </button>
    </div>

    {{-- Grid Foto --}}
    <div class="row g-3">
        {{-- Tampilkan Cover Utama dulu --}}
        <div class="col-6 col-md-3">
            <div class="card h-100 border border-primary">
                <div class="ratio ratio-4x3">
                    <img src="{{ asset('storage/prestasis/covers/'.$prestasi->foto) }}" class="object-fit-cover rounded-top" alt="Cover">
                </div>
                <div class="card-footer p-2 text-center bg-primary text-white small fw-bold">
                    Cover Utama
                </div>
            </div>
        </div>

        {{-- Foto-foto Tambahan --}}
        @foreach($prestasi->items as $item)
        <div class="col-6 col-md-3">
            <div class="card h-100 shadow-sm position-relative group-hover">
                <div class="ratio ratio-4x3">
                    <img src="{{ asset('storage/prestasis/items/'.$item->file) }}" class="object-fit-cover rounded" alt="Foto">
                </div>
                <div class="position-absolute top-0 end-0 m-2">
                    <form action="{{ route('admin.landing.prestasi.item.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus foto ini?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger btn-icon shadow rounded-circle"><i class='bx bx-x'></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Modal Upload Multiple --}}
<div class="modal fade" id="modalUpload" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.landing.prestasi.item.store', $prestasi->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Dokumentasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Foto (Bisa Banyak)</label>
                        <input type="file" class="form-control" name="files[]" multiple accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection