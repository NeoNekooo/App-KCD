
@extends('layouts.admin')

@section('title', 'Tambah Master Kas/Bank')

@section('content')
<div class="card">
    <div class="card-header">Tambah Data Kas/Bank Baru</div>
    <div class="card-body">
        <form action="{{ route('bendahara.keuangan.kas-master.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="nama_kas" class="form-label">Nama Kas/Bank</label>
                <input type="text" name="nama_kas" id="nama_kas" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="saldo_awal" class="form-label">Saldo Awal</label>
                <input type="number" name="saldo_awal" id="saldo_awal" class="form-control" value="0">
            </div>

            <button type="submit" class="btn btn-primary">Simpan Kas</button>
            <a href="{{ route('bendahara.keuangan.kas-master.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
