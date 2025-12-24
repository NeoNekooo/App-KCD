@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">Landing /</span> Galeri Kegiatan</h4>
            <small class="text-muted">Dokumentasi kegiatan dan album foto sekolah</small>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bx bx-images me-1"></i> Tambah Album
        </button>
    </div>

    <div class="row g-4">
        @forelse($galeris as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden group-action-card">
                
                @if($item->tanggal)
                <div class="position-absolute top-0 start-0 px-3 py-1 rounded-bottom-end shadow-sm bg-white text-primary small fw-bold" style="z-index: 5;">
                    <i class="bx bx-calendar me-1"></i> {{ $item->tanggal->format('d M Y') }}
                </div>
                @endif

                <div class="position-absolute top-0 end-0 p-2" style="z-index: 10;">
                    <div class="d-flex gap-1">
                        <button type="button" 
                                class="btn btn-sm btn-light btn-icon shadow-sm opacity-75 hover-100 btn-edit-action"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEdit"
                                data-id="{{ $item->id }}"
                                data-judul="{{ $item->judul }}"
                                data-tanggal="{{ $item->tanggal ? $item->tanggal->format('Y-m-d') : '' }}"
                                data-deskripsi="{{ $item->deskripsi }}"
                                data-foto="{{ asset('storage/galeris/'.$item->foto) }}">
                            <i class="bx bx-pencil text-warning"></i>
                        </button>

                        <form action="{{ route('admin.landing.galeri.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus album ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light btn-icon shadow-sm opacity-75 hover-100 text-danger">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="galeri-img-wrapper bg-light">
                    <img src="{{ asset('storage/galeris/'.$item->foto) }}" 
                         class="card-img-top galeri-img" 
                         alt="{{ $item->judul }}" 
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/400x250?text=No+Image';">
                </div>

                <div class="card-body p-3">
                    <h5 class="card-title text-primary mb-1 text-truncate">{{ $item->judul }}</h5>
                    <p class="card-text text-muted small text-truncate">{{ $item->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card py-5 text-center border-dashed">
                <div class="card-body">
                    <i class="bx bx-photo-album fs-1 text-muted mb-3"></i>
                    <h5>Belum ada Album Kegiatan</h5>
                    <p class="text-muted">Yuk, upload dokumentasi kegiatan sekolah!</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $galeris->links() }}
    </div>
</div>

<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Album Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.landing.galeri.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kegiatan / Album <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="judul" placeholder="Contoh: Perpisahan Angkatan 2024" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kegiatan</label>
                        <input type="date" class="form-control" name="tanggal">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Singkat</label>
                        <textarea class="form-control" name="deskripsi" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cover Album <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="foto" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Album</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Album</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kegiatan / Album</label>
                        <input type="text" class="form-control" id="editJudul" name="judul" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kegiatan</label>
                        <input type="date" class="form-control" id="editTanggal" name="tanggal">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Singkat</label>
                        <textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti Cover (Opsional)</label>
                        <input type="file" class="form-control" name="foto" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Album</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .galeri-img-wrapper {
        position: relative;
        height: 220px; /* Sedikit lebih tinggi dari prestasi */
        overflow: hidden;
    }
    .galeri-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    .group-action-card:hover .galeri-img {
        transform: scale(1.05);
    }
    .hover-100:hover { opacity: 1 !important; }
    .border-dashed { border: 2px dashed #d9dee3; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit-action');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                
                document.getElementById('editJudul').value = this.dataset.judul;
                document.getElementById('editTanggal').value = this.dataset.tanggal;
                document.getElementById('editDeskripsi').value = this.dataset.deskripsi;

                let updateUrl = "{{ route('admin.landing.galeri.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                document.getElementById('formEdit').action = updateUrl;
            });
        });
    });
</script>
@endpush

@endsection