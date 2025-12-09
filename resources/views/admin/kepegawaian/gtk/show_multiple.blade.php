@extends('layouts.admin')

@section('content')

{{-- HEADER --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Kepegawaian /</span> Detail GTK
    </h4>

    <div class="d-flex gap-2 align-items-center">
        @if($gtks->isNotEmpty())
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle btn-sm" type="button" id="gtkDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bx bx-user me-1"></i> Pilih GTK Lain
            </button>
            <ul class="dropdown-menu dropdown-menu-end p-2 shadow-lg" aria-labelledby="gtkDropdown" style="width: 300px; max-height: 400px; overflow-y: auto;">
                <li class="mb-2 px-1">
                    <div class="input-group input-group-sm input-group-merge">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control" id="searchGtk" placeholder="Cari nama..." onkeyup="filterGtk()">
                    </div>
                </li>
                <div id="gtkListContainer">
                    @foreach($gtks as $listGtk)
                        <li>
                            <a class="dropdown-item rounded mb-1 {{ $loop->first ? 'active' : '' }}" 
                               href="javascript:void(0);" 
                               onclick="switchGtk('{{ $listGtk->id }}', this)"
                               data-name="{{ strtolower($listGtk->nama) }}">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($listGtk->nama, 0, 1) }}</span>
                                    </div>
                                    <div class="text-truncate small fw-medium">{{ $listGtk->nama }}</div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </div>
            </ul>
        </div>
        @endif

        <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($gtks->isNotEmpty())
    @foreach ($gtks as $gtk)
    <div class="gtk-view-wrapper fade-in-animation" id="view-{{ $gtk->id }}" style="{{ !$loop->first ? 'display: none;' : '' }}">
        
        <div class="card shadow-sm overflow-hidden">
            <div class="card-body p-0">
                
                {{-- LAYOUT FLEX: SIDEBAR & KONTEN --}}
                <div class="row g-0">
                    
                    {{-- 1. SIDEBAR KIRI --}}
                    <div class="col-lg-3 border-end d-flex flex-column align-items-center text-center py-4 px-3" style="background-color: #f5f5f9;">
                        
                        <form action="{{ route('admin.kepegawaian.gtk.upload_media', $gtk->id) }}" method="POST" enctype="multipart/form-data" class="w-100">
                            @csrf
                            <div class="position-relative d-inline-block mb-3">
                                <div class="avatar-wrapper">
                                    @if($gtk->foto)
                                        <img src="{{ asset('storage/' . $gtk->foto) }}" alt="Foto" class="d-block rounded shadow-sm object-fit-cover" style="width: 140px; height: 170px;">
                                    @else
                                        <div class="rounded bg-white d-flex align-items-center justify-content-center shadow-sm border" style="width: 140px; height: 170px;">
                                            <i class="bx bx-user bx-lg text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <label for="upload-foto-{{ $gtk->id }}" class="btn-upload-float shadow-sm" title="Ubah Foto">
                                    <i class="bx bx-camera"></i>
                                    <input type="file" id="upload-foto-{{ $gtk->id }}" name="foto" class="d-none" onchange="this.form.submit()">
                                </label>
                            </div>

                            <h6 class="fw-bold text-dark mb-2">{{ $gtk->nama }}</h6>
                            
                            {{-- PERBAIKAN BADGE: Menggunakan style inline !important untuk memaksa warna SOFT --}}
                            @if($gtk->jabatan_ptk_id_str)
                                <span class="badge mb-3 d-inline-block shadow-sm" 
                                      style="background-color: #e7e7ff !important; color: #696cff !important; border: 1px solid rgba(105, 108, 255, 0.2);">
                                    {{ $gtk->jabatan_ptk_id_str }}
                                </span>
                            @else
                                <div class="mb-3 text-muted small">-</div>
                            @endif

                            <div class="d-flex justify-content-center gap-2 mb-4">
                                <span class="badge bg-label-{{ $gtk->ptk_induk == 1 ? 'success' : 'warning' }}" style="font-size: 0.7rem;">
                                    {{ $gtk->ptk_induk == 1 ? 'INDUK' : 'NON-INDUK' }}
                                </span>
                                @if($gtk->jenis_ptk_id_str)
                                    <span class="badge bg-label-info" style="font-size: 0.7rem;">{{ $gtk->jenis_ptk_id_str }}</span>
                                @endif
                            </div>

                            <div class="text-start px-2 w-100">
                                <label class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Tanda Tangan</label>
                                <div class="signature-box border rounded bg-white position-relative hover-lift">
                                    @if($gtk->tandatangan)
                                        <img src="{{ asset('storage/' . $gtk->tandatangan) }}" class="signature-img">
                                    @else
                                        <div class="text-muted small fst-italic py-3 text-center" style="font-size: 0.75rem;">Klik untuk upload</div>
                                    @endif
                                    <label for="upload-ttd-{{ $gtk->id }}" class="stretched-link cursor-pointer"></label>
                                    <input type="file" id="upload-ttd-{{ $gtk->id }}" name="tandatangan" class="d-none" accept="image/png" onchange="this.form.submit()">
                                </div>
                            </div>

                            <div class="d-grid mt-4 w-100">
                                <a href="{{ route('admin.kepegawaian.gtk.cetak_pdf', ['id' => $gtk->id]) }}" class="btn btn-primary btn-sm shadow-sm" target="_blank">
                                    <i class="bx bx-printer me-1"></i> Cetak Biodata
                                </a>
                            </div>
                        </form>
                    </div>

                    {{-- 2. KONTEN KANAN --}}
                    <div class="col-lg-9 p-4 d-flex flex-column bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <h6 class="mb-0 text-primary fw-bold"><i class="bx bx-id-card me-2"></i>Informasi Lengkap</h6>
                            
                            {{-- CONTAINER TOMBOL AKSI --}}
                            <div class="action-button-container">
                                <button type="button" class="btn btn-sm btn-label-primary btn-edit-toggle" id="btn-edit-{{ $gtk->id }}" data-id="{{ $gtk->id }}">
                                    <i class="bx bx-edit-alt me-1"></i> Edit Data
                                </button>

                                {{-- PERBAIKAN TOMBOL: Ukuran pas (btn-sm) dan class error diperbaiki --}}
                                <div class="d-none align-items-center gap-2" id="action-buttons-{{ $gtk->id }}">
                                    <button type="button" class="btn btn-sm btn-label-secondary btn-cancel-edit" data-id="{{ $gtk->id }}">Batal</button>
                                    <button type="submit" form="form-data-{{ $gtk->id }}" class="btn btn-sm btn-primary shadow-sm">
                                        <i class="bx bx-save me-1"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </div>

                        <form id="form-data-{{ $gtk->id }}" action="{{ route('admin.kepegawaian.gtk.update_data', $gtk->id) }}" method="POST">
    @csrf
    @method('PUT')

    <ul class="nav nav-pills nav-fill mb-3 custom-pills" role="tablist">
        <li class="nav-item"><button type="button" class="nav-link active btn-sm" data-bs-toggle="tab" data-bs-target="#tab-identitas-{{ $gtk->id }}">Identitas</button></li>
        <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-pribadi-{{ $gtk->id }}">Pribadi</button></li>
        <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-kepegawaian-{{ $gtk->id }}">Kepegawaian</button></li>
        <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-pendidikan-{{ $gtk->id }}">Pendidikan</button></li>
        <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-kompetensi-{{ $gtk->id }}">Kompetensi</button></li>
        <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-kontak-{{ $gtk->id }}">Kontak</button></li>
    </ul>

    <div class="tab-content p-0 mt-2">
        
        {{-- TAB 1: IDENTITAS (DIKUNCI DAPODIK) --}}
        <div class="tab-pane fade show active" id="tab-identitas-{{ $gtk->id }}">
            {{-- Nama --}}
            <div class="row-clean">
                <label>Nama Lengkap <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                <div class="sep">:</div>
                <div class="inp"><input type="text" name="nama" class="clean-input fw-bold text-dark locked-dapodik" value="{{ $gtk->nama ?: '-' }}" readonly></div>
            </div>
            {{-- NIK --}}
            <div class="row-clean">
                <label>NIK <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                <div class="sep">:</div>
                <div class="inp"><input type="text" name="nik" class="clean-input locked-dapodik" value="{{ $gtk->nik ?: '-' }}" readonly></div>
            </div>
            {{-- No KK (Bisa Diedit - Biasanya lokal) --}}
            <div class="row-clean">
                <label>No KK</label>
                <div class="sep">:</div>
                <div class="inp"><input type="text" name="no_kk" class="clean-input" value="{{ $gtk->no_kk ?: '-' }}" readonly></div>
            </div>
            {{-- Jenis Kelamin --}}
            <div class="row-clean">
                <label>Jenis Kelamin <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                <div class="sep">:</div>
                <div class="inp">
                    <select name="jenis_kelamin" class="clean-input locked-dapodik" disabled>
                        <option value="" {{ !$gtk->jenis_kelamin ? 'selected' : '' }}>-</option>
                        <option value="L" {{ $gtk->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $gtk->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>
            {{-- Tempat Lahir --}}
            <div class="row-clean">
                <label>Tempat Lahir <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                <div class="sep">:</div>
                <div class="inp"><input type="text" name="tempat_lahir" class="clean-input locked-dapodik" value="{{ $gtk->tempat_lahir ?: '-' }}" readonly></div>
            </div>
            {{-- Tanggal Lahir --}}
            <div class="row-clean">
                <label>Tanggal Lahir <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                <div class="sep">:</div>
                <div class="inp"><input type="date" name="tanggal_lahir" class="clean-input locked-dapodik" value="{{ $gtk->tanggal_lahir }}" readonly></div>
            </div>
            {{-- Ibu Kandung --}}
            <div class="row-clean">
                <label>Nama Ibu Kandung <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                <div class="sep">:</div>
                <div class="inp"><input type="text" name="nama_ibu_kandung" class="clean-input locked-dapodik" value="{{ $gtk->nama_ibu_kandung ?: '-' }}" readonly></div>
            </div>
            {{-- Agama --}}
            <div class="row-clean">
                <label>Agama <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                <div class="sep">:</div>
                <div class="inp">
                    <select name="agama_id_str" class="clean-input locked-dapodik" disabled>
                        <option value="" {{ !$gtk->agama_id_str ? 'selected' : '' }}>-</option>
                        @foreach(['Islam','Kristen','Katolik','Hindu','Buddha'] as $ag)<option value="{{ $ag }}" {{ $gtk->agama_id_str == $ag ? 'selected' : '' }}>{{ $ag }}</option>@endforeach
                    </select>
                </div>
            </div>
            {{-- Kewarganegaraan (Bisa Edit) --}}
            <div class="row-clean"><label>Kewarganegaraan</label><div class="sep">:</div><div class="inp"><input type="text" name="kewarganegaraan" class="clean-input" value="{{ $gtk->kewarganegaraan ?: '-' }}" readonly></div></div>
        </div>

        {{-- TAB 2: PRIBADI (SEMUA BISA DIEDIT) --}}
        <div class="tab-pane fade" id="tab-pribadi-{{ $gtk->id }}">
            <div class="row-clean"><label>Alamat Jalan</label><div class="sep">:</div><div class="inp"><input type="text" name="alamat_jalan" class="clean-input" value="{{ $gtk->alamat_jalan ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>RT / RW</label><div class="sep">:</div><div class="inp d-flex"><input type="text" name="rt" class="clean-input w-25" value="{{ $gtk->rt ?: '-' }}" readonly> / <input type="text" name="rw" class="clean-input w-25" value="{{ $gtk->rw ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>Dusun</label><div class="sep">:</div><div class="inp"><input type="text" name="dusun" class="clean-input" value="{{ $gtk->dusun ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>Desa/Kelurahan</label><div class="sep">:</div><div class="inp"><input type="text" name="desa_kelurahan" class="clean-input" value="{{ $gtk->desa_kelurahan ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>Kecamatan</label><div class="sep">:</div><div class="inp"><input type="text" name="kecamatan" class="clean-input" value="{{ $gtk->kecamatan ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>Kode Pos</label><div class="sep">:</div><div class="inp"><input type="text" name="kode_pos" class="clean-input" value="{{ $gtk->kode_pos ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>Lintang / Bujur</label><div class="sep">:</div><div class="inp d-flex gap-2"><input type="text" name="lintang" class="clean-input" value="{{ $gtk->lintang ?: '-' }}" readonly> <input type="text" name="bujur" class="clean-input" value="{{ $gtk->bujur ?: '-' }}" readonly></div></div>
            <hr class="my-2 border-dashed">
            <div class="row-clean"><label>Status Kawin</label><div class="sep">:</div><div class="inp">
                <select name="status_perkawinan" class="clean-input" disabled>
                    <option value="">-</option>
                    <option value="Kawin" {{ $gtk->status_perkawinan == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                    <option value="Belum Kawin" {{ $gtk->status_perkawinan == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                    <option value="Janda/Duda" {{ $gtk->status_perkawinan == 'Janda/Duda' ? 'selected' : '' }}>Janda/Duda</option>
                </select>
            </div></div>
            <div class="row-clean"><label>Nama Pasangan</label><div class="sep">:</div><div class="inp"><input type="text" name="nama_suami_istri" class="clean-input" value="{{ $gtk->nama_suami_istri ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>Pekerjaan</label><div class="sep">:</div><div class="inp"><input type="text" name="pekerjaan_suami_istri" class="clean-input" value="{{ $gtk->pekerjaan_suami_istri ?: '-' }}" readonly></div></div>
        </div>

        {{-- TAB 3: KEPEGAWAIAN (SEBAGIAN BESAR DIKUNCI DAPODIK) --}}
        <div class="tab-pane fade" id="tab-kepegawaian-{{ $gtk->id }}">
            <div class="row-clean"><label>Status Pegawai <i class="bx bx-lock-alt text-muted small ms-1"></i></label><div class="sep">:</div><div class="inp">
                <select name="status_kepegawaian_id_str" class="clean-input fw-bold text-primary locked-dapodik" disabled>
                    <option value="">-</option>
                    @foreach(['GTY/PTY', 'PNS', 'PPPK', 'Honor Sekolah'] as $st)<option value="{{ $st }}" {{ $gtk->status_kepegawaian_id_str == $st ? 'selected' : '' }}>{{ $st }}</option>@endforeach
                </select>
            </div></div>
            <div class="row-clean"><label>NIP <i class="bx bx-lock-alt text-muted small ms-1"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="nip" class="clean-input locked-dapodik" value="{{ $gtk->nip ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>NIY / NIGK <i class="bx bx-lock-alt text-muted small ms-1"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="niy_nigk" class="clean-input locked-dapodik" value="{{ $gtk->niy_nigk ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>NUPTK <i class="bx bx-lock-alt text-muted small ms-1"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="nuptk" class="clean-input locked-dapodik" value="{{ $gtk->nuptk ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>NRG <i class="bx bx-lock-alt text-muted small ms-1"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="nrg" class="clean-input locked-dapodik" value="{{ $gtk->nrg ?: '-' }}" readonly></div></div>
            
            <div class="row-clean"><label>SK Pengangkatan <i class="bx bx-lock-alt text-muted small ms-1"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="sk_pengangkatan" class="clean-input locked-dapodik" value="{{ $gtk->sk_pengangkatan ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>TMT Pengangkatan <i class="bx bx-lock-alt text-muted small ms-1"></i></label><div class="sep">:</div><div class="inp"><input type="date" name="tmt_pengangkatan" class="clean-input locked-dapodik" value="{{ $gtk->tmt_pengangkatan }}" readonly></div></div>
            <div class="row-clean"><label>Lembaga <i class="bx bx-lock-alt text-muted small ms-1"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="lembaga_pengangkat" class="clean-input locked-dapodik" value="{{ $gtk->lembaga_pengangkat ?: '-' }}" readonly></div></div>
            
            <div class="row-clean"><label>SK CPNS <i class="bx bx-lock-alt text-muted small ms-1"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="sk_cpns" class="clean-input locked-dapodik" value="{{ $gtk->sk_cpns ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>TMT CPNS <i class="bx bx-lock-alt text-muted small ms-1"></i></label><div class="sep">:</div><div class="inp"><input type="date" name="tmt_cpns" class="clean-input locked-dapodik" value="{{ $gtk->tmt_cpns }}" readonly></div></div>
            <div class="row-clean"><label>TMT PNS <i class="bx bx-lock-alt text-muted small ms-1"></i></label><div class="sep">:</div><div class="inp"><input type="date" name="tmt_pns" class="clean-input locked-dapodik" value="{{ $gtk->tmt_pns }}" readonly></div></div>
            
            <div class="row-clean"><label>Pangkat Akhir <i class="bx bx-lock-alt text-muted small ms-1"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="pangkat_golongan_terakhir" class="clean-input locked-dapodik" value="{{ $gtk->pangkat_golongan_terakhir ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>Sumber Gaji</label><div class="sep">:</div><div class="inp"><input type="text" name="sumber_gaji" class="clean-input" value="{{ $gtk->sumber_gaji ?: '-' }}" readonly></div></div>

            <div class="mt-3">
                <div class="small fw-bold text-muted text-uppercase mb-2">Riwayat Kepangkatan (Dapodik)</div>
                <div class="table-responsive border rounded-3">
                    <table class="table table-sm table-striped mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light"><tr><th>Gol</th><th>Nomor SK</th><th>TMT</th><th>Masa Kerja</th></tr></thead>
                        <tbody>
                            @php $pangkat = json_decode($gtk->rwy_kepangkatan) ?? []; @endphp
                            @forelse($pangkat as $pkt)
                                <tr>
                                    <td class="fw-bold">{{ $pkt->pangkat_golongan_id_str ?? '-' }}</td>
                                    <td>{{ $pkt->nomor_sk ?? '-' }}</td>
                                    <td>{{ $pkt->tmt_pangkat ? \Carbon\Carbon::parse($pkt->tmt_pangkat)->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $pkt->masa_kerja_gol_tahun ?? 0 }} Thn</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted small py-2">Data kosong.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 4: PENDIDIKAN (DATA UTAMA DIKUNCI) --}}
        <div class="tab-pane fade" id="tab-pendidikan-{{ $gtk->id }}">
            <div class="row-clean"><label>Pend. Terakhir <i class="bx bx-lock-alt text-muted small ms-1"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="pendidikan_terakhir" class="clean-input locked-dapodik" value="{{ $gtk->pendidikan_terakhir ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>Bidang Studi <i class="bx bx-lock-alt text-muted small ms-1"></i></label><div class="sep">:</div><div class="inp"><input type="text" name="bidang_studi_terakhir" class="clean-input locked-dapodik" value="{{ $gtk->bidang_studi_terakhir ?: '-' }}" readonly></div></div>
            
            <div class="mt-3">
                <div class="small fw-bold text-muted text-uppercase mb-2">Riwayat Pendidikan Formal (Dapodik)</div>
                <div class="table-responsive border rounded-3">
                    <table class="table table-sm table-striped mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light"><tr><th>Jenjang</th><th>Institusi</th><th>Thn Lulus</th><th>IPK</th></tr></thead>
                        <tbody>
                            @php $pend = json_decode($gtk->rwy_pend_formal) ?? []; @endphp
                            @forelse($pend as $rw)
                                <tr>
                                    <td>{{ $rw->jenjang_pendidikan_id_str ?? '-' }}</td>
                                    <td>{{ $rw->satuan_pendidikan_formal ?? '-' }}</td>
                                    <td>{{ $rw->tahun_lulus ?? '-' }}</td>
                                    <td>{{ $rw->ipk ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted small py-2">Data kosong.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 5: KOMPETENSI (BISA DIEDIT) --}}
        <div class="tab-pane fade" id="tab-kompetensi-{{ $gtk->id }}">
            <div class="row-clean"><label>Lisensi Kepsek</label><div class="sep">:</div><div class="inp"><select name="lisensi_kepsek" class="clean-input" disabled><option value="0" {{ $gtk->lisensi_kepsek == 0 ? 'selected' : '' }}>Tidak</option><option value="1" {{ $gtk->lisensi_kepsek == 1 ? 'selected' : '' }}>Ya</option></select></div></div>
            <div class="row-clean"><label>No Registrasi (NUKS)</label><div class="sep">:</div><div class="inp"><input type="text" name="nuks" class="clean-input" value="{{ $gtk->nuks ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>Keahlian Lab</label><div class="sep">:</div><div class="inp"><input type="text" name="keahlian_laboratorium" class="clean-input" value="{{ $gtk->keahlian_laboratorium ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>Menangani Keb. Khusus</label><div class="sep">:</div><div class="inp"><input type="text" name="mampu_menangani_kebutuhan_khusus" class="clean-input" value="{{ $gtk->mampu_menangani_kebutuhan_khusus ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>Keahlian Braille</label><div class="sep">:</div><div class="inp"><select name="keahlian_braille" class="clean-input" disabled><option value="0" {{ $gtk->keahlian_braille == 0 ? 'selected' : '' }}>Tidak</option><option value="1" {{ $gtk->keahlian_braille == 1 ? 'selected' : '' }}>Ya</option></select></div></div>
            <div class="row-clean"><label>Bahasa Isyarat</label><div class="sep">:</div><div class="inp"><select name="keahlian_bahasa_isyarat" class="clean-input" disabled><option value="0" {{ $gtk->keahlian_bahasa_isyarat == 0 ? 'selected' : '' }}>Tidak</option><option value="1" {{ $gtk->keahlian_bahasa_isyarat == 1 ? 'selected' : '' }}>Ya</option></select></div></div>
        </div>

        {{-- TAB 6: KONTAK (BISA DIEDIT) --}}
        <div class="tab-pane fade" id="tab-kontak-{{ $gtk->id }}">
            <div class="row-clean"><label>No. HP</label><div class="sep">:</div><div class="inp"><input type="text" name="no_hp" class="clean-input" value="{{ $gtk->no_hp ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>Email</label><div class="sep">:</div><div class="inp"><input type="email" name="email" class="clean-input" value="{{ $gtk->email ?: '-' }}" readonly></div></div>
            <div class="row-clean"><label>No. Telp Rumah</label><div class="sep">:</div><div class="inp"><input type="text" name="no_telepon_rumah" class="clean-input" value="{{ $gtk->no_telepon_rumah ?: '-' }}" readonly></div></div>
        </div>

    </div> {{-- End Tab Content --}}
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
            <h5 class="fw-bold">Tidak ada data GTK dipilih</h5>
            <a href="javascript:history.back()" class="btn btn-primary px-4">Kembali</a>
        </div>
    </div>
@endif

<style>
    /* Styling Khusus untuk Input Terkunci */
.clean-input.locked-dapodik {
    cursor: not-allowed;
    color: #697a8d; /* Warna tetap jelas tapi user tau ini mati */
}

/* Pastikan saat mode edit, input terkunci TIDAK berubah */
.clean-input.locked-dapodik.editing {
    border-bottom: none !important;
    background-color: transparent !important;
}
    /* 1. ROW YANG LEBIH RAPAT */
    .row-clean {
        display: flex;
        align-items: flex-start;
        padding: 4px 0;
        border-bottom: 1px solid transparent; 
        line-height: 1.5;
    }
    .row-clean label {
        width: 35%; font-weight: 600; color: #566a7f; margin: 0; font-size: 0.9375rem;
    }
    .row-clean .sep {
        width: 3%; text-align: center; font-weight: 600; color: #566a7f;
    }
    .row-clean .inp {
        width: 62%;
    }

    /* 2. INPUT TEXT BIASA (MODE LIHAT) */
    .clean-input {
        width: 100%;
        background: transparent !important;
        border: none !important;
        padding: 0;
        margin: 0;
        outline: none !important;
        box-shadow: none !important;
        font-size: 0.9375rem;
        font-family: inherit;
        color: #697a8d;
        pointer-events: none;
        -webkit-appearance: none;
        appearance: none;
    }

    /* 3. INPUT MODE EDIT (NO BORDER, ONLY CURSOR CHANGE) */
    .clean-input.editing {
        pointer-events: auto;
        color: #333;
        cursor: text;
        border: none !important; /* Pastikan tidak ada border */
    }
    .clean-input.editing:focus {
         border: none !important; /* Pastikan fokus juga aman */
         outline: none !important;
    }
    
    /* Placeholder Logic */
    .clean-input::placeholder { color: #b4bdc6; font-style: italic; opacity: 0; }
    .clean-input.editing::placeholder { opacity: 1; }

    /* 4. TABS PILLS MODERN */
    .custom-pills .nav-link {
        border-radius: 50rem; padding: 0.4rem 1rem; color: #697a8d; font-weight: 500; font-size: 0.85rem; transition: all 0.2s; border: 1px solid transparent; margin-right: 4px; margin-bottom: 4px;
    }
    .custom-pills .nav-link:hover { background-color: rgba(67, 89, 113, 0.05); color: #696cff; }
    .custom-pills .nav-link.active { background-color: #696cff; color: #fff; box-shadow: 0 2px 6px rgba(105, 108, 255, 0.4); }

    /* 5. UTILS */
    .object-fit-cover { object-fit: cover; }
    .btn-upload-float { position: absolute; bottom: -10px; right: -10px; background: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #696cff; cursor: pointer; box-shadow: 0 0.25rem 0.5rem rgba(161, 172, 184, 0.45); transition: 0.2s; }
    .btn-upload-float:hover { transform: scale(1.1); }
    .signature-box { min-height: 70px; display: flex; align-items: center; justify-content: center; background-image: radial-gradient(#e2e2e2 1px, transparent 1px); background-size: 8px 8px; transition: 0.2s; }
    .signature-box:hover { border-color: #696cff !important; }
    .signature-img { max-height: 60px; max-width: 100%; }
    .fade-in-animation { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>

@endsection

@push('scripts')
<script>
    window.switchGtk = function(id, element) {
        document.querySelectorAll('.gtk-view-wrapper').forEach(el => el.style.display = 'none');
        const target = document.getElementById('view-' + id);
        target.style.display = 'block';
        target.classList.remove('fade-in-animation');
        void target.offsetWidth; 
        target.classList.add('fade-in-animation');

        document.querySelectorAll('.dropdown-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
    };

    window.filterGtk = function() {
        let input = document.getElementById("searchGtk");
        let filter = input.value.toLowerCase();
        let items = document.querySelectorAll("#gtkListContainer li");
        items.forEach(function(item) {
            let a = item.getElementsByTagName("a")[0];
            let txtValue = a.getAttribute('data-name');
            item.style.display = txtValue.indexOf(filter) > -1 ? "" : "none";
        });
    };

    document.addEventListener('DOMContentLoaded', function() {
    // Tombol Edit
    document.querySelectorAll('.btn-edit-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const form = document.getElementById('form-data-' + id);
            const actionBtns = document.getElementById('action-buttons-' + id);
            const editBtn = document.getElementById('btn-edit-' + id);
            
            editBtn.classList.add('d-none'); 
            actionBtns.classList.remove('d-none'); 
            actionBtns.classList.add('d-flex'); 

            // LOGIKA PENTING: Hanya aktifkan yang BUKAN locked-dapodik
            form.querySelectorAll('.clean-input').forEach(input => {
                
                // Cek apakah input ini dikunci (Dapodik)?
                if (!input.classList.contains('locked-dapodik')) {
                    input.removeAttribute('readonly');
                    input.removeAttribute('disabled');
                    input.classList.add('editing');
                    
                    // Munculkan placeholder hanya di kolom yang bisa diedit
                    if(input.value.trim() === '-') {
                        input.value = '';
                        input.setAttribute('placeholder', 'klik untuk isi..');
                    }
                }
            });
        });
    });

    // Tombol Batal
    document.querySelectorAll('.btn-cancel-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            location.reload(); 
        });
    });
});
</script>
@endpush