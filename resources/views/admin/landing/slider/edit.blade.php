@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Slider /</span> Edit Slider</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Data Slider</h5>
                    <a href="{{ route('admin.landing.slider.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.landing.slider.update', $slider->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Judul Besar</label>
                                <input type="text" class="form-control" name="judul" value="{{ $slider->judul }}" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Urutan Tampil</label>
                                <input type="number" class="form-control" name="urutan" value="{{ $slider->urutan }}" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi Singkat</label>
                            <textarea class="form-control" name="deskripsi" rows="3">{{ $slider->deskripsi }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Gambar Saat Ini</label><br>
                            <img src="{{ Storage::url('sliders/'.$slider->gambar) }}" class="img-thumbnail mb-2" width="300">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ganti Gambar (Opsional)</label>
                            <input class="form-control @error('gambar') is-invalid @enderror" type="file" name="gambar" accept="image/*" />
                            <div class="form-text">Biarkan kosong jika tidak ingin mengubah gambar.</div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Slider</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection