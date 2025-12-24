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

    <div class="row">
        {{-- KOLOM KIRI: FORM --}}
        <div class="col-md-8">
            <div class="nav-align-top mb-4">
                
                {{-- TAB NAVIGATION --}}
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-sambutan" aria-controls="navs-sambutan" aria-selected="true">
                            <i class="bx bx-message-square-detail me-1"></i> Sambutan
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-visimisi" aria-controls="navs-visimisi" aria-selected="false">
                            <i class="bx bx-target-lock me-1"></i> Visi, Misi & Program
                        </button>
                    </li>
                </ul>

                <div class="card">
                    <form action="{{ route('admin.landing.sambutan.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
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
                                        placeholder="Contoh: Mewujudkan Generasi Emas Berkarakter" required />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Isi Sambutan</label>
                                    <textarea class="form-control" id="editor-sambutan" name="isi_sambutan" rows="10" 
                                        placeholder="Tuliskan kata sambutan di sini..." required>{{ old('isi_sambutan', $sambutan->isi_sambutan ?? '') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Foto Kepala Sekolah</label>
                                    <input class="form-control" type="file" name="foto" accept="image/*" onchange="previewImage(event)">
                                    <div class="form-text">Format: JPG, PNG. Max 2MB.</div>
                                </div>
                            </div>

                            {{-- TAB 2: VISI, MISI & PROGRAM KERJA --}}
                            <div class="tab-pane fade" id="navs-visimisi" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Visi Sekolah</label>
                                    <textarea class="form-control" id="editor-visi" name="visi" rows="3" 
                                        placeholder="Tuliskan Visi Sekolah...">{{ old('visi', $sambutan->visi ?? '') }}</textarea>
                                    <div class="form-text">Visi adalah cita-cita jangka panjang sekolah.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Misi Sekolah</label>
                                    <textarea class="form-control" id="editor-misi" name="misi" rows="5" 
                                        placeholder="Tuliskan Misi Sekolah...">{{ old('misi', $sambutan->misi ?? '') }}</textarea>
                                    <div class="form-text">Misi adalah langkah-langkah untuk mencapai visi.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Program Kerja Unggulan</label>
                                    <textarea class="form-control" id="editor-program" name="program_kerja" rows="5" 
                                        placeholder="Tuliskan Program Kerja Unggulan...">{{ old('program_kerja', $sambutan->program_kerja ?? '') }}</textarea>
                                </div>
                            </div>

                        </div>
                        
                        <div class="card-footer d-flex justify-content-end pt-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Semua Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: PREVIEW FOTO --}}
        <div class="col-md-4">
            <div class="card mb-4">
                <h5 class="card-header">Foto Saat Ini</h5>
                <div class="card-body text-center">
                    @if(isset($sambutan) && $sambutan->foto)
                        <img src="{{ asset('storage/sambutan/'.$sambutan->foto) }}" 
                             alt="Kepala Sekolah" 
                             class="img-fluid rounded shadow-sm" 
                             id="preview-img">
                    @else
                        <img src="https://via.placeholder.com/300x400?text=Belum+Ada+Foto" 
                             class="img-fluid rounded shadow-sm opacity-50" 
                             id="preview-img">
                    @endif
                    <p class="mt-3 text-muted small">Preview foto Kepala Sekolah.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Opsional: Jika ingin menggunakan CKEditor untuk textarea agar bisa bold/list --}}
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    // Preview Image
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview-img');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    // Inisialisasi CKEditor (Jika ingin rich text editor)
    // Hapus bagian ini jika ingin menggunakan textarea biasa
    const editors = ['#editor-sambutan', '#editor-visi', '#editor-misi', '#editor-program'];
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