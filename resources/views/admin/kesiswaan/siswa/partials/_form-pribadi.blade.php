<div class="row">
    <!-- Kolom Kiri: Foto, ID & Bantuan Pemerintah -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <h5 class="card-header">Foto & ID Siswa</h5>
            <div class="card-body">
                <!-- Foto -->
                <div class="d-flex justify-content-center mb-3">
                    <img src="{{ isset($siswa) && $siswa->foto ? asset('storage/' . $siswa->foto) : 'https://placehold.co/300x400?text=Foto+3x4' }}" 
                         alt="Foto Siswa" 
                         class="img-thumbnail" 
                         id="foto-preview"
                         style="width: 150px; height: 200px; object-fit: cover; aspect-ratio: 3/4;">
                </div>
                <div class="text-center mb-4">
                    <input class="d-none" type="file" id="foto" name="foto" onchange="previewImage('foto', 'foto-preview', 'foto-filename');">
                    <label for="foto" class="btn btn-primary btn-sm">
                        <i class="bx bx-upload me-1"></i> Pilih Foto
                    </label>
                    <p class="text-muted mb-0 mt-2" id="foto-filename">No file chosen</p>
                </div>
                
                <!-- ID Siswa -->
                <div class="mb-3">
                    <label for="nis" class="form-label">NIS (Nomor Induk Siswa)</label>
                    <input type="text" class="form-control form-control-sm" id="nis" name="nis" value="{{ old('nis', $siswa->nis ?? '') }}" placeholder="Contoh: 2324001">
                </div>
                <div class="mb-3">
                    <label for="nisn" class="form-label">NISN (Nomor Induk Siswa Nasional)</label>
                    <input type="text" class="form-control form-control-sm" id="nisn" name="nisn" value="{{ old('nisn', $siswa->nisn ?? '') }}" placeholder="Contoh: 0012345678">
                </div>
                <div class="mb-3">
                    <label for="nik" class="form-label">NIK</label>
                    <input type="text" class="form-control form-control-sm" id="nik" name="nik" value="{{ old('nik', $siswa->nik ?? '') }}" placeholder="16 digit NIK KTP/KK">
                </div>
                 <div class="mb-3">
                    <label for="no_akta_lahir" class="form-label">Nomor Akte Kelahiran</label>
                    <input type="text" class="form-control form-control-sm" id="no_akta_lahir" name="no_akta_lahir" value="{{ old('no_akta_lahir', $siswa->no_akta_lahir ?? '') }}">
                </div>
            </div>
        </div>
        
    </div>

    <!-- Kolom Kanan: Detail Informasi Siswa -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <h5 class="card-header">Informasi Detail</h5>
            <div class="card-body">
                
                <h6 class="fw-semibold mb-3">DATA DIRI</h6>
                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control form-control-sm" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $siswa->nama_lengkap ?? '') }}" required>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="nama_panggilan" class="form-label">Panggilan</label>
                        <input type="text" class="form-control form-control-sm" id="nama_panggilan" name="nama_panggilan" value="{{ old('nama_panggilan', $siswa->nama_panggilan ?? '') }}">
                    </div>
                    <div class="col-md-7 mb-3">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" class="form-control form-control-sm" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $siswa->tempat_lahir ?? '') }}">
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control form-control-sm" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_kelamin" id="laki" value="Laki-laki" {{ (old('jenis_kelamin', $siswa->jenis_kelamin ?? 'Laki-laki') == 'Laki-laki') ? 'checked' : '' }}>
                                <label class="form-check-label" for="laki">Laki-laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_kelamin" id="perempuan" value="Perempuan" {{ (old('jenis_kelamin', $siswa->jenis_kelamin ?? '') == 'Perempuan') ? 'checked' : '' }}>
                                <label class="form-check-label" for="perempuan">Perempuan</label>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-6 mb-3">
                        <label class="form-label">Golongan Darah</label>
                        <div>
                           <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="golongan_darah" id="gol_a" value="A" {{ (old('golongan_darah', $siswa->golongan_darah ?? '') == 'A') ? 'checked' : '' }}><label class="form-check-label" for="gol_a">A</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="golongan_darah" id="gol_b" value="B" {{ (old('golongan_darah', $siswa->golongan_darah ?? '') == 'B') ? 'checked' : '' }}><label class="form-check-label" for="gol_b">B</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="golongan_darah" id="gol_ab" value="AB" {{ (old('golongan_darah', $siswa->golongan_darah ?? '') == 'AB') ? 'checked' : '' }}><label class="form-check-label" for="gol_ab">AB</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="golongan_darah" id="gol_o" value="O" {{ (old('golongan_darah', $siswa->golongan_darah ?? '') == 'O') ? 'checked' : '' }}><label class="form-check-label" for="gol_o">O</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="agama" class="form-label">Agama</label>
                        <select class="form-select form-select-sm" id="agama" name="agama">
                            <option value="" disabled selected>- Pilih -</option>
                            <option value="Islam" {{ (old('agama', $siswa->agama ?? '') == 'Islam') ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ (old('agama', $siswa->agama ?? '') == 'Kristen') ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ (old('agama', $siswa->agama ?? '') == 'Katolik') ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ (old('agama', $siswa->agama ?? '') == 'Hindu') ? 'selected' : '' }}>Hindu</option>
                            <option value="Budha" {{ (old('agama', $siswa->agama ?? '') == 'Budha') ? 'selected' : '' }}>Budha</option>
                            <option value="Konghucu" {{ (old('agama', $siswa->agama ?? '') == 'Konghucu') ? 'selected' : '' }}>Konghucu</option>
                        </select>
                    </div>
                     <div class="col-md-6 mb-3">
                        <label class="form-label">Kewarganegaraan</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="kewarganegaraan" id="wni" value="WNI" {{ (old('kewarganegaraan', $siswa->kewarganegaraan ?? 'WNI') == 'WNI') ? 'checked' : '' }}>
                                <label class="form-check-label" for="wni">WNI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="kewarganegaraan" id="wna" value="WNA" {{ (old('kewarganegaraan', $siswa->kewarganegaraan ?? '') == 'WNA') ? 'checked' : '' }}>
                                <label class="form-check-label" for="wna">WNA</label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <div class="row">
                    <div class="col-md-7">
                        <h6 class="fw-semibold mb-3">KETERANGAN KELUARGA</h6>
                         <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bahasa_sehari_hari" class="form-label">Bahasa Sehari-hari</label>
                                <input type="text" class="form-control form-control-sm" id="bahasa_sehari_hari" name="bahasa_sehari_hari" value="{{ old('bahasa_sehari_hari', $siswa->bahasa_sehari_hari ?? '') }}">
                            </div>
                             <div class="col-md-6 mb-3">
                                <label for="anak_ke" class="form-label">Anak Ke</label>
                                <input type="number" class="form-control form-control-sm" id="anak_ke" name="anak_ke" value="{{ old('anak_ke', $siswa->anak_ke ?? '1') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jumlah_saudara_kandung" class="form-label">Sdr. Kandung</label>
                                <input type="number" class="form-control form-control-sm" id="jumlah_saudara_kandung" name="jumlah_saudara_kandung" value="{{ old('jumlah_saudara_kandung', $siswa->jumlah_saudara_kandung ?? '0') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jumlah_saudara_tiri" class="form-label">Sdr. Tiri</label>
                                <input type="number" class="form-control form-control-sm" id="jumlah_saudara_tiri" name="jumlah_saudara_tiri" value="{{ old('jumlah_saudara_tiri', $siswa->jumlah_saudara_tiri ?? '0') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jumlah_saudara_angkat" class="form-label">Sdr. Angkat</label>
                                <input type="number" class="form-control form-control-sm" id="jumlah_saudara_angkat" name="jumlah_saudara_angkat" value="{{ old('jumlah_saudara_angkat', $siswa->jumlah_saudara_angkat ?? '0') }}">
                            </div>
                             <div class="col-md-12 mb-3">
                                <label for="status_dalam_keluarga" class="form-label">Status dalam Keluarga</label>
                                <select class="form-select form-select-sm" id="status_dalam_keluarga" name="status_dalam_keluarga">
                                    <option value="Anak Kandung" {{ (old('status_dalam_keluarga', $siswa->status_dalam_keluarga ?? '') == 'Anak Kandung') ? 'selected' : '' }}>Anak Kandung</option>
                                    <option value="Anak Angkat" {{ (old('status_dalam_keluarga', $siswa->status_dalam_keluarga ?? '') == 'Anak Angkat') ? 'selected' : '' }}>Anak Angkat</option>
                                </select>
                            </div>
                             <div class="col-md-12 mb-3">
                                <label class="form-label">Kategori Anak</label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="kategori_anak" id="lengkap" value="Lengkap" {{ (old('kategori_anak', $siswa->kategori_anak ?? 'Lengkap') == 'Lengkap') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="lengkap">Lengkap</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="kategori_anak" id="yatim" value="Yatim" {{ (old('kategori_anak', $siswa->kategori_anak ?? '') == 'Yatim') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="yatim">Yatim</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="kategori_anak" id="piatu" value="Piatu" {{ (old('kategori_anak', $siswa->kategori_anak ?? '') == 'Piatu') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="piatu">Piatu</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="kategori_anak" id="yatim_piatu" value="Yatim Piatu" {{ (old('kategori_anak', $siswa->kategori_anak ?? '') == 'Yatim Piatu') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="yatim_piatu">Yatim Piatu</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Sub-kolom kanan di dalam kolom kanan -->
                    <div class="col-md-5">
                         <h6 class="fw-semibold mb-3">BANTUAN PEMERINTAH</h6>
                         <div class="mb-3">
                            <label for="no_seri_pip" class="form-label">Nomor Seri PIP</label>
                            <input type="text" class="form-control form-control-sm" id="no_seri_pip" name="no_seri_pip" value="{{ old('no_seri_pip', $siswa->no_seri_pip ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label for="pkh_kks" class="form-label">PKH/KKS</label>
                            <input type="text" class="form-control form-control-sm" id="pkh_kks" name="pkh_kks" value="{{ old('pkh_kks', $siswa->pkh_kks ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label for="kps" class="form-label">KPS</label>
                            <input type="text" class="form-control form-control-sm" id="kps" name="kps" value="{{ old('kps', $siswa->kps ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

