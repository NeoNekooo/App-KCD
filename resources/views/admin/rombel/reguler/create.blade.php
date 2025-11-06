@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Rombongan Belajar / Reguler /</span> Tambah Data
</h4>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Form Tambah Rombel Reguler</h5>
    </div>
    <div class="card-body">
        <form action="#" method="POST">
            @csrf
            <div class="row">
                {{-- Kolom Kiri --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jenis_rombel" class="form-label">Jenis Rombel</label>
                        <select id="jenis_rombel" name="jenis_rombel" class="form-select">
                            <option>Pilih Jenis Rombel...</option>
                            {{-- Pilihan dikosongkan, akan diisi dari database nanti --}}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tingkat" class="form-label">Tingkat Pendidikan</label>
                        <select id="tingkat" name="tingkat" class="form-select">
                            <option>Pilih Tingkat...</option>
                            {{-- Pilihan dikosongkan --}}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jurusan" class="form-label">Program/Kompetensi Keahlian</label>
                        <select id="jurusan" name="jurusan" class="form-select">
                            <option>Pilih Jurusan...</option>
                            {{-- Pilihan dikosongkan --}}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kurikulum" class="form-label">Kurikulum</label>
                        <select id="kurikulum" name="kurikulum" class="form-select">
                            <option>Pilih Kurikulum...</option>
                            {{-- Pilihan dikosongkan --}}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nama_rombel" class="form-label">Nama Rombel</label>
                        <input type="text" class="form-control" id="nama_rombel" name="nama_rombel" placeholder="Contoh: X RPL 1">
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="wali_kelas" class="form-label">Wali/Guru Kelas</label>
                        <select id="wali_kelas" name="wali_kelas" class="form-select">
                            <option>Pilih Guru...</option>
                            {{-- Pilihan dikosongkan --}}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ruang" class="form-label">Ruang</label>
                        <select id="ruang" name="ruang" class="form-select">
                            <option>Pilih Ruang...</option>
                            {{-- Pilihan dikosongkan --}}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="moving_class" class="form-label">Moving Class</label>
                        <select id="moving_class" name="moving_class" class="form-select">
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kebutuhan_khusus" class="form-label">Melayani Kebutuhan Khusus</label>
                        <select id="kebutuhan_khusus" name="kebutuhan_khusus" class="form-select">
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.rombel.reguler.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection