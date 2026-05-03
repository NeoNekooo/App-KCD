@extends('layouts.admin')

@section('title', 'Buat Paket Instrumen PKKS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary py-4 text-center position-relative">
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-white opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 10px 10px;"></div>
                    <i class="bx bx-file-blank text-white mb-2" style="font-size: 3rem;"></i>
                    <h5 class="mb-0 text-white fw-bold">Buat Paket Instrumen Baru</h5>
                    <p class="text-white text-opacity-75 mb-0 small">Siapkan wadah untuk indikator penilaian PKKS</p>
                </div>
                <div class="card-body pt-5 px-4 px-md-5">
                    <form action="{{ route('admin.pkks.instrumen.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark"><i class="bx bx-rename me-1"></i>Nama Instrumen</label>
                            <input type="text" name="nama" class="form-control form-control-lg border-2" 
                                   placeholder="Contoh: PKKS Kepala Sekolah SMA 2024" required>
                            <div class="form-text text-muted">Gunakan nama yang deskriptif dan mudah dikenali.</div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark"><i class="bx bx-calendar me-1"></i>Tahun Berlaku</label>
                                <div class="input-group input-group-merge border-2">
                                    <span class="input-group-text"><i class="bx bx-time"></i></span>
                                    <input type="number" name="tahun" class="form-control form-control-lg" value="{{ date('Y') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark"><i class="bx bx-list-check me-1"></i>Skala Nilai (Maks)</label>
                                <select name="skor_maks" class="form-select form-select-lg border-2" required>
                                    <option value="4" selected>Skala 1 - 4 (Default)</option>
                                    <option value="5">Skala 1 - 5</option>
                                    <option value="10">Skala 1 - 10</option>
                                    <option value="100">Skala 1 - 100</option>
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 bg-label-info d-flex align-items-center mb-5 p-3">
                            <i class="bx bx-info-circle me-3 fs-3"></i>
                            <div class="small">
                                Setelah disimpan, kamu bisa langsung menambahkan butir-butir soal melalui fitur <strong>Manual</strong> atau <strong>Import Excel</strong>.
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                            <a href="{{ route('admin.pkks.instrumen.index') }}" class="btn btn-label-secondary px-4 py-2">
                                <i class="bx bx-arrow-back me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-5 py-2 shadow">
                                <i class="bx bx-save me-1"></i> Simpan & Lanjut Kelola Soal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus, .form-select:focus {
        border-color: #696cff !important;
        box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.1) !important;
    }
    .card { border-radius: 15px !important; overflow: hidden; }
    .card-header { border-radius: 0 !important; }
</style>
@endsection
