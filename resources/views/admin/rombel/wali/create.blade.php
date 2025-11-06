@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Rombongan Belajar / Wali Kelas /</span> Tambah Data
</h4>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Form Tambah Wali Kelas</h5>
    </div>
    <div class="card-body">
        <form action="#" method="POST">
            @csrf
            <div class="mb-3">
                <label for="nama_rombel" class="form-label">Nama Rombel</label>
                <select id="nama_rombel" name="nama_rombel" class="form-select">
                    <option>Pilih Rombel...</option>
                    {{-- Data Rombel akan dimuat dari database --}}
                </select>
            </div>
            <div class="mb-3">
                <label for="wali" class="form-label">Wali</label>
                <select id="wali" name="wali" class="form-select">
                    <option>Pilih Guru...</option>
                    {{-- Data guru akan dimuat dari database --}}
                </select>
            </div>
            <div class="mb-3">
                <label for="ruang" class="form-label">Ruang</label>
                <select id="ruang" name="ruang" class="form-select">
                    <option>Pilih Ruang...</option>
                    {{-- Data ruang akan dimuat dari database --}}
                </select>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.rombel.wali.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection