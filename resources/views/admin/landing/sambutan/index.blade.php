@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Landing /</span> Profil & Sambutan</h4>

    {{-- Alert Success --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ route('admin.landing.sambutan.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- KOLOM KIRI: FORM INPUT --}}
            <div class="col-md-8">
                <div class="nav-align-top mb-4">
                    
                    {{-- TAB NAVIGATION --}}
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-sambutan" aria-controls="navs-sambutan" aria-selected="true">
                                <i class="bx bx-message-square-detail me-1"></i> Sambutan Kepsek
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-visimisi" aria-controls="navs-visimisi" aria-selected="false">
                                <i class="bx bx-building-house me-1"></i> Visi, Misi & Sejarah
                            </button>
                        </li>
                    </ul>

                    <div class="card">
                        <div class="card-body tab-content">
                            
                            {{-- TAB 1: SAMBUTAN KEPALA SEKOLAH --}}
                            <div class="tab-pane fade show active" id="navs-sambutan" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">Nama Kepala Sekolah</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-user"></i></span>
                                        <input type="text" class="form-control" name="nama_kepala_sekolah" 
                                            value="{{ old('nama_kepala_sekolah', $sambutan->nama_kepala_sekolah ?? '') }}" 
                                            placeholder="Contoh: Dr. H. Budi Santoso, M.Pd" required />
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Judul Sambutan</label>
                                    <input type="text" class="form-control" name="judul_sambutan" 
                                        value="{{ old('judul_sambutan', $sambutan->judul_sambutan ?? '') }}" 
                                        placeholder="Contoh: Mewujudkan Generasi Emas" required />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Isi Sambutan</label>
                                    <textarea class="form-control" id="editor-sambutan" name="isi_sambutan" rows="10">{{ old('isi_sambutan', $sambutan->isi_sambutan ?? '') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Foto Kepala Sekolah</label>
                                    <input class="form-control" type="file" name="foto" accept="image/*" onchange="previewImage(event, 'preview-kepsek')">
                                    <div class="form-text">Format: JPG, PNG. Max 2MB.</div>
                                </div>
                            </div>

                            {{-- TAB 2: VISI, MISI, SEJARAH & FOTO GEDUNG --}}
                            <div class="tab-pane fade" id="navs-visimisi" role="tabpanel">
                                
                                {{-- INPUT FOTO GEDUNG (BARU) --}}
                                <div class="mb-4 p-3 bg-lighter rounded border border-dashed">
                                    <label class="form-label fw-bold text-dark"><i class='bx bx-image-add'></i> Foto Gedung / Sejarah</label>
                                    <input class="form-control" type="file" name="foto_gedung" accept="image/*" onchange="previewImage(event, 'preview-gedung')">
                                    <div class="form-text">Foto ini akan muncul di halaman Profil Sekolah (Bagian Sejarah).</div>
                                </div>

                                {{-- INPUT SEJARAH (BARU) --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Sejarah Sekolah</label>
                                    <textarea class="form-control" id="editor-sejarah" name="sejarah" rows="5" 
                                        placeholder="Ceritakan sejarah singkat sekolah...">{{ old('sejarah', $sambutan->sejarah ?? '') }}</textarea>
                                </div>

                                <hr class="my-4">

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Visi Sekolah</label>
                                    <textarea class="form-control" id="editor-visi" name="visi" rows="3">{{ old('visi', $sambutan->visi ?? '') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Misi Sekolah</label>
                                    <textarea class="form-control" id="editor-misi" name="misi" rows="5">{{ old('misi', $sambutan->misi ?? '') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Program Kerja Unggulan</label>
                                    <textarea class="form-control" id="editor-program" name="program_kerja" rows="5">{{ old('program_kerja', $sambutan->program_kerja ?? '') }}</textarea>
                                </div>
                            </div>

                        </div>
                        
                        <div class="card-footer d-flex justify-content-end pt-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Semua Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: PREVIEW FOTO --}}
            <div class="col-md-4">
                
                {{-- Preview Foto Kepsek --}}
                <div class="card mb-4">
                    <h5 class="card-header fs-6">Preview Foto Kepsek</h5>
                    <div class="card-body text-center">
                        @if(isset($sambutan) && $sambutan->foto)
                            <img src="{{ asset('storage/sambutan/'.$sambutan->foto) }}" 
                                 alt="Kepala Sekolah" 
                                 class="img-fluid rounded shadow-sm" 
                                 id="preview-kepsek">
                        @else
                            <img src="https://via.placeholder.com/300x400?text=Belum+Ada+Foto" 
                                 class="img-fluid rounded shadow-sm opacity-50" 
                                 id="preview-kepsek">
                        @endif
                    </div>
                </div>

                {{-- Preview Foto Gedung (BARU) --}}
                <div class="card mb-4">
                    <h5 class="card-header fs-6">Preview Foto Gedung</h5>
                    <div class="card-body text-center">
                        @if(isset($sambutan) && $sambutan->foto_gedung)
                            <img src="{{ asset('storage/sambutan/'.$sambutan->foto_gedung) }}" 
                                 alt="Gedung Sekolah" 
                                 class="img-fluid rounded shadow-sm" 
                                 id="preview-gedung">
                        @else
                            <img src="https://via.placeholder.com/400x300?text=Belum+Ada+Foto+Gedung" 
                                 class="img-fluid rounded shadow-sm opacity-50" 
                                 id="preview-gedung">
                        @endif
                        <p class="mt-2 text-muted small">Jika kosong, web akan mencoba mengambil dari Slider.</p>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    // Fungsi Preview Image Dinamis
    // parameter 'targetId' digunakan untuk membedakan preview kepsek dan gedung
    function previewImage(event, targetId) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById(targetId);
            if(output) {
                output.src = reader.result;
            }
        };
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // Inisialisasi CKEditor untuk semua textarea
    const editors = ['#editor-sambutan', '#editor-visi', '#editor-misi', '#editor-program', '#editor-sejarah'];
    editors.forEach(selector => {
        if(document.querySelector(selector)){
            ClassicEditor
                .create(document.querySelector(selector))
                .catch(error => {
                    console.error(error);
                });
        }
    });
</script>
@endpush
@endsection