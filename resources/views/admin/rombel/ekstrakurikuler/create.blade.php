@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Rombongan Belajar / Ekstrakurikuler /</span> Tambah Data
</h4>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Form Tambah Ekstrakurikuler</h5>
    </div>
    <div class="card-body">
        <form action="#" method="POST">
            @csrf
            <div class="mb-3">
                <label for="nama_ekskul" class="form-label">Nama Ekskul</label>
                <input type="text" class="form-control" id="nama_ekskul" name="nama_ekskul" placeholder="Contoh: Pramuka">
            </div>
            <div class="mb-3">
                <label for="pembina" class="form-label">Pembina</label>
                <select id="pembina" name="pembina" class="form-select">
                    <option>Pilih Pembina...</option>
                    {{-- Data pembina (guru) akan dimuat dari database --}}
                </select>
            </div>
            <div class="mb-3">
                <label for="prasarana" class="form-label">Prasarana</label>
                <select id="prasarana" name="prasarana" class="form-select">
                    <option>Pilih Prasarana...</option>
                    {{-- Data prasarana (ruang/lapangan) akan dimuat dari database --}}
                </select>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.rombel.ekstrakurikuler.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection