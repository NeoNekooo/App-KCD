@extends('layouts.admin')

@section('title', 'Buat Paket Instrumen PKKS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">PKKS / Instrumen /</span> Buat Baru</h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-none border">
                <div class="card-header d-flex justify-content-between align-items-center border-bottom py-3">
                    <h5 class="mb-0">Detail Paket</h5>
                </div>
                <div class="card-body pt-4">
                    <form action="{{ route('admin.pkks.instrumen.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Instrumen</label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: PKKS Kepala Sekolah SMA 2024" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun</label>
                                <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Skala Nilai (Maks)</label>
                                <input type="number" name="skor_maks" class="form-control" value="4" placeholder="Misal: 4, 5, atau 10" required>
                                <small class="text-muted">Contoh: 4 berarti skala 1-4</small>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Simpan & Lanjut Kelola Soal</button>
                            <a href="{{ route('admin.pkks.instrumen.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
