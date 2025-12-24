@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Slider /</span> Tambah Baru</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Tambah Slider</h5>
                    <a href="{{ route('admin.landing.slider.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.landing.slider.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Judul Besar</label>
                                <input type="text" class="form-control" name="judul" placeholder="Contoh: PPDB Telah Dibuka" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Urutan Tampil</label>
                                <input type="number" class="form-control" name="urutan" value="1" />
                                <small class="text-muted">Angka 1 akan muncul paling awal.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi Singkat</label>
                            <textarea class="form-control" name="deskripsi" rows="3" placeholder="Teks kecil di bawah judul..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Gambar Slider <span class="text-danger">*</span></label>
                            <input class="form-control @error('gambar') is-invalid @enderror" type="file" name="gambar" accept="image/*" required />
                            @error('gambar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Format: JPG, PNG. Max: 2MB. Disarankan ukuran 1920x800px.</div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Slider</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection