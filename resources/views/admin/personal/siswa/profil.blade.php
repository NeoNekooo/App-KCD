@extends('layouts.admin')

@section('content')

@php
    $namaKelas = $siswa->nama_rombel;
    if (empty($namaKelas)) {
        $namaKelas = $siswa->rombel->nama ?? null;
    }
    if (empty($namaKelas) && $siswa->peserta_didik_id) {
        $rombelJson = \App\Models\Rombel::where('anggota_rombel', 'like', '%'.$siswa->peserta_didik_id.'%')->first();
        if ($rombelJson) $namaKelas = $rombelJson->nama;
    }
    $namaKelas = $namaKelas ?? 'Belum Masuk Kelas';
@endphp
{{-- HEADER --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Kesiswaan / Data Siswa /</span> Detail Siswa
    </h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.kesiswaan.siswa.cetak_pdf', $siswa->id) }}" target="_blank" class="btn btn-danger btn-sm shadow-sm">
            <i class="bx bxs-file-pdf me-1"></i> Biodata PDF
        </a>
        <a href="{{ route('admin.kesiswaan.siswa.cetak_kartu', $siswa->id) }}" target="_blank" class="btn btn-info btn-sm shadow-sm">
            <i class="bx bx-id-card me-1"></i> Kartu Pelajar
        </a>
        <a href="{{ route('admin.kesiswaan.siswa.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="card shadow-sm overflow-hidden fade-in-animation">
    <div class="card-body p-0">
        <div class="row g-0">

            {{-- SIDEBAR KIRI --}}
            <div class="col-lg-3 border-end d-flex flex-column align-items-center text-center py-4 px-3 bg-light-gray">
                {{-- Form Foto (Boleh Edit) --}}
                <form action="{{ route('admin.kesiswaan.siswa.update', $siswa->id) }}" method="POST" enctype="multipart/form-data" class="w-100">
                    @csrf @method('PUT')
                    <div class="position-relative d-inline-block mb-3">
                        <div class="avatar-wrapper rounded shadow-sm border p-1 bg-white" style="width: 140px; height: 170px;">
                            @if($siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($siswa->foto))
                                <img src="{{ asset('storage/' . $siswa->foto) }}" class="d-block w-100 h-100 object-fit-cover rounded">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($siswa->nama) }}&background=random&color=ffffff&size=140" class="d-block w-100 h-100 object-fit-cover rounded">
                            @endif
                        </div>
                        <label for="upload-foto" class="btn-upload-float shadow-sm" title="Ubah Foto">
                            <i class="bx bx-camera"></i>
                            <input type="file" id="upload-foto" name="foto" class="d-none" accept="image/png,image/jpeg,image/jpg" onchange="this.form.submit()">
                        </label>
                    </div>
                </form>

                <h6 class="fw-bold text-dark mb-1">{{ $siswa->nama }}</h6>
                <div class="text-muted small mb-3">
                    {{ $siswa->nisn ? 'NISN: '.$siswa->nisn : ($siswa->nipd ? 'NIPD: '.$siswa->nipd : '-') }}
                </div>

                <div class="d-flex justify-content-center gap-2 mb-4 w-100 px-2">
                    <span class="badge bg-label-primary flex-fill text-truncate">{{ $namaKelas }}</span>
                    <span class="badge bg-label-{{ $siswa->status == 'Aktif' ? 'success' : 'secondary' }} flex-fill">
                        {{ strtoupper($siswa->status ?? 'AKTIF') }}
                    </span>
                </div>
            </div>

            {{-- KONTEN KANAN --}}
            <div class="col-lg-9 p-4 d-flex flex-column bg-white">

                {{-- HEADER KONTEN --}}
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h6 class="mb-0 text-primary fw-bold"><i class="bx bx-file me-2"></i>Data Lengkap Siswa</h6>

                    <div class="action-button-container">
                        <button type="button" class="btn btn-sm btn-label-primary btn-edit-toggle" id="btn-edit-data">
                            <i class="bx bx-edit-alt me-1"></i> Edit data
                        </button>

                        <div class="d-none align-items-center gap-2" id="action-buttons-group">
                            <button type="button" class="btn btn-sm btn-label-secondary" id="btn-batal-edit">Batal</button>
                            <button type="submit" form="form-data-siswa" class="btn btn-sm btn-primary shadow-sm">
                                <i class="bx bx-save me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.kesiswaan.siswa.update', $siswa->id) }}" method="POST" id="form-data-siswa">
                    @csrf @method('PUT')

                    <ul class="nav nav-pills nav-fill mb-3 custom-pills" role="tablist">
                        <li class="nav-item"><button type="button" class="nav-link active btn-sm" data-bs-toggle="tab" data-bs-target="#tab-identitas">Identitas</button></li>
                        <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-kesejahteraan">Kesejahteraan</button></li>
                        <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-alamat">Alamat</button></li>
                        <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-transport">Transport</button></li>
                        <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-ortu">Orang Tua</button></li>
                        <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-akademik">Akademik</button></li>
                        <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-riwayat">Riwayat</button></li>
                    </ul>

                    <div class="tab-content p-0 mt-2">

                        {{-- 1. IDENTITAS (FULL DATA DAPODIK -> LOCKED) --}}
                        <div class="tab-pane fade show active" id="tab-identitas">
                            {{-- Data Inti --}}
                            <div class="row-clean"><label>Nama Lengkap <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->nama }}</div></div>
                            <div class="row-clean"><label>NIPD / NISN <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->nipd ?? '-' }} / {{ $siswa->nisn ?? '-' }}</div></div>
                            <div class="row-clean"><label>NIK <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->nik ?? '-' }}</div></div>
                            <div class="row-clean"><label>Jenis Kelamin <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'Laki-laki' : 'Perempuan' }}</div></div>
                            <div class="row-clean"><label>Tempat, Tgl Lahir <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</div></div>

                            {{-- Data yang ada di SQL tapi sering kosong di Dapodik (Email/HP/Agama) - INI DIKUNCI SESUAI PERMINTAAN "TARIKAN DAPODIK" --}}
                            <div class="row-clean"><label>Agama <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->agama_id_str ?? '-' }}</div></div>
                            <div class="row-clean"><label>Kewarganegaraan <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->kewarganegaraan ?? 'Indonesia' }}</div></div>
                            <div class="row-clean"><label>Berkebutuhan Khusus <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->kebutuhan_khusus ?? 'Tidak Ada' }}</div></div>
                            <div class="row-clean"><label>Email <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->email ?? '-' }}</div></div>
                            <div class="row-clean"><label>No. HP <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->nomor_telepon_seluler ?? '-' }}</div></div>
                            <div class="row-clean"><label>No. WA <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="no_wa" class="clean-input editable-field" value="{{ $siswa->no_wa }}" readonly></div></div>
                        </div>

                        {{-- 2. KESEJAHTERAAN (DATA BARU - EDITABLE) --}}
                        <div class="tab-pane fade" id="tab-kesejahteraan">
                            {{-- Tinggi/Berat ada di SQL Asli -> Locked --}}
                            <h6 class="small fw-bold text-uppercase text-muted mb-2">Data Periodik Dapodik (Locked)</h6>
                            <div class="row-clean"><label>Tinggi Badan <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->tinggi_badan }} cm</div></div>
                            <div class="row-clean"><label>Berat Badan <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->berat_badan }} kg</div></div>

                            <hr class="my-3 border-dashed">

                            {{-- Bantuan -> INI DATA BARU (EDITABLE) --}}
                            <h6 class="small fw-bold text-uppercase text-primary mb-2">Bantuan Pemerintah (Data Baru - Editable)</h6>
                            <div class="row-clean"><label>Penerima KIP <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="penerima_kip" class="clean-input editable-field" value="{{ $siswa->penerima_kip }}" readonly placeholder="Ya/Tidak"></div></div>
                            <div class="row-clean"><label>No. KIP <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="no_kip" class="clean-input editable-field" value="{{ $siswa->no_kip }}" readonly></div></div>
                            <div class="row-clean"><label>Nama di KIP <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="nama_di_kip" class="clean-input editable-field" value="{{ $siswa->nama_di_kip }}" readonly></div></div>
                            <div class="row-clean"><label>Layak PIP <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="layak_pip" class="clean-input editable-field" value="{{ $siswa->layak_pip }}" readonly></div></div>
                            <div class="row-clean"><label>Penerima KPS/PKH <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="penerima_kps" class="clean-input editable-field" value="{{ $siswa->penerima_kps }}" readonly></div></div>
                            <div class="row-clean"><label>No KPS / KKS <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="no_kps" class="clean-input editable-field" value="{{ $siswa->no_kps }}" readonly placeholder="No KPS"> / <input type="text" name="no_kks" class="clean-input editable-field" value="{{ $siswa->no_kks }}" readonly placeholder="No KKS"></div></div>
                        </div>

                       {{-- 3. ALAMAT (SEKARANG EDITABLE - DATA BARU) --}}
                        <div class="tab-pane fade" id="tab-alamat">
                            <h6 class="small fw-bold text-uppercase text-primary mb-2">Domisili Siswa (Editable)</h6>

                            <div class="row-clean">
                                <label>Alamat Jalan <i class="bx bx-pencil ms-1 text-primary small"></i></label>
                                <div class="sep">:</div>
                                <div class="inp"><input type="text" name="alamat_jalan" class="clean-input editable-field" value="{{ $siswa->alamat_jalan }}" readonly></div>
                            </div>

                            <div class="row-clean">
                                <label>RT / RW <i class="bx bx-pencil ms-1 text-primary small"></i></label>
                                <div class="sep">:</div>
                                <div class="inp">
                                    <input type="text" name="rt" class="clean-input editable-field" value="{{ $siswa->rt }}" readonly style="width: 40px; display:inline-block;">
                                    /
                                    <input type="text" name="rw" class="clean-input editable-field" value="{{ $siswa->rw }}" readonly style="width: 40px; display:inline-block;">
                                </div>
                            </div>

                            <div class="row-clean">
                                <label>Dusun <i class="bx bx-pencil ms-1 text-primary small"></i></label>
                                <div class="sep">:</div>
                                <div class="inp"><input type="text" name="dusun" class="clean-input editable-field" value="{{ $siswa->dusun ?? ($siswa->nama_dusun ?? '') }}" readonly></div>
                            </div>

                            <div class="row-clean">
                                <label>Desa/Kelurahan <i class="bx bx-pencil ms-1 text-primary small"></i></label>
                                <div class="sep">:</div>
                                <div class="inp"><input type="text" name="desa_kelurahan" class="clean-input editable-field" value="{{ $siswa->desa_kelurahan }}" readonly></div>
                            </div>

                            <div class="row-clean">
                                <label>Kecamatan <i class="bx bx-pencil ms-1 text-primary small"></i></label>
                                <div class="sep">:</div>
                                <div class="inp"><input type="text" name="kecamatan" class="clean-input editable-field" value="{{ $siswa->kecamatan }}" readonly></div>
                            </div>

                            <div class="row-clean">
                                <label>Kabupaten/Kota <i class="bx bx-pencil ms-1 text-primary small"></i></label>
                                <div class="sep">:</div>
                                <div class="inp"><input type="text" name="kabupaten_kota" class="clean-input editable-field" value="{{ $siswa->kabupaten_kota }}" readonly></div>
                            </div>

                            <div class="row-clean">
                                <label>Provinsi <i class="bx bx-pencil ms-1 text-primary small"></i></label>
                                <div class="sep">:</div>
                                <div class="inp"><input type="text" name="provinsi" class="clean-input editable-field" value="{{ $siswa->provinsi }}" readonly></div>
                            </div>

                            <div class="row-clean">
                                <label>Kode Pos <i class="bx bx-pencil ms-1 text-primary small"></i></label>
                                <div class="sep">:</div>
                                <div class="inp"><input type="text" name="kode_pos" class="clean-input editable-field" value="{{ $siswa->kode_pos }}" readonly></div>
                            </div>

                            <hr class="my-3 border-dashed">

                            <h6 class="small fw-bold text-uppercase text-primary mb-2">Koordinat (Editable)</h6>
                            <div class="row-clean">
                                <label>Lintang / Bujur <i class="bx bx-pencil ms-1 text-primary small"></i></label>
                                <div class="sep">:</div>
                                <div class="inp">
                                    <input type="text" name="lintang" class="clean-input editable-field" value="{{ $siswa->lintang }}" readonly placeholder="Latitude" style="width: 100px; display:inline-block;">
                                    /
                                    <input type="text" name="bujur" class="clean-input editable-field" value="{{ $siswa->bujur }}" readonly placeholder="Longitude" style="width: 100px; display:inline-block;">
                                </div>
                            </div>
                        </div>

                        {{-- 4. TRANSPORTASI (DATA BARU - EDITABLE) --}}
                        <div class="tab-pane fade" id="tab-transport">
                            <h6 class="small fw-bold text-uppercase text-primary mb-2">Detail Transportasi (Editable)</h6>
                            <div class="row-clean"><label>Jenis Tinggal <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="jenis_tinggal_id_str" class="clean-input editable-field" value="{{ $siswa->jenis_tinggal_id_str }}" readonly></div></div>
                            <div class="row-clean"><label>Transportasi <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="alat_transportasi_id_str" class="clean-input editable-field" value="{{ $siswa->alat_transportasi_id_str }}" readonly></div></div>
                            <div class="row-clean"><label>Jarak ke Sekolah (km) <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="jarak_rumah_ke_sekolah_km" class="clean-input editable-field" value="{{ $siswa->jarak_rumah_ke_sekolah_km }}" readonly></div></div>
                            <div class="row-clean"><label>Waktu Tempuh (menit) <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="waktu_tempuh_menit" class="clean-input editable-field" value="{{ $siswa->waktu_tempuh_menit }}" readonly></div></div>
                            {{-- Jml Saudara ada di Excel tapi di SQL Asli tidak ada kolomnya (hanya anak_keberapa), jadi ini Data Baru --}}
                            <div class="row-clean"><label>Jml Saudara Kandung <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="number" name="jumlah_saudara_kandung" class="clean-input editable-field" value="{{ $siswa->jumlah_saudara_kandung }}" readonly></div></div>
                        </div>

                        {{-- 5. ORANG TUA (NAMA & PEKERJAAN = LOCKED | TAHUN/PENDIDIKAN/HASIL = EDITABLE) --}}
                        <div class="tab-pane fade" id="tab-ortu">
                            {{-- AYAH --}}
                            <h6 class="small fw-bold text-uppercase text-dark mb-2 mt-1">Ayah Kandung</h6>
                            <div class="row-clean"><label>Nama Ayah <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->nama_ayah ?? '-' }}</div></div>
                            <div class="row-clean"><label>Pekerjaan <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->pekerjaan_ayah_id_str ?? '-' }}</div></div>
                            {{-- Detail Ayah (Data Baru) --}}
                            <div class="row-clean"><label>Tahun Lahir <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="tahun_lahir_ayah" class="clean-input editable-field" value="{{ $siswa->tahun_lahir_ayah }}" readonly></div></div>
                            <div class="row-clean"><label>Pendidikan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="pendidikan_ayah_id_str" class="clean-input editable-field" value="{{ $siswa->pendidikan_ayah_id_str }}" readonly></div></div>
                            <div class="row-clean"><label>Penghasilan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="penghasilan_ayah_id_str" class="clean-input editable-field" value="{{ $siswa->penghasilan_ayah_id_str }}" readonly></div></div>

                            {{-- IBU --}}
                            <h6 class="small fw-bold text-uppercase text-dark mb-2 mt-4">Ibu Kandung</h6>
                            <div class="row-clean"><label>Nama Ibu <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->nama_ibu ?? '-' }}</div></div>
                            <div class="row-clean"><label>Pekerjaan <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->pekerjaan_ibu_id_str ?? '-' }}</div></div>
                            {{-- Detail Ibu (Data Baru) --}}
                            <div class="row-clean"><label>Tahun Lahir <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="tahun_lahir_ibu" class="clean-input editable-field" value="{{ $siswa->tahun_lahir_ibu }}" readonly></div></div>
                            <div class="row-clean"><label>Pendidikan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="pendidikan_ibu_id_str" class="clean-input editable-field" value="{{ $siswa->pendidikan_ibu_id_str }}" readonly></div></div>
                            <div class="row-clean"><label>Penghasilan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="penghasilan_ibu_id_str" class="clean-input editable-field" value="{{ $siswa->penghasilan_ibu_id_str }}" readonly></div></div>

                            {{-- WALI (Nama & Pekerjaan ada di SQL Asli -> Locked) --}}
                            <h6 class="small fw-bold text-uppercase text-dark mb-2 mt-4">Wali</h6>
                            <div class="row-clean"><label>Nama Wali <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->nama_wali ?? '-' }}</div></div>
                            <div class="row-clean"><label>Pekerjaan <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->pekerjaan_wali_id_str ?? '-' }}</div></div>
                            {{-- Detail Wali (Data Baru) --}}
                            <div class="row-clean"><label>Tahun Lahir <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="tahun_lahir_wali" class="clean-input editable-field" value="{{ $siswa->tahun_lahir_wali }}" readonly></div></div>
                            <div class="row-clean"><label>Pendidikan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="pendidikan_wali_id_str" class="clean-input editable-field" value="{{ $siswa->pendidikan_wali_id_str }}" readonly></div></div>
                            <div class="row-clean"><label>Penghasilan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="penghasilan_wali_id_str" class="clean-input editable-field" value="{{ $siswa->penghasilan_wali_id_str }}" readonly></div></div>
                        </div>

                        {{-- 6. AKADEMIK (LOCKED DAPODIK) --}}
                        <div class="tab-pane fade" id="tab-akademik">
                            <div class="row-clean"><label>Kelas Saat Ini <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik fw-bold text-primary">{{ $namaKelas }}</div></div>
                            <div class="row-clean"><label>Tingkat <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->tingkat_pendidikan_id ?? '-' }}</div></div>
                            <div class="row-clean"><label>Tanggal Masuk <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->tanggal_masuk_sekolah ? \Carbon\Carbon::parse($siswa->tanggal_masuk_sekolah)->translatedFormat('d F Y') : '-' }}</div></div>
                            <div class="row-clean"><label>Anak ke- <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->anak_keberapa ?? '-' }}</div></div>
                            <div class="row-clean"><label>Status <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->status ?? 'Aktif' }}</div></div>
                            {{-- Sekolah Asal ada di SQL Asli -> Locked --}}
                            <div class="row-clean"><label>Sekolah Asal <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->sekolah_asal ?? '-' }}</div></div>
                        </div>

                        {{-- 7. RIWAYAT & DOKUMEN (DATA BARU - EDITABLE) --}}
                        <div class="tab-pane fade" id="tab-riwayat">
                            <h6 class="small fw-bold text-uppercase text-primary mb-2">Dokumen (Data Baru - Editable)</h6>
                            <div class="row-clean"><label>NPSN Sekolah Asal <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="npsn_sekolah_asal" class="clean-input editable-field" value="{{ $siswa->npsn_sekolah_asal }}" readonly></div></div>
                            <div class="row-clean"><label>No. Seri Ijazah <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="no_seri_ijazah" class="clean-input editable-field" value="{{ $siswa->no_seri_ijazah }}" readonly></div></div>
                            <div class="row-clean"><label>No. Seri SKHUN <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="no_seri_skhun" class="clean-input editable-field" value="{{ $siswa->no_seri_skhun }}" readonly></div></div>
                            <div class="row-clean"><label>No. Peserta UN <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="no_ujian_nasional" class="clean-input editable-field" value="{{ $siswa->no_ujian_nasional }}" readonly></div></div>
                            <div class="row-clean"><label>No Reg Akta Lahir <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="no_registrasi_akta_lahir" class="clean-input editable-field" value="{{ $siswa->no_registrasi_akta_lahir }}" readonly></div></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-gray { background-color: #f5f5f9; }
    .object-fit-cover { object-fit: cover; }

    /* Layout Baris Bersih */
    .row-clean { display: flex; align-items: flex-start; padding: 5px 0; border-bottom: none; line-height: 1.6; }
    .row-clean label { width: 35%; font-weight: 600; color: #566a7f; margin: 0; font-size: 0.9375rem; }
    .row-clean .sep { width: 3%; text-align: center; font-weight: 600; color: #566a7f; }
    .row-clean .inp { width: 62%; font-size: 0.9375rem; color: #566a7f; font-weight: 400; }

    /* LOCKED STATE (DAPODIK) */
    .inp.locked-dapodik { cursor: default; color: #697a8d; }

    /* EDITABLE STATE (DATA BARU) */
    .clean-input { width: 100%; background: transparent; border: none; padding: 0; outline: none; color: inherit; font-family: inherit; font-size: inherit; }
    .clean-input.editable-field { pointer-events: none; } /* Default state */
    .clean-input.editing { pointer-events: auto; border-bottom: 1px dashed #696cff; color: #333; } /* Editing state */
    .clean-input.editing:focus { border-bottom: 1px solid #696cff; }

    /* Tab Styling */
    .custom-pills .nav-link { border-radius: 50rem; padding: 0.3rem 0.8rem; color: #697a8d; font-weight: 500; font-size: 0.8rem; margin-right: 2px; margin-bottom: 4px; border: 1px solid transparent; }
    .custom-pills .nav-link.active { background-color: #696cff; color: #fff; box-shadow: 0 2px 6px rgba(105, 108, 255, 0.4); }

    .btn-upload-float { position: absolute; bottom: -10px; right: -10px; background: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #696cff; cursor: pointer; box-shadow: 0 0.25rem 0.5rem rgba(161, 172, 184, 0.45); transition: 0.2s; }
    .fade-in-animation { animation: fadeIn 0.5s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnEdit = document.getElementById('btn-edit-data');
        const btnBatal = document.getElementById('btn-batal-edit');
        const actionGroup = document.getElementById('action-buttons-group');
        const editableFields = document.querySelectorAll('.editable-field');

        // Logic Tombol Edit (HANYA MEMBUKA KOLOM BARU)
        btnEdit.addEventListener('click', function() {
            btnEdit.classList.add('d-none');
            actionGroup.classList.remove('d-none');
            actionGroup.classList.add('d-flex');

            editableFields.forEach(field => {
                field.removeAttribute('readonly');
                field.classList.add('editing');
            });
        });

        // Logic Tombol Batal
        btnBatal.addEventListener('click', function() {
            location.reload();
        });
    });
</script>
@endpush
