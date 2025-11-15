@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kesiswaan / Data Siswa /</span> Tambah Data</h4>

{{-- Tag <form> sekarang membungkus langsung @include --}}
<form action="{{ route('admin.kesiswaan.siswa.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    @include('admin.kesiswaan.siswa._form-tabs')

    {{-- Tombol Simpan dipindahkan ke dalam file _form-tabs atau bisa diletakkan di sini jika perlu --}}
    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Simpan Data Siswa</button>
        <a href="{{ route('admin.kesiswaan.siswa.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
@endsection

