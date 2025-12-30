@extends('layouts.admin')

@section('content')

{{-- ============================================================== --}}
{{-- HEADER: JUDUL & NAVIGASI (SEMUA TOMBOL AKSI DISINI) --}}
{{-- ============================================================== --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Kesiswaan / Data Siswa /</span> Detail Siswa
    </h4>

    <div class="d-flex gap-2 align-items-center flex-wrap">

        @if($siswas->isNotEmpty())

            {{-- 3. DROPDOWN PILIH SISWA --}}
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle btn-sm" type="button" id="siswaDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bx bx-user me-1"></i> Pilih Siswa Lain
                </button>
                <ul class="dropdown-menu dropdown-menu-end p-2 shadow-lg" aria-labelledby="siswaDropdown" style="width: 300px; max-height: 400px; overflow-y: auto;">
                    <li class="mb-2 px-1">
                        <div class="input-group input-group-sm input-group-merge">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" class="form-control" id="searchSiswa" placeholder="Cari nama..." onkeyup="filterSiswa()">
                        </div>
                    </li>
                    <div id="siswaListContainer">
                        @foreach($siswas as $listSiswa)
                            <li>
                                <a class="dropdown-item rounded mb-1 {{ $loop->first ? 'active' : '' }}"
                                   href="javascript:void(0);"
                                   onclick="switchSiswa('{{ $listSiswa->id }}', this)"
                                   data-name="{{ strtolower($listSiswa->nama) }}">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($listSiswa->nama, 0, 1) }}</span>
                                        </div>
                                        <div class="text-truncate small fw-medium">{{ $listSiswa->nama }}</div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </div>
                </ul>
            </div>
        @endif

        {{-- 4. TOMBOL KEMBALI --}}
        <a href="{{ route('admin.alumni.dataAlumni.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </div>
</div>


{{-- LOOPING UTAMA --}}
@if($siswas->isNotEmpty())
    @foreach($siswas as $siswa)

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

    {{-- WRAPPER PER SISWA --}}
    <div class="siswa-view-wrapper fade-in-animation" id="view-{{ $siswa->id }}" style="{{ !$loop->first ? 'display: none;' : '' }}">

        <div class="card shadow-sm overflow-hidden">
            <div class="card-body p-0">
                <div class="row g-0">

                    {{-- SIDEBAR KIRI --}}
                    <div class="col-lg-3 border-end d-flex flex-column align-items-center text-center py-4 px-3 bg-light-gray">
                        {{-- Form Foto --}}
                        <form action="{{ route('admin.alumni.dataAlumni.update', $siswa->id) }}" method="POST" enctype="multipart/form-data" class="w-100">
                            @csrf @method('PUT')
                            <div class="position-relative d-inline-block mb-3">
                                <div class="avatar-wrapper rounded shadow-sm border p-1 bg-white" style="width: 140px; height: 170px;">
                                    @if($siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($siswa->foto))
                                        <img src="{{ asset('storage/' . $siswa->foto) }}" class="d-block w-100 h-100 object-fit-cover rounded">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($siswa->nama) }}&background=random&color=ffffff&size=140" class="d-block w-100 h-100 object-fit-cover rounded">
                                    @endif
                                </div>
                                <label for="upload-foto-{{ $siswa->id }}" class="btn-upload-float shadow-sm" title="Ubah Foto">
                                    <i class="bx bx-camera"></i>
                                    <input type="file" id="upload-foto-{{ $siswa->id }}" name="foto" class="d-none" accept="image/png,image/jpeg,image/jpg" onchange="this.form.submit()">
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

                        {{-- TOMBOL DI SINI SUDAH DIHAPUS (DIPINDAH KE HEADER) --}}
                    </div>

                    {{-- KONTEN KANAN --}}
                    <div class="col-lg-9 p-4 d-flex flex-column bg-white">
                        {{-- Header & Tombol Edit --}}
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <h6 class="mb-0 text-primary fw-bold"><i class="bx bx-file me-2"></i>Informasi Lengkap</h6>
                            <div class="action-button-container">
                                <button type="button" class="btn btn-sm btn-label-primary btn-edit-toggle" id="btn-edit-{{ $siswa->id }}" data-id="{{ $siswa->id }}">
                                    <i class="bx bx-edit-alt me-1"></i> Lengkapi Data
                                </button>
                                <div class="d-none align-items-center gap-2" id="action-buttons-{{ $siswa->id }}">
                                    <button type="button" class="btn btn-sm btn-label-secondary btn-cancel-edit">Batal</button>
                                    <button type="submit" form="form-siswa-{{ $siswa->id }}" class="btn btn-sm btn-primary shadow-sm">
                                        <i class="bx bx-save me-1"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('admin.alumni.dataAlumni.update', $siswa->id) }}" method="POST" id="form-siswa-{{ $siswa->id }}">
                            @csrf @method('PUT')
                            @if(request()->has('ids')) <input type="hidden" name="_ids_multiple" value="{{ request('ids') }}"> @endif

                            <ul class="nav nav-pills nav-fill mb-3 custom-pills" role="tablist">
                                <li class="nav-item"><button type="button" class="nav-link active btn-sm" data-bs-toggle="tab" data-bs-target="#tab-identitas-{{ $siswa->id }}">Identitas</button></li>
                                <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-kesejahteraan-{{ $siswa->id }}">Kesejahteraan</button></li>
                                <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-alamat-{{ $siswa->id }}">Alamat</button></li>
                                <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-transport-{{ $siswa->id }}">Transport</button></li>
                                <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-ortu-{{ $siswa->id }}">Orang Tua</button></li>
                                <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-akademik-{{ $siswa->id }}">Akademik</button></li>
                                <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-riwayat-{{ $siswa->id }}">Riwayat</button></li>
                            </ul>

                            <div class="tab-content p-0 mt-2">
                                {{-- TAB 1: IDENTITAS (LOCKED) --}}
                                <div class="tab-pane fade show active" id="tab-identitas-{{ $siswa->id }}">
                                    <div class="row-clean"><label>Nama Lengkap <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->nama }}</div></div>
                                    <div class="row-clean"><label>NIPD / NISN <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->nipd ?? '-' }} / {{ $siswa->nisn ?? '-' }}</div></div>
                                    <div class="row-clean"><label>NIK <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->nik ?? '-' }}</div></div>
                                    <div class="row-clean"><label>Jenis Kelamin <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'Laki-laki' : 'Perempuan' }}</div></div>
                                    <div class="row-clean"><label>Tempat, Tgl Lahir <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</div></div>
                                    <div class="row-clean"><label>Agama <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->agama_id_str ?? '-' }}</div></div>
                                    <div class="row-clean"><label>Kewarganegaraan <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->kewarganegaraan ?? 'Indonesia' }}</div></div>
                                    <div class="row-clean"><label>Berkebutuhan Khusus <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->kebutuhan_khusus ?? 'Tidak Ada' }}</div></div>
                                    <hr class="my-3 border-dashed">
                                    <div class="row-clean"><label>Email <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->email ?? '-' }}</div></div>
                                    <div class="row-clean"><label>No. HP <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->nomor_telepon_seluler ?? '-' }}</div></div>
                                    <div class="row-clean"><label>No. WA <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="no_wa" class="clean-input editable-field" value="{{ $siswa->no_wa }}" readonly></div></div>
                                </div>

                                {{-- TAB 2: KESEJAHTERAAN --}}
                                <div class="tab-pane fade" id="tab-kesejahteraan-{{ $siswa->id }}">
                                    {{-- <h6 class="small fw-bold text-uppercase text-muted mb-2">Data Periodik (Locked)</h6> --}}
                                    <div class="row-clean"><label>Tinggi Badan <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->tinggi_badan }} cm</div></div>
                                    <div class="row-clean"><label>Berat Badan <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->berat_badan }} kg</div></div>
                                    <hr class="my-3 border-dashed">
                                    {{-- <h6 class="small fw-bold text-uppercase text-primary mb-2">Bantuan (Editable)</h6> --}}
                                    <div class="row-clean"><label>Penerima KIP <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="penerima_kip" class="clean-input editable-field" value="{{ $siswa->penerima_kip }}" readonly placeholder="Ya/Tidak"></div></div>
                                    <div class="row-clean"><label>No. KIP <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="no_kip" class="clean-input editable-field" value="{{ $siswa->no_kip }}" readonly></div></div>
                                    <div class="row-clean"><label>Nama di KIP <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="nama_di_kip" class="clean-input editable-field" value="{{ $siswa->nama_di_kip }}" readonly></div></div>
                                    <div class="row-clean"><label>Layak PIP <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="layak_pip" class="clean-input editable-field" value="{{ $siswa->layak_pip }}" readonly></div></div>
                                    <div class="row-clean"><label>Penerima KPS/PKH <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="penerima_kps" class="clean-input editable-field" value="{{ $siswa->penerima_kps }}" readonly></div></div>
                                    <div class="row-clean"><label>No KPS / KKS <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="no_kps" class="clean-input editable-field" value="{{ $siswa->no_kps }}" readonly placeholder="No KPS"> / <input type="text" name="no_kks" class="clean-input editable-field" value="{{ $siswa->no_kks }}" readonly placeholder="No KKS"></div></div>
                                </div>

                               {{-- 3. ALAMAT (SEKARANG EDITABLE ✏️) --}}
                                <div class="tab-pane fade" id="tab-alamat-{{ $siswa->id }}">
                                    <div class="row-clean"><label>Alamat Jalan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="alamat_jalan" class="clean-input editable-field" value="{{ $siswa->alamat_jalan }}" readonly></div></div>
                                    <div class="row-clean"><label>RT / RW <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="rt" class="clean-input editable-field" value="{{ $siswa->rt }}" readonly style="width: 40px; display:inline-block;"> / <input type="text" name="rw" class="clean-input editable-field" value="{{ $siswa->rw }}" readonly style="width: 40px; display:inline-block;"></div></div>
                                    <div class="row-clean"><label>Dusun <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="dusun" class="clean-input editable-field" value="{{ $siswa->dusun ?? ($siswa->nama_dusun ?? '') }}" readonly></div></div>
                                    <div class="row-clean"><label>Desa/Kelurahan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="desa_kelurahan" class="clean-input editable-field" value="{{ $siswa->desa_kelurahan }}" readonly></div></div>
                                    <div class="row-clean"><label>Kecamatan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="kecamatan" class="clean-input editable-field" value="{{ $siswa->kecamatan }}" readonly></div></div>
                                    <div class="row-clean"><label>Kabupaten/Kota <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="kabupaten_kota" class="clean-input editable-field" value="{{ $siswa->kabupaten_kota }}" readonly></div></div>
                                    <div class="row-clean"><label>Provinsi <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="provinsi" class="clean-input editable-field" value="{{ $siswa->provinsi }}" readonly></div></div>
                                    <div class="row-clean"><label>Kode Pos <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="kode_pos" class="clean-input editable-field" value="{{ $siswa->kode_pos }}" readonly></div></div>
                                </div>

                                {{-- TAB 4: TRANSPORT (EDITABLE) --}}
                                <div class="tab-pane fade" id="tab-transport-{{ $siswa->id }}">
                                    {{-- <h6 class="small fw-bold text-uppercase text-primary mb-2">Transportasi (Editable)</h6> --}}
                                    <div class="row-clean"><label>Jenis Tinggal <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="jenis_tinggal_id_str" class="clean-input editable-field" value="{{ $siswa->jenis_tinggal_id_str }}" readonly></div></div>
                                    <div class="row-clean"><label>Transportasi <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="alat_transportasi_id_str" class="clean-input editable-field" value="{{ $siswa->alat_transportasi_id_str }}" readonly></div></div>
                                    <div class="row-clean"><label>Jarak ke Sekolah (km) <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="jarak_rumah_ke_sekolah_km" class="clean-input editable-field" value="{{ $siswa->jarak_rumah_ke_sekolah_km }}" readonly></div></div>
                                    <div class="row-clean"><label>Waktu Tempuh (menit) <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="waktu_tempuh_menit" class="clean-input editable-field" value="{{ $siswa->waktu_tempuh_menit }}" readonly></div></div>
                                    <div class="row-clean"><label>Jml Saudara <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="number" name="jumlah_saudara_kandung" class="clean-input editable-field" value="{{ $siswa->jumlah_saudara_kandung }}" readonly></div></div>
                                </div>

                                {{-- TAB 5: ORANG TUA --}}
                                <div class="tab-pane fade" id="tab-ortu-{{ $siswa->id }}">
                                    <h6 class="small fw-bold text-uppercase text-dark mb-2">Ayah</h6>
                                    <div class="row-clean"><label>Nama Ayah <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->nama_ayah ?? '-' }}</div></div>
                                    <div class="row-clean"><label>Pekerjaan <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->pekerjaan_ayah_id_str ?? '-' }}</div></div>
                                    <div class="row-clean"><label>Tahun Lahir <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="tahun_lahir_ayah" class="clean-input editable-field" value="{{ $siswa->tahun_lahir_ayah }}" readonly></div></div>
                                    <div class="row-clean"><label>Pendidikan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="pendidikan_ayah_id_str" class="clean-input editable-field" value="{{ $siswa->pendidikan_ayah_id_str }}" readonly></div></div>
                                    <div class="row-clean"><label>Penghasilan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="penghasilan_ayah_id_str" class="clean-input editable-field" value="{{ $siswa->penghasilan_ayah_id_str }}" readonly></div></div>

                                    <hr class="my-2 border-dashed">
                                    <h6 class="small fw-bold text-uppercase text-dark mb-2">Ibu</h6>
                                    <div class="row-clean"><label>Nama Ibu <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->nama_ibu ?? '-' }}</div></div>
                                    <div class="row-clean"><label>Pekerjaan <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->pekerjaan_ibu_id_str ?? '-' }}</div></div>
                                    <div class="row-clean"><label>Tahun Lahir <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="tahun_lahir_ibu" class="clean-input editable-field" value="{{ $siswa->tahun_lahir_ibu }}" readonly></div></div>
                                    <div class="row-clean"><label>Pendidikan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="pendidikan_ibu_id_str" class="clean-input editable-field" value="{{ $siswa->pendidikan_ibu_id_str }}" readonly></div></div>
                                    <div class="row-clean"><label>Penghasilan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="penghasilan_ibu_id_str" class="clean-input editable-field" value="{{ $siswa->penghasilan_ibu_id_str }}" readonly></div></div>

                                    <hr class="my-2 border-dashed">
                                    {{-- <h6 class="small fw-bold text-uppercase text-dark mb-2">Wali (Editable)</h6> --}}
                                    <div class="row-clean"><label>Nama Wali <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->nama_wali ?? '-' }}</div></div>
                                    <div class="row-clean"><label>Pekerjaan <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->pekerjaan_wali_id_str ?? '-' }}</div></div>
                                    <div class="row-clean"><label>Tahun Lahir <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="tahun_lahir_wali" class="clean-input editable-field" value="{{ $siswa->tahun_lahir_wali }}" readonly></div></div>
                                    <div class="row-clean"><label>Pendidikan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="pendidikan_wali_id_str" class="clean-input editable-field" value="{{ $siswa->pendidikan_wali_id_str }}" readonly></div></div>
                                    <div class="row-clean"><label>Penghasilan <i class="bx bx-pencil ms-1 text-primary small"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="penghasilan_wali_id_str" class="clean-input editable-field" value="{{ $siswa->penghasilan_wali_id_str }}" readonly></div></div>
                                </div>

                                {{-- TAB 6: AKADEMIK --}}
                                <div class="tab-pane fade" id="tab-akademik-{{ $siswa->id }}">
                                    <div class="row-clean"><label>Kelas <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik fw-bold text-primary">{{ $namaKelas }}</div></div>
                                    <div class="row-clean"><label>Tingkat <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->tingkat_pendidikan_id ?? '-' }}</div></div>
                                    <div class="row-clean"><label>Tanggal Masuk <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->tanggal_masuk_sekolah ? \Carbon\Carbon::parse($siswa->tanggal_masuk_sekolah)->translatedFormat('d F Y') : '-' }}</div></div>
                                    <div class="row-clean"><label>Anak ke- <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->anak_keberapa ?? '-' }}</div></div>
                                    <div class="row-clean"><label>Status <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->status ?? 'Aktif' }}</div></div>
                                    <div class="row-clean"><label>Sekolah Asal <i class="bx bx-lock-alt ms-1 text-muted small"></i></label><div class="sep">:</div><div class="inp locked-dapodik">{{ $siswa->sekolah_asal ?? '-' }}</div></div>
                                </div>

                                {{-- TAB 7: RIWAYAT (EDITABLE) --}}
                                <div class="tab-pane fade" id="tab-riwayat-{{ $siswa->id }}">
                                    {{-- <h6 class="small fw-bold text-uppercase text-primary mb-2">Dokumen (Editable)</h6> --}}
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
    </div>
    @endforeach

@else
    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5">
            <div class="mb-3"><i class="bx bx-user-x bx-lg text-muted"></i></div>
            <h5 class="fw-bold">Tidak ada data siswa dipilih</h5>
            <a href="{{ route('admin.kesiswaan.siswa.index') }}" class="btn btn-primary px-4">Kembali ke Daftar</a>
        </div>
    </div>
@endif

{{-- CSS KHUSUS --}}
<style>
    /* Styling Input */
    .clean-input.locked-dapodik { cursor: not-allowed; color: #697a8d; }
    .clean-input.locked-dapodik.editing { border-bottom: none !important; background-color: transparent !important; }

    /* Row Styles */
    .row-clean { display: flex; align-items: flex-start; padding: 4px 0; border-bottom: 1px solid transparent; line-height: 1.5; }
    .row-clean label { width: 35%; font-weight: 600; color: #566a7f; margin: 0; font-size: 0.9375rem; }
    .row-clean .sep { width: 3%; text-align: center; font-weight: 600; color: #566a7f; }
    .row-clean .inp { width: 62%; }

    /* Base Input */
    .clean-input { width: 100%; background: transparent !important; border: none !important; padding: 0; margin: 0; outline: none !important; box-shadow: none !important; font-size: 0.9375rem; font-family: inherit; color: #697a8d; pointer-events: none; }

    /* Editing Mode */
    .clean-input.editing { pointer-events: auto; color: #333; cursor: text; border: none !important; }
    .clean-input.editing:focus { border: none !important; outline: none !important; }
    .clean-input::placeholder { color: #b4bdc6; font-style: italic; opacity: 0; }
    .clean-input.editing::placeholder { opacity: 1; }

    /* Tabs & Utils */
    .custom-pills .nav-link { border-radius: 50rem; padding: 0.4rem 1rem; color: #697a8d; font-weight: 500; font-size: 0.85rem; transition: all 0.2s; border: 1px solid transparent; margin-right: 4px; margin-bottom: 4px; }
    .custom-pills .nav-link:hover { background-color: rgba(67, 89, 113, 0.05); color: #696cff; }
    .custom-pills .nav-link.active { background-color: #696cff; color: #fff; box-shadow: 0 2px 6px rgba(105, 108, 255, 0.4); }
    .object-fit-cover { object-fit: cover; }
    .btn-upload-float { position: absolute; bottom: -10px; right: -10px; background: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #696cff; cursor: pointer; box-shadow: 0 0.25rem 0.5rem rgba(161, 172, 184, 0.45); transition: 0.2s; }
    .btn-upload-float:hover { transform: scale(1.1); }
    .bg-light-gray { background-color: #f5f5f9; }
    .fade-in-animation { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>

@endsection

@push('scripts')
<script>
    // FUNGSI SWITCH SISWA (Dropdown)
    window.switchSiswa = function(id, element) {
        document.querySelectorAll('.siswa-view-wrapper').forEach(el => el.style.display = 'none');
        const target = document.getElementById('view-' + id);
        target.style.display = 'block';
        target.classList.remove('fade-in-animation');
        void target.offsetWidth;
        target.classList.add('fade-in-animation');

        document.querySelectorAll('.dropdown-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        const btnPdf = document.getElementById('header-btn-pdf');
        const btnKartu = document.getElementById('header-btn-kartu');

        if(btnPdf) btnPdf.href = `/admin/kesiswaan/siswa/${id}/cetak-pdf`;
        if(btnKartu) btnKartu.href = `/admin/kesiswaan/siswa/${id}/cetak-kartu`;
    };

    // FUNGSI FILTER SISWA (Search Dropdown)
    window.filterSiswa = function() {
        let input = document.getElementById("searchSiswa");
        let filter = input.value.toLowerCase();
        let items = document.querySelectorAll("#siswaListContainer li");
        items.forEach(function(item) {
            let a = item.getElementsByTagName("a")[0];
            let txtValue = a.getAttribute('data-name');
            item.style.display = txtValue.indexOf(filter) > -1 ? "" : "none";
        });
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Handle Tombol Batal
        document.querySelectorAll('.btn-cancel-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                location.reload();
            });
        });

        // Handle Tombol Edit (Hanya field yang BUKAN locked-dapodik)
        document.querySelectorAll('.btn-edit-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const form = document.getElementById('form-siswa-' + id);
                const actionBtns = document.getElementById('action-buttons-' + id);
                const editBtn = document.getElementById('btn-edit-' + id);

                editBtn.classList.add('d-none');
                actionBtns.classList.remove('d-none');
                actionBtns.classList.add('d-flex');

                // LOGIKA: Aktifkan input yang boleh diedit
                form.querySelectorAll('.clean-input').forEach(input => {
                    if (!input.classList.contains('locked-dapodik')) {
                        input.removeAttribute('readonly');
                        input.removeAttribute('disabled');
                        input.classList.add('editing');

                        // Placeholder trick
                        if(input.value.trim() === '-') {
                            input.value = '';
                            input.setAttribute('placeholder', 'Isi data...');
                        }
                    }
                });
            });
        });
    });
</script>
@endpush
