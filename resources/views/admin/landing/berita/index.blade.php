@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">Landing /</span> Berita & Artikel</h4>
            <small class="text-muted">Kabar terbaru dan prestasi sekolah</small>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bx bx-pencil me-1"></i> Tulis Berita
        </button>
    </div>

    <div class="row g-4">
        @forelse($beritas as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden group-action-card">
                
                <div class="position-absolute top-0 start-0 px-3 py-1 rounded-bottom-end shadow-sm text-white small fw-bold" 
                     style="z-index: 5; background: {{ $item->status == 'published' ? '#71dd37' : '#8592a3' }};">
                    {{ ucfirst($item->status) }}
                </div>

                <div class="position-absolute top-0 end-0 p-2" style="z-index: 10;">
                    <div class="d-flex gap-1">
                        <button type="button" 
                                class="btn btn-sm btn-light btn-icon shadow-sm opacity-75 hover-100 btn-edit-action"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEdit"
                                data-id="{{ $item->id }}"
                                data-judul="{{ $item->judul }}"
                                data-isi="{{ $item->isi }}"
                                data-status="{{ $item->status }}"
                                data-gambar="{{ asset('storage/beritas/'.$item->gambar) }}">
                            <i class="bx bx-edit text-warning"></i>
                        </button>

                        <form action="{{ route('admin.landing.berita.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus berita ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light btn-icon shadow-sm opacity-75 hover-100 text-danger">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="berita-img-wrapper bg-light">
                    <img src="{{ asset('storage/beritas/'.$item->gambar) }}" 
                         class="card-img-top berita-img" 
                         alt="{{ $item->judul }}" 
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/400x250?text=No+Image';">
                </div>

                <div class="card-body p-3 d-flex flex-column">
                    <small class="text-muted mb-2">
                        <i class="bx bx-calendar me-1"></i> {{ $item->created_at->format('d M Y') }}
                        &nbsp;|&nbsp; 
                        <i class="bx bx-user me-1"></i> {{ $item->penulis }}
                    </small>
                    <h5 class="card-title text-primary mb-2 text-truncate" title="{{ $item->judul }}">{{ $item->judul }}</h5>
                    <p class="card-text text-muted small mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $item->ringkasan }}
                    </p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card py-5 text-center border-dashed">
                <div class="card-body">
                    <i class="bx bx-news fs-1 text-muted mb-3"></i>
                    <h5>Belum ada Berita</h5>
                    <p class="text-muted">Mulai tulis artikel atau pengumuman sekolah.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $beritas->links() }}
    </div>
</div>

<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tulis Berita Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCreate" action="{{ route('admin.landing.berita.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Judul Berita <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="judul" placeholder="Judul artikel..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="published">Published (Tayang)</option>
                                <option value="draft">Draft (Simpan Dulu)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Isi Berita <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="editor-berita-create" name="isi" rows="8" placeholder="Tulis isi berita lengkap di sini..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gambar Thumbnail <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="gambar" accept="image/*" required>
                        <div class="form-text">Format: JPG, PNG, WEBP. Max: 2MB.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Publish Berita</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <img id="previewEditImg" src="" class="img-fluid rounded mb-2 shadow-sm" style="max-height: 200px; object-fit: cover;">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Judul Berita</label>
                            <input type="text" class="form-control" id="editJudul" name="judul" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Isi Berita</label>
                        <textarea class="form-control" id="editor-berita-edit" name="isi" rows="8" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ganti Gambar (Opsional)</label>
                        <input type="file" class="form-control" name="gambar" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Berita</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .berita-img-wrapper {
        position: relative;
        height: 200px;
        overflow: hidden;
    }
    .berita-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    .group-action-card:hover .berita-img {
        transform: scale(1.05);
    }
    .hover-100:hover { opacity: 1 !important; }
    .border-dashed { border: 2px dashed #d9dee3; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    let editorBeritaCreate = null;
    let editorBeritaEdit = null;

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initializing CKEditor...');

        // Inisialisasi CKEditor untuk modal create
        if (document.querySelector('#editor-berita-create')) {
            ClassicEditor
                .create(document.querySelector('#editor-berita-create'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo']
                })
                .then(editor => {
                    editorBeritaCreate = editor;
                    console.log('CKEditor Create initialized');
                })
                .catch(error => {
                    console.error('Error CKEditor Create:', error);
                });
        }

        // Inisialisasi CKEditor untuk modal edit
        if (document.querySelector('#editor-berita-edit')) {
            ClassicEditor
                .create(document.querySelector('#editor-berita-edit'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo']
                })
                .then(editor => {
                    editorBeritaEdit = editor;
                    console.log('CKEditor Edit initialized');
                })
                .catch(error => {
                    console.error('Error CKEditor Edit:', error);
                });
        }

        // Handle form submit untuk Modal Create
        const formCreate = document.querySelector('#formCreate');
        if (formCreate) {
            formCreate.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form Create submitted');
                
                // Validasi CKEditor Create
                if (editorBeritaCreate) {
                    const isiBerita = editorBeritaCreate.getData();
                    console.log('CKEditor data:', isiBerita);
                    
                    if (isiBerita.trim() === '') {
                        alert('Isi berita tidak boleh kosong!');
                        return false;
                    }
                    
                    // Set value ke textarea asli
                    document.querySelector('#editor-berita-create').value = isiBerita;
                }
                
                // Submit form
                this.submit();
            });
        }

        // Handle form submit untuk Modal Edit
        const formEdit = document.querySelector('#formEdit');
        if (formEdit) {
            formEdit.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form Edit submitted');
                
                // Validasi CKEditor Edit
                if (editorBeritaEdit) {
                    const isiBerita = editorBeritaEdit.getData();
                    console.log('CKEditor edit data:', isiBerita);
                    
                    if (isiBerita.trim() === '') {
                        alert('Isi berita tidak boleh kosong!');
                        return false;
                    }
                    
                    // Set value ke textarea asli
                    document.querySelector('#editor-berita-edit').value = isiBerita;
                }
                
                // Submit form
                this.submit();
            });
        }

        // Script Modal Edit - Populate form saat button diklik
        const editButtons = document.querySelectorAll('.btn-edit-action');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const judul = this.dataset.judul;
                const isi = this.dataset.isi;
                const status = this.dataset.status;
                const fotoUrl = this.dataset.gambar;

                document.getElementById('editJudul').value = judul;
                
                // Tunggu editor ready sebelum set data
                if (editorBeritaEdit) {
                    editorBeritaEdit.setData(isi);
                } else {
                    console.warn('Editor Edit belum ready');
                }
                
                document.getElementById('editStatus').value = status;
                document.getElementById('previewEditImg').src = fotoUrl;

                let updateUrl = "{{ route('admin.landing.berita.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                document.getElementById('formEdit').action = updateUrl;
            });
        });
    });
</script>
@endpush

@endsection