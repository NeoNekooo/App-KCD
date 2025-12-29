@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">Landing /</span> Galeri Kegiatan</h4>
            <small class="text-muted">Kelola album foto dan video kegiatan sekolah</small>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bx bx-folder-plus me-1"></i> Buat Album Baru
        </button>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-4">
        @forelse($galeris as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden group-action-card hover-shadow transition-all">
                
                {{-- Badge Tanggal --}}
                @if($item->tanggal)
                <div class="position-absolute top-0 start-0 px-3 py-1 rounded-bottom-end shadow-sm bg-white text-primary small fw-bold" style="z-index: 5;">
                    <i class="bx bx-calendar me-1"></i> {{ $item->tanggal->format('d M Y') }}
                </div>
                @endif

                {{-- Gambar Cover Album --}}
                <div class="galeri-img-wrapper bg-light position-relative">
                    <img src="{{ asset('storage/galeris/covers/'.$item->foto) }}" 
                         class="card-img-top galeri-img" 
                         alt="{{ $item->judul }}" 
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/400x250?text=No+Cover';">
                    
                    {{-- Overlay Jumlah Item --}}
                    <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-gradient-dark text-white d-flex align-items-center">
                        <i class='bx bx-images me-1'></i> 
                        <span class="small fw-bold">{{ $item->items_count ?? 0 }} Item</span>
                    </div>
                </div>

                <div class="card-body p-3 d-flex flex-column">
                    <h5 class="card-title text-primary mb-1 text-truncate" title="{{ $item->judul }}">{{ $item->judul }}</h5>
                    <p class="card-text text-muted small text-truncate mb-3">{{ $item->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                    
                    <div class="mt-auto d-flex gap-2">
                        {{-- TOMBOL UTAMA: KELOLA ISI ALBUM --}}
                        <a href="{{ route('admin.landing.galeri.show', $item->id) }}" class="btn btn-sm btn-primary flex-grow-1">
                            <i class='bx bx-folder-open me-1'></i> Kelola Isi Album
                        </a>

                        {{-- Tombol Edit Info Album --}}
                        <button type="button" 
                                class="btn btn-sm btn-outline-warning btn-icon btn-edit-action"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEdit"
                                data-id="{{ $item->id }}"
                                data-judul="{{ $item->judul }}"
                                data-tanggal="{{ $item->tanggal ? $item->tanggal->format('Y-m-d') : '' }}"
                                data-deskripsi="{{ $item->deskripsi }}">
                            <i class="bx bx-pencil"></i>
                        </button>

                        {{-- Tombol Hapus Album --}}
                        <form action="{{ route('admin.landing.galeri.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus album ini beserta seluruh foto/video di dalamnya?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger btn-icon">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card py-5 text-center border-dashed bg-lighter">
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-label-secondary p-3 rounded-circle">
                            <i class="bx bx-photo-album fs-1"></i>
                        </span>
                    </div>
                    <h5 class="mb-1">Belum ada Album Kegiatan</h5>
                    <p class="text-muted mb-3">Buat album baru untuk mulai mengupload dokumentasi.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
                        <i class="bx bx-plus me-1"></i> Buat Album Pertama
                    </button>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $galeris->links() }}
    </div>
</div>

{{-- MODAL CREATE ALBUM --}}
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
                        <label class="form-label">Nama Album / Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="judul" placeholder="Contoh: HUT RI ke-79" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kegiatan</label>
                        <input type="date" class="form-control" name="tanggal">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Singkat</label>
                        <textarea class="form-control" name="deskripsi" rows="2" placeholder="Penjelasan singkat tentang kegiatan..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cover Album <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="foto" accept="image/*" required>
                        <div class="form-text">Gambar utama yang tampil di depan album.</div>
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

{{-- MODAL EDIT ALBUM --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Info Album</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Album / Kegiatan</label>
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
                        <div class="form-text">Biarkan kosong jika tidak ingin mengubah cover.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Info</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .galeri-img-wrapper {
        position: relative;
        height: 200px;
        overflow: hidden;
        border-bottom: 1px solid #eee;
    }
    .galeri-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .group-action-card:hover .galeri-img {
        transform: scale(1.08);
    }
    .bg-gradient-dark {
        background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    }
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
    .border-dashed { border: 2px dashed #d9dee3; }
    .bg-lighter { background-color: #fcfdfd; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Edit Button Click
        const editButtons = document.querySelectorAll('.btn-edit-action');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                
                document.getElementById('editJudul').value = this.dataset.judul;
                document.getElementById('editTanggal').value = this.dataset.tanggal;
                document.getElementById('editDeskripsi').value = this.dataset.deskripsi;

                // Update Form Action URL
                let updateUrl = "{{ route('admin.landing.galeri.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                document.getElementById('formEdit').action = updateUrl;
            });
        });
    });
</script>
@endpush

@endsection