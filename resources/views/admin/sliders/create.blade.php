@extends('layouts.admin')

@section('title', 'Tambah Slider')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Website /</span> <span class="text-muted fw-light">Slider Beranda /</span> Tambah Slider
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Tambah Slider</h5>
                    <a href="{{ route('admin.website.sliders.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.website.sliders.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="title">Judul Utama Slider</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" placeholder="Masukkan judul yang menarik..." required />
                            @error('title') <div class="text-danger mt-1"><small>{{ $message }}</small></div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="subtitle">Subjudul / Keterangan</label>
                            <textarea id="subtitle" class="form-control" name="subtitle" placeholder="Berikan penjelasan singkat mengenai slider ini..." rows="3">{{ old('subtitle') }}</textarea>
                            @error('subtitle') <div class="text-danger mt-1"><small>{{ $message }}</small></div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="order">Urutan Tampil (Unique)</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">#</span>
                                <input type="number" id="order" name="order" class="form-control" value="{{ old('order', 1) }}" required />
                            </div>
                            <div class="form-text">* Pastikan nomor urutan tidak sama dengan slider lain.</div>
                            @error('order') <div class="text-danger mt-1"><small>{{ $message }}</small></div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="image">Visual Slider</label>
                            <input class="form-control" type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)" required />
                            <div class="form-text">Rekomendasi ukuran: 1920x800px.</div>
                            @error('image') <div class="text-danger mt-1"><small>{{ $message }}</small></div> @enderror
                        </div>

                        <div class="mb-3" id="preview-container" style="display: none;">
                            <label class="form-label d-block">Preview Gambar</label>
                            <div style="border-radius: 8px; overflow: hidden; border: 1px solid #d9dee3; max-width: 400px;">
                                <img id="preview-img" style="width: 100%; height: auto; display: block;" alt="Preview" />
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan & Publikasikan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function previewImage(event) {
        var input = event.target;
        var reader = new FileReader();
        reader.onload = function() {
            var dataURL = reader.result;
            var output = document.getElementById('preview-img');
            output.src = dataURL;
            document.getElementById('preview-container').style.display = 'block';
        };
        if (input.files[0]) {
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
