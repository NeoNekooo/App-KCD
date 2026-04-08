@extends('layouts.admin')

@push('styles')
    <style>
        .card-premium {
            background: #fff;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 25px rgba(105, 108, 255, 0.08);
        }
        .animate-entry {
            animation: slideInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 animate-entry gap-3">
        <div>
            <h4 class="fw-bolder m-0 text-dark">Kelola Profil Frontend</h4>
            <span class="text-muted small">Atur Sejarah Singkat, Visi, dan Misi yang tampil di website publik pengunjung.</span>
        </div>
        <button type="submit" form="form-profil" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold d-flex align-items-center">
            <i class='bx bx-save me-2'></i> Simpan Konten
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class='bx bx-check-circle me-1'></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card card-premium animate-entry">
                <div class="card-body p-4 p-md-5">
                    <form id="form-profil" action="{{ route('admin.website.profil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        <div class="row g-4">
                            <div class="col-lg-8">
                                <label class="form-label small fw-bold text-primary text-uppercase mb-2"><i class='bx bx-history me-1'></i> Sejarah Singkat Instansi</label>
                                <textarea class="form-control ckeditor-classic" name="sejarah_singkat" rows="15">{{ old('sejarah_singkat', $instansi->sejarah_singkat) }}</textarea>
                            </div>
                            
                            <div class="col-lg-4">
                                <label class="form-label small fw-bold text-primary text-uppercase mb-2"><i class='bx bx-image me-1'></i> Foto Profil / Sejarah</label>
                                <div class="border rounded-4 p-3 text-center bg-light">
                                    @if ($instansi->foto_profil)
                                        <img src="{{ Storage::url($instansi->foto_profil) }}" id="previewFoto" class="img-fluid rounded mb-3 shadow-sm" style="max-height: 200px; object-fit: cover;">
                                    @else
                                        <img src="" id="previewFoto" class="img-fluid rounded mb-3 shadow-sm d-none" style="max-height: 200px; object-fit: cover;">
                                        <div id="fotoPlaceholder" class="text-muted mb-3 py-4">
                                            <i class='bx bx-image-add fs-1 opacity-50'></i>
                                            <div class="small fw-medium mt-2">Belum ada foto</div>
                                        </div>
                                    @endif
                                    
                                    <label for="uploadFoto" class="btn btn-sm btn-outline-primary w-100 fw-bold rounded-pill">
                                        <i class="bx bx-upload me-1"></i> Pilih Foto Baru
                                    </label>
                                    <input type="file" id="uploadFoto" name="foto_profil" class="d-none" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewImage(this, 'previewFoto', 'fotoPlaceholder')">
                                    <div class="small text-muted mt-2" style="font-size: 0.75rem;">Format JPG/PNG/WEBP (Max 2MB)</div>
                                </div>
                            </div>
                            
                            <hr class="my-5 border-light">

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-success text-uppercase mb-2"><i class='bx bx-target-lock me-1'></i> Visi Website</label>
                                <textarea class="form-control ckeditor-classic" name="visi" rows="8">{{ old('visi', $instansi->visi) }}</textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-info text-uppercase mb-2"><i class='bx bx-list-check me-1'></i> Misi Website</label>
                                <textarea class="form-control ckeditor-classic" name="misi" rows="8">{{ old('misi', $instansi->misi) }}</textarea>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- CKEditor 5 --}}
        <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
        
        {{-- Tweak Style untuk CKEditor --}}
        <style>
            .ck-editor__editable {
                min-height: 250px !important;
                border-radius: 0 0 0.5rem 0.5rem !important;
            }
            .ck-toolbar {
                border-radius: 0.5rem 0.5rem 0 0 !important;
            }
        </style>

        <script>
            document.querySelectorAll('.ckeditor-classic').forEach((el) => {
                ClassicEditor
                    .create(el, {
                        toolbar: ['heading', '|', 'bold', 'italic', 'underline', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });

            function previewImage(input, imgId, placeholderId) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = document.getElementById(imgId);
                        const placeholder = document.getElementById(placeholderId);
                        
                        img.src = e.target.result;
                        img.classList.remove('d-none');
                        if (placeholder) placeholder.classList.add('d-none');
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    @endpush
@endsection
