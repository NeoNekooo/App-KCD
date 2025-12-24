@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">Landing /</span> Fasilitas Sekolah</h4>
            <small class="text-muted">Manajemen sarana dan prasarana sekolah</small>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bx bx-plus me-1"></i> Tambah Fasilitas
        </button>
    </div>

    <div class="row g-4">
        @forelse($fasilitas as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden group-action-card">
                
                {{-- Action Buttons (Edit & Delete) --}}
                <div class="position-absolute top-0 end-0 p-2" style="z-index: 10;">
                    <div class="d-flex gap-1">
                        <button type="button" 
                                class="btn btn-sm btn-light btn-icon shadow-sm opacity-75 hover-100 btn-edit-action"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEdit"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->nama_fasilitas }}"
                                data-deskripsi="{{ $item->deskripsi }}">
                            <i class="bx bx-pencil text-warning"></i>
                        </button>

                        <form action="{{ route('admin.landing.fasilitas.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus fasilitas ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light btn-icon shadow-sm opacity-75 hover-100 text-danger">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Gambar Fasilitas --}}
                <div class="fasilitas-img-wrapper bg-light cursor-pointer" 
                     onclick="showPreview('{{ $item->nama_fasilitas }}', '{{ $item->deskripsi }}', '{{ asset('storage/fasilitas/'.$item->foto) }}')"
                     data-bs-toggle="modal" 
                     data-bs-target="#modalPreview">
                    
                    <img src="{{ asset('storage/fasilitas/'.$item->foto) }}" 
                         class="card-img-top fasilitas-img" 
                         alt="{{ $item->nama_fasilitas }}" 
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/400x250?text=No+Image';">
                </div>

                {{-- Konten Card --}}
                <div class="card-body text-center p-3 cursor-pointer"
                     onclick="showPreview('{{ $item->nama_fasilitas }}', '{{ $item->deskripsi }}', '{{ asset('storage/fasilitas/'.$item->foto) }}')"
                     data-bs-toggle="modal" 
                     data-bs-target="#modalPreview">
                    
                    <h5 class="card-title text-truncate mb-2 text-primary">{{ $item->nama_fasilitas }}</h5>
                    <p class="card-text text-muted small mb-0 text-truncate">
                        {{ $item->deskripsi ?? 'Tidak ada deskripsi.' }}
                    </p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card py-5 text-center border-dashed">
                <div class="card-body">
                    <i class="bx bxs-buildings fs-1 text-muted mb-3"></i>
                    <h5>Belum ada Fasilitas</h5>
                    <p class="text-muted">Silakan tambahkan data fasilitas sekolah.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        @if($fasilitas instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $fasilitas->links() }}
        @endif
    </div>
</div>

{{-- MODAL PREVIEW / DETAIL --}}
<div class="modal fade" id="modalPreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title">Detail Fasilitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0 text-center">
                <img id="previewBigImg" src="" class="img-fluid rounded mb-3 shadow-sm" style="max-height: 250px; object-fit: contain;">
                <h4 id="previewNama" class="mb-2 text-primary"></h4>
                
                <div class="text-start bg-light p-3 rounded mb-3">
                    <small class="text-muted d-block fw-bold mb-1">Deskripsi:</small>
                    <p id="previewDeskripsi" class="text-dark small mb-0 text-justify"></p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREATE --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Fasilitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.landing.fasilitas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Fasilitas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_fasilitas" placeholder="Contoh: Laboratorium Komputer" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3" placeholder="Jelaskan fasilitas ini..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Fasilitas <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="foto" accept="image/*" required>
                        <div class="form-text">Format: JPG, PNG, JPEG. Max: 2MB.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Fasilitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Fasilitas</label>
                        <input type="text" class="form-control" id="editNama" name="nama_fasilitas" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti Foto (Opsional)</label>
                        <input type="file" class="form-control" name="foto" accept="image/*">
                        <div class="form-text">Biarkan kosong jika tidak ingin mengubah foto.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .fasilitas-img-wrapper {
        position: relative;
        height: 200px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .fasilitas-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    .cursor-pointer { cursor: pointer; }
    .group-action-card:hover .fasilitas-img {
        transform: scale(1.05);
    }
    .hover-100:hover { opacity: 1 !important; }
    .border-dashed { border: 2px dashed #d9dee3; }
</style>
@endpush

@push('scripts')
<script>
    // Fungsi Preview Detail
    function showPreview(nama, deskripsi, foto) {
        document.getElementById('previewNama').textContent = nama;
        document.getElementById('previewBigImg').src = foto;
        
        const deskripsiEl = document.getElementById('previewDeskripsi');
        deskripsiEl.textContent = (deskripsi && deskripsi !== 'null') ? deskripsi : "Tidak ada deskripsi.";
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi Tombol Edit
        const editButtons = document.querySelectorAll('.btn-edit-action');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.nama;
                const deskripsi = this.dataset.deskripsi;

                document.getElementById('editNama').value = nama;
                document.getElementById('editDeskripsi').value = deskripsi;

                // Update Action URL Form
                let updateUrl = "{{ route('admin.landing.fasilitas.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                document.getElementById('formEdit').action = updateUrl;
            });
        });
    });
</script>
@endpush

@endsection