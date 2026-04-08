@extends('layouts.admin')

@section('title', 'Kelola Sambutan Pimpinan')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Website /</span> Pesan Sambutan
    </h4>

    <form action="{{ route('admin.website.welcome.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- CARD 1: Visual Pimpinan --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bx-user-circle bx-sm text-primary me-2"></i>
                <h5 class="mb-0">Data & Foto Pimpinan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- LEFT: Foto Preview --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Foto Pimpinan</label>
                        <div class="text-center p-3" style="border: 2px dashed #d9dee3; border-radius: 12px; background: #f8f9fa;">
                            <div style="width: 200px; height: 260px; margin: 0 auto; border-radius: 12px; overflow: hidden; background: #eef0f7;">
                                @if($welcome && $welcome->image)
                                    <img id="preview-img" src="{{ asset('storage/' . $welcome->image) }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Preview" />
                                @else
                                    <img id="preview-img" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;" alt="Preview" />
                                    <div id="preview-placeholder" style="height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column; color: #a1acb8;">
                                        <i class="bx bx-camera bx-lg mb-2"></i>
                                        <small class="fw-semibold">Upload Foto</small>
                                        <small class="text-muted mt-1">Rasio 4:5 portrait</small>
                                    </div>
                                @endif
                            </div>
                            <input class="form-control mt-3" type="file" name="image" accept="image/*" onchange="previewImage(event)" />
                            <div class="form-text text-muted mt-1">Format: JPG/PNG. Maks: 2MB</div>
                        </div>
                    </div>

                    {{-- RIGHT: Nama & Jabatan --}}
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="pimpinan_name">
                                <i class="bx bx-id-card text-primary me-1"></i> Nama Lengkap Pimpinan
                            </label>
                            <input type="text" id="pimpinan_name" name="pimpinan_name" value="{{ old('pimpinan_name', optional($welcome)->pimpinan_name) }}" class="form-control form-control-lg" placeholder="Contoh: Drs. H. Nama Lengkap, M.Pd.">
                            <div class="form-text">Nama lengkap beserta gelar depan dan belakang</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="pimpinan_role">
                                <i class="bx bx-briefcase text-primary me-1"></i> Jabatan Struktural
                            </label>
                            <input type="text" id="pimpinan_role" name="pimpinan_role" value="{{ old('pimpinan_role', optional($welcome)->pimpinan_role) }}" class="form-control form-control-lg" placeholder="Contoh: Kepala Cabang Dinas Pendidikan">
                            <div class="form-text">Jabatan resmi yang akan ditampilkan di halaman beranda</div>
                        </div>

                        {{-- Preview Card --}}
                        <div class="mt-4 p-3 rounded-3" style="background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%);">
                            <small class="text-white-50 fw-bold text-uppercase" style="font-size: 9px; letter-spacing: 2px;">Preview Tampilan di Website</small>
                            <div class="mt-2">
                                <h6 class="text-white fw-bold mb-1" id="preview-name">{{ optional($welcome)->pimpinan_name ?? 'Nama Pimpinan' }}</h6>
                                <small class="text-info" style="font-size: 10px; letter-spacing: 1.5px;" id="preview-role">{{ strtoupper(optional($welcome)->pimpinan_role ?? 'JABATAN') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 2: Konten Sambutan --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bx-edit bx-sm text-primary me-2"></i>
                <h5 class="mb-0">Konten Sambutan</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="title">
                        <i class="bx bx-heading text-primary me-1"></i> Judul Sambutan
                    </label>
                    <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ old('title', optional($welcome)->title) }}" placeholder="Contoh: Selamat Datang di Portal Resmi KCD" required />
                    <div class="form-text">Judul utama yang tampil besar di halaman sambutan</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="editor">
                        <i class="bx bx-text text-primary me-1"></i> Narasi Sambutan
                    </label>
                    <textarea name="content" id="editor">{{ old('content', optional($welcome)->content) }}</textarea>
                    <div class="form-text">Gunakan toolbar di atas untuk format teks (bold, italic, list, dll)</div>
                </div>
            </div>
        </div>

        {{-- SUBMIT --}}
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary px-5">
                <i class="bx bx-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
    <style>
        .ck-editor__editable {
            min-height: 350px !important;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            ClassicEditor
                .create(document.querySelector('#editor'), {
                    toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ]
                })
                .catch(error => {
                    console.error(error);
                });

            // Live preview nama & jabatan
            const nameInput = document.getElementById('pimpinan_name');
            const roleInput = document.getElementById('pimpinan_role');
            const previewName = document.getElementById('preview-name');
            const previewRole = document.getElementById('preview-role');

            if(nameInput && previewName) {
                nameInput.addEventListener('input', () => {
                    previewName.textContent = nameInput.value || 'Nama Pimpinan';
                });
            }
            if(roleInput && previewRole) {
                roleInput.addEventListener('input', () => {
                    previewRole.textContent = (roleInput.value || 'JABATAN').toUpperCase();
                });
            }
        });

        function previewImage(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function() {
                var dataURL = reader.result;
                var output = document.getElementById('preview-img');
                var placeholder = document.getElementById('preview-placeholder');
                
                output.src = dataURL;
                output.style.display = 'block';
                
                if(placeholder) {
                    placeholder.style.display = 'none';
                }
            };
            if (input.files[0]) {
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
