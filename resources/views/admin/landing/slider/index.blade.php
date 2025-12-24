@extends('layouts.admin')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">Landing /</span> Slider Visual</h4>
            <small class="text-muted">Klik gambar untuk memperbesar (Preview)</small>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bx bx-plus me-1"></i> Tambah Slider
        </button>
    </div>

    <div class="row g-4">
        @forelse($sliders as $slider)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 overflow-hidden position-relative group-action-card">
                
                {{-- BAGIAN INI DIMODIFIKASI AGAR BISA DIKLIK --}}
                <div class="slider-preview-img cursor-pointer" 
                     onclick="showPreview('{{ asset('storage/sliders/'.$slider->gambar) }}', '{{ $slider->judul }}')"
                     style="background-image: url('{{ asset('storage/sliders/'.$slider->gambar) }}');">
                    
                    {{-- Overlay Icon Mata (Zoom) saat dihover --}}
                    <div class="slider-overlay d-flex align-items-center justify-content-center">
                        <i class="bx bx-zoom-in text-white fs-1 opacity-50"></i>
                    </div>
                </div>

                <div class="card-body position-absolute bottom-0 start-0 text-white w-100 p-3" style="z-index: 2; pointer-events: none;">
                    <span class="badge bg-primary mb-2">Urutan: #{{ $slider->urutan }}</span>
                    <h5 class="card-title text-white mb-1 text-truncate">{{ $slider->judul ?? 'Tanpa Judul' }}</h5>
                </div>

                <div class="position-absolute top-0 end-0 p-2" style="z-index: 3;">
                    <div class="d-flex gap-2">
                        <button type="button" 
                                class="btn btn-sm btn-light btn-icon shadow-sm opacity-75 hover-100 btn-edit"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEdit"
                                data-id="{{ $slider->id }}"
                                data-judul="{{ $slider->judul }}"
                                data-deskripsi="{{ $slider->deskripsi }}"
                                data-urutan="{{ $slider->urutan }}">
                            <i class="bx bx-pencil text-dark"></i>
                        </button>

                        <form action="{{ route('admin.landing.slider.destroy', $slider->id) }}" method="POST" onsubmit="return confirm('Hapus slider ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger btn-icon shadow-sm opacity-75 hover-100">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card py-5 text-center border-dashed">
                <div class="card-body">
                    <i class="bx bx-images fs-1 text-muted mb-3"></i>
                    <h5>Belum ada Slider</h5>
                    <p class="text-muted">Klik tombol "Tambah Slider" di pojok kanan atas.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- MODAL PREVIEW GAMBAR (POP UP) --}}
<div class="modal fade" id="modalPreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content bg-transparent shadow-none border-0">
            <div class="modal-header border-0 p-0">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 5;"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img id="previewImageFull" src="" class="img-fluid rounded shadow-lg" style="max-height: 80vh;">
                <h5 id="previewTitle" class="text-white mt-3 text-shadow"></h5>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREATE (Sama seperti sebelumnya) --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Slider Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.landing.slider.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-8 mb-3">
                            <label class="form-label">Judul Slider</label>
                            <input type="text" class="form-control" name="judul" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label">Urutan</label>
                            <input type="number" class="form-control" name="urutan" value="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar Banner <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="gambar" accept="image/*" required>
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

{{-- MODAL EDIT (Preview Dihapus Sesuai Request) --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Slider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    {{-- PREVIEW LAMA DIHAPUS DISINI --}}
                    
                    <div class="row">
                        <div class="col-8 mb-3">
                            <label class="form-label">Judul Slider</label>
                            <input type="text" class="form-control" id="editJudul" name="judul">
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label">Urutan</label>
                            <input type="number" class="form-control" id="editUrutan" name="urutan">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti Gambar (Opsional)</label>
                        <input type="file" class="form-control" name="gambar" accept="image/*">
                        <div class="form-text small">Biarkan kosong jika tidak ingin mengubah gambar.</div>
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
    .slider-preview-img {
        height: 200px;
        background-color: #f5f5f9;
        background-size: cover;
        background-position: center;
        position: relative;
        transition: transform 0.2s;
    }
    
    /* Efek Hover untuk Zoom */
    .slider-preview-img:hover {
        transform: scale(1.02);
    }
    
    .cursor-pointer {
        cursor: pointer;
    }

    /* Overlay hitam transparan */
    .slider-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.3);
        z-index: 1;
        opacity: 0;
        transition: opacity 0.3s;
    }

    /* Munculkan overlay saat hover */
    .slider-preview-img:hover .slider-overlay {
        opacity: 1;
    }

    .hover-100:hover { opacity: 1 !important; }
    .text-shadow { text-shadow: 0 2px 4px rgba(0,0,0,0.8); }
</style>
@endpush

@push('scripts')
<script>
    // Fungsi untuk memunculkan Modal Preview
    function showPreview(imageUrl, title) {
        // Set sumber gambar di modal
        document.getElementById('previewImageFull').src = imageUrl;
        // Set judul (opsional)
        document.getElementById('previewTitle').innerText = title;
        
        // Panggil Modal Bootstrap secara manual
        var myModal = new bootstrap.Modal(document.getElementById('modalPreview'));
        myModal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const judul = this.dataset.judul;
                const deskripsi = this.dataset.deskripsi;
                const urutan = this.dataset.urutan;
                // Kita tidak butuh gambarUrl di edit lagi

                document.getElementById('editJudul').value = judul;
                document.getElementById('editDeskripsi').value = deskripsi;
                document.getElementById('editUrutan').value = urutan;
                
                let updateUrl = "{{ route('admin.landing.slider.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                document.getElementById('formEdit').action = updateUrl;
            });
        });
    });
</script>
@endpush

@endsection