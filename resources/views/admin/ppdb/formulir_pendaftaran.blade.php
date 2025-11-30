@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">PPDB /</span> Calon Siswa
</h4>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">{{ isset($formulir) ? 'Edit Calon Siswa' : 'Tambah Calon Siswa' }}</h5>
                <div class="ms-auto d-flex gap-2">
                    <button type="submit" name="action" value="update" 
                            form="calonSiswaForm" class="btn btn-primary d-flex align-items-center">
                        <i class='bx bx-save me-1'></i> Simpan
                    </button>
                    <button type="submit" name="action" value="create" 
                            form="calonSiswaForm" class="btn btn-success d-flex align-items-center">
                        <i class='bx bx-plus me-1'></i> Baru
                    </button>
                </div>
            </div>

            <div class="card-body">
                <form id="calonSiswaForm" method="POST" 
                      action="{{ isset($formulir) 
                                  ? route('admin.ppdb.formulir-ppdb.update', $formulir->id) 
                                  : route('admin.ppdb.formulir-ppdb.store') }}">
                    @csrf
                    @if(isset($formulir))
                        @method('PUT')
                    @endif

                    {{-- 🔹 Hidden Fields Tahun & Tingkat --}}
                    <input type="hidden" name="tahun_id" value="{{ $tahunAktif->id ?? '' }}">
                    <input type="hidden" name="tingkat" value="{{ $tingkatAktif->tingkat ?? '' }}">

                    <div class="row">

                        {{-- 🔹 Kolom 1 --}}
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" placeholder="Masukkan Nama Lengkap"
                                       value="{{ old('nama_lengkap', $formulir->nama_lengkap ?? '') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">NISN</label>
                                <input type="text" name="nisn" class="form-control" placeholder="NISN"
                                       value="{{ old('nisn', $formulir->nisn ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">NPUN</label>
                                <input type="text" name="npun" class="form-control" placeholder="NPUN"
                                       value="{{ old('npun', $formulir->npun ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <div class="d-flex gap-3 mt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" value="L"
                                            {{ old('jenis_kelamin', $formulir->jenis_kelamin ?? '') == 'L' ? 'checked' : '' }}>
                                        <label class="form-check-label">Laki-laki</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" value="P"
                                            {{ old('jenis_kelamin', $formulir->jenis_kelamin ?? '') == 'P' ? 'checked' : '' }}>
                                        <label class="form-check-label">Perempuan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" placeholder="Tempat Lahir"
                                       value="{{ old('tempat_lahir', $formulir->tempat_lahir ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tgl_lahir" class="form-control"
                                       value="{{ old('tgl_lahir', $formulir->tgl_lahir ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Ayah</label>
                                <input type="text" name="nama_ayah" class="form-control" placeholder="Nama Ayah"
                                       value="{{ old('nama_ayah', $formulir->nama_ayah ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Ibu</label>
                                <input type="text" name="nama_ibu" class="form-control" placeholder="Nama Ibu"
                                       value="{{ old('nama_ibu', $formulir->nama_ibu ?? '') }}">
                            </div>
                        </div>

                        {{-- 🔹 Kolom 2 --}}
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <input type="text" name="alamat" class="form-control" placeholder="Alamat Rumah"
                                       value="{{ old('alamat', $formulir->alamat ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Desa</label>
                                <input type="text" name="desa" class="form-control" placeholder="Desa"
                                       value="{{ old('desa', $formulir->desa ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kecamatan</label>
                                <input type="text" name="kecamatan" class="form-control" placeholder="Kecamatan"
                                       value="{{ old('kecamatan', $formulir->kecamatan ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kabupaten</label>
                                <input type="text" name="kabupaten" class="form-control" placeholder="Kabupaten"
                                       value="{{ old('kabupaten', $formulir->kabupaten ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Provinsi</label>
                                <input type="text" name="provinsi" class="form-control" placeholder="Provinsi"
                                       value="{{ old('provinsi', $formulir->provinsi ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" name="kode_pos" class="form-control" placeholder="Kode Pos"
                                       value="{{ old('kode_pos', $formulir->kode_pos ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kontak</label>
                                <input type="text" name="kontak" class="form-control" placeholder="No HP / WA"
                                       value="{{ old('kontak', $formulir->kontak ?? '') }}">
                            </div>
                        </div>

                        {{-- 🔹 Kolom 3 --}}
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Asal Sekolah</label>
                                <input type="text" name="asal_sekolah" class="form-control" placeholder="Asal Sekolah"
                                       value="{{ old('asal_sekolah', $formulir->asal_sekolah ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kelas</label>
                                <select name="kelas" class="form-select">
                                    <option value="">- Pilih Kelas -</option>
                                    @foreach($kelasAsal as $kelas)
                                    <option value="{{ $kelas }}" {{ old('kelas', $formulir->kelas ?? '') == $kelas ? 'selected' : '' }}>
                                        {{ $kelas }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            @if($tingkatAktif->tingkat == 10)
                                <div class="mb-3">
                                    <label class="form-label">Jurusan Yang Diminati</label>
                                    <select name="jurusan" class="form-select">
                                        <option value="">- Pilih Jurusan -</option>
                                        @foreach($jurusans as $jurusan)
                                            <option value="{{ $jurusan->kompetensi }}"
                                                {{ old('jurusan', $formulir->jurusan ?? '') == $jurusan->kompetensi ? 'selected' : '' }}>
                                                {{ $jurusan->kompetensi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Ukuran Pakaian</label>
                                <select name="ukuran_pakaian" class="form-select">
                                    <option value="">- Pilih Ukuran -</option>
                                    @foreach(['S','M','L','XL','XXL','XXXL','JB'] as $size)
                                        <option value="{{ $size }}" {{ old('ukuran_pakaian', $formulir->ukuran_pakaian ?? '') == $size ? 'selected' : '' }}>
                                            {{ $size }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pembayaran</label>
                                <input type="number" name="pembayaran" class="form-control" placeholder="Jumlah Pembayaran"
                                       value="{{ old('pembayaran', $formulir->pembayaran ?? 0) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jalur Pendaftaran</label>
                                <select name="jalur_id" id="jalurPendaftaran" class="form-select">
                                    <option value="">- Pilih Jalur -</option>
                                    @foreach($jalurs as $jalur)
                                        <option value="{{ $jalur->id }}" {{ old('jalur_id', $formulir->jalur_id ?? '') == $jalur->id ? 'selected' : '' }}>
                                            {{ $jalur->jalur }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Syarat Pendaftaran</label>
                                <div id="syaratContainer" class="border rounded p-3 bg-light">Silahkan pilih jalur terlebih dahulu</div>
                            </div>
                        </div>

                    </div> {{-- end row --}}
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const jalurSelect = document.getElementById('jalurPendaftaran');
    const syaratContainer = document.getElementById('syaratContainer');
    const selectedSyarats = @json(isset($formulir) ? $formulir->syarat->pluck('id')->toArray() : []);

    function loadSyarat(jalurId, selected=[]) {
        if (!jalurId) return syaratContainer.innerHTML = "Silahkan pilih jalur terlebih dahulu";

        fetch(`/admin/ppdb/get-syarat/${jalurId}`)
            .then(res => res.json())
            .then(data => {
                if (!data.length) return syaratContainer.innerHTML = "<p class='text-muted'>Tidak ada syarat untuk jalur ini</p>";
                syaratContainer.innerHTML = data.map(item => `
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="syarat_id[]" value="${item.id}" id="syarat_${item.id}" ${selected.includes(item.id) ? 'checked' : ''}>
                        <label class="form-check-label" for="syarat_${item.id}">${item.syarat}</label>
                    </div>
                `).join('');
            });
    }

    // load awal jika edit
    if (jalurSelect.value) loadSyarat(jalurSelect.value, selectedSyarats);

    // reload saat ganti jalur
    jalurSelect.addEventListener('change', () => loadSyarat(jalurSelect.value, []));
});
</script>
@endsection
