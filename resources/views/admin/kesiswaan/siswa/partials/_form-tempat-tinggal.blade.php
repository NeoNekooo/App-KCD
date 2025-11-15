<div class="row">
    <!-- Kolom Kiri: Alamat Lengkap -->
    <div class="col-md-6">
        <h6 class="fw-semibold mb-3">ALAMAT LENGKAP</h6>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat Jalan/Kp/Gg</label>
            <textarea class="form-control form-control-sm" id="alamat" name="alamat" rows="2">{{ old('alamat', $siswa->alamat ?? '') }}</textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="rt" class="form-label">RT</label>
                <input type="text" class="form-control form-control-sm" id="rt" name="rt" value="{{ old('rt', $siswa->rt ?? '') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label for="rw" class="form-label">RW</label>
                <input type="text" class="form-control form-control-sm" id="rw" name="rw" value="{{ old('rw', $siswa->rw ?? '') }}">
            </div>
        </div>
        <div class="mb-3">
            <label for="desa" class="form-label">Desa/Kelurahan</label>
            <input type="text" class="form-control form-control-sm" id="desa" name="desa" value="{{ old('desa', $siswa->desa ?? '') }}">
        </div>
        <div class="mb-3">
            <label for="kecamatan" class="form-label">Kecamatan</label>
            <input type="text" class="form-control form-control-sm" id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $siswa->kecamatan ?? '') }}">
        </div>
        <div class="row">
            <div class="col-md-8 mb-3">
                <label for="kabupaten" class="form-label">Kabupaten</label>
                <input type="text" class="form-control form-control-sm" id="kabupaten" name="kabupaten" value="{{ old('kabupaten', $siswa->kabupaten ?? '') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="kode_pos" class="form-label">Kode Pos</label>
                <input type="text" class="form-control form-control-sm" id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $siswa->kode_pos ?? '') }}">
            </div>
        </div>
        <div class="mb-3">
            <label for="provinsi" class="form-label">Provinsi</label>
            <input type="text" class="form-control form-control-sm" id="provinsi" name="provinsi" value="{{ old('provinsi', $siswa->provinsi ?? '') }}">
        </div>
    </div>

    <!-- Kolom Kanan: Keterangan Tambahan -->
    <div class="col-md-6">
        <h6 class="fw-semibold mb-3">KETERANGAN TAMBAHAN</h6>
        <div class="mb-3">
            <label for="telepon" class="form-label">Telepon</label>
            <input type="text" class="form-control form-control-sm" id="telepon" name="telepon" value="{{ old('telepon', $siswa->telepon ?? '') }}">
        </div>
        <div class="mb-3">
            <label for="tinggal_dengan" class="form-label">Selama bersekolah tinggal dengan</label>
            <input type="text" class="form-control form-control-sm" id="tinggal_dengan" name="tinggal_dengan" value="{{ old('tinggal_dengan', $siswa->tinggal_dengan ?? '') }}">
        </div>
        <div class="mb-3">
            <label for="jarak_ke_sekolah" class="form-label">Jarak dari tempat tinggal ke sekolah (KM)</label>
            <select class="form-select form-select-sm" id="jarak_ke_sekolah" name="jarak_ke_sekolah">
                <option value="" disabled selected>- Pilih -</option>
                <option value="Kurang dari 1 KM" {{ (old('jarak_ke_sekolah', $siswa->jarak_ke_sekolah ?? '') == 'Kurang dari 1 KM') ? 'selected' : '' }}>Kurang dari 1 KM</option>
                <option value="1 - 3 KM" {{ (old('jarak_ke_sekolah', $siswa->jarak_ke_sekolah ?? '') == '1 - 3 KM') ? 'selected' : '' }}>1 - 3 KM</option>
                <option value="3 - 5 KM" {{ (old('jarak_ke_sekolah', $siswa->jarak_ke_sekolah ?? '') == '3 - 5 KM') ? 'selected' : '' }}>3 - 5 KM</option>
                <option value="Lebih dari 5 KM" {{ (old('jarak_ke_sekolah', $siswa->jarak_ke_sekolah ?? '') == 'Lebih dari 5 KM') ? 'selected' : '' }}>Lebih dari 5 KM</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="transportasi_ke_sekolah" class="form-label">Ke sekolah dengan (Mode Transportasi)</label>
            <select class="form-select form-select-sm" id="transportasi_ke_sekolah" name="transportasi_ke_sekolah">
                <option value="" disabled selected>- Pilih -</option>
                <option value="Jalan Kaki" {{ (old('transportasi_ke_sekolah', $siswa->transportasi_ke_sekolah ?? '') == 'Jalan Kaki') ? 'selected' : '' }}>Jalan Kaki</option>
                <option value="Sepeda" {{ (old('transportasi_ke_sekolah', $siswa->transportasi_ke_sekolah ?? '') == 'Sepeda') ? 'selected' : '' }}>Sepeda</option>
                <option value="Sepeda Motor" {{ (old('transportasi_ke_sekolah', $siswa->transportasi_ke_sekolah ?? '') == 'Sepeda Motor') ? 'selected' : '' }}>Sepeda Motor</option>
                <option value="Angkutan Umum" {{ (old('transportasi_ke_sekolah', $siswa->transportasi_ke_sekolah ?? '') == 'Angkutan Umum') ? 'selected' : '' }}>Angkutan Umum</option>
                <option value="Antar Jemput" {{ (old('transportasi_ke_sekolah', $siswa->transportasi_ke_sekolah ?? '') == 'Antar Jemput') ? 'selected' : '' }}>Antar Jemput</option>
            </select>
        </div>
    </div>
</div>
