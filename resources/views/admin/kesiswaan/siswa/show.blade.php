@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kesiswaan / <a href="{{ route('admin.kesiswaan.siswa.index') }}">Data Siswa</a> /</span> Profil Siswa
    </h4>

    {{-- HEADER GRADIENT --}}
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                <div class="h-px-150 bg-primary position-relative" style="background: linear-gradient(45deg, #20c997, #2563eb);">
                    <div style="position: absolute; bottom: -20px; left: 0; width: 100%; height: 60px; background: #f5f5f9; clip-path: polygon(0 50%, 100% 0, 100% 100%, 0% 100%);"></div>
                </div>
                
                <div class="card-body position-relative pt-0">
                    <div class="row">
                        <div class="col-sm-auto text-center text-sm-start mt-n5">
                            <div class="d-flex justify-content-center justify-content-sm-start">
                                <div class="bg-white p-1 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 140px; height: 140px;">
                                    @php
                                        $cleanPath = str_replace('public/', '', $siswa->foto ?? '');
                                        $fileExists = !empty($cleanPath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath);
                                    @endphp

                                    @if($fileExists)
                                        <img src="{{ asset('storage/' . $cleanPath) }}" alt="Avatar" class="rounded-circle p-1" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <span class="avatar-initial rounded-circle bg-label-info fs-1 fw-bold text-uppercase w-100 h-100 d-flex align-items-center justify-content-center">
                                            {{ substr($siswa->nama, 0, 2) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col flex-grow-1 mt-3 mt-sm-4 text-center text-sm-start">
                            <h3 class="fw-bold mb-1 text-primary">{{ $siswa->nama }}</h3>
                            <p class="mb-2 text-muted fw-medium">
                                Siswa Kelas <span class="fw-bold text-dark">{{ $siswa->rombel->nama ?? 'Belum Ada Kelas' }}</span>
                            </p>
                            
                            <div class="d-flex flex-wrap justify-content-center justify-content-sm-start gap-2">
                                <span class="badge bg-label-primary">
                                    <i class='bx bx-id-card me-1'></i> NISN: {{ $siswa->nisn ?? '-' }}
                                </span>
                                <span class="badge bg-label-{{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'info' : 'danger' }}">
                                    <i class='bx bx-user me-1'></i> {{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                                <span class="badge bg-label-success">
                                    <i class='bx bx-check-circle me-1'></i> {{ $siswa->status ?? 'Aktif' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-12 col-md-auto mt-4 mt-sm-0 text-center text-sm-end d-flex align-items-end flex-column justify-content-center">
                            <a href="{{ route('admin.kesiswaan.siswa.index') }}" class="btn btn-outline-secondary mb-2">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="row">
        {{-- INFO KIRI --}}
        <div class="col-xl-4 col-lg-5 col-md-5 mb-4">
            <div class="card h-100">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0"><i class='bx bx-school me-2 text-primary'></i>Akademik & Kontak</h5>
                </div>
                <div class="card-body py-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 pb-3 pt-0">
                            <span class="fw-semibold d-block mb-1">Sekolah Induk:</span>
                            @if($siswa->pengguna && $siswa->pengguna->sekolah)
                                <span class="badge bg-label-dark text-wrap lh-sm">{{ $siswa->pengguna->sekolah->nama }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </li>
                        <li class="list-group-item px-0 py-3">
                            <span class="fw-semibold d-block mb-1">NIPD / NIK:</span>
                            <span class="text-muted">{{ $siswa->nipd ?? '-' }} / {{ $siswa->nik ?? '-' }}</span>
                        </li>
                        <li class="list-group-item px-0 py-3">
                            <span class="fw-semibold d-block mb-1">Email / HP Siswa:</span>
                            <span class="text-muted">{{ $siswa->email ?? '-' }} / {{ $siswa->nomor_telepon_seluler ?? '-' }}</span>
                        </li>
                    </ul>
                    
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold mb-3">Alamat Domisili</h6>
                        <p class="mb-1 text-muted"><i class='bx bx-map me-2'></i>{{ $siswa->alamat_jalan ?? '-' }}</p>
                        <p class="mb-1 ms-4 text-muted small">RT {{ $siswa->rt ?? '-' }} / RW {{ $siswa->rw ?? '-' }}</p>
                        <p class="mb-1 ms-4 text-muted small">Ds. {{ $siswa->desa_kelurahan ?? '-' }}, Kec. {{ $siswa->kecamatan ?? '-' }}</p>
                        <p class="ms-4 fw-semibold text-dark">{{ $siswa->kabupaten_kota ?? '' }} - {{ $siswa->kode_pos ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- INFO KANAN (TABS) --}}
        <div class="col-xl-8 col-lg-7 col-md-7 mb-4">
            <div class="nav-align-top mb-4 h-100">
                <ul class="nav nav-tabs nav-fill" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pribadi">
                            <i class="bx bx-user me-1"></i> Data Pribadi
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-akademik">
                            <i class="bx bx-book-open me-1"></i> Akademik
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-wali">
                            <i class="bx bx-group me-1"></i> Orang Tua
                        </button>
                    </li>
                </ul>
                <div class="tab-content h-100">
                    
                    {{-- TAB 1: DATA PRIBADI --}}
                    <div class="tab-pane fade show active" id="navs-pribadi" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <td width="35%" class="fw-semibold text-muted">Nama Lengkap</td>
                                        <td>: <span class="fw-bold text-dark">{{ $siswa->nama }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Tempat, Tgl Lahir</td>
                                        <td>: {{ $siswa->tempat_lahir ?? '-' }}, {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Jenis Kelamin</td>
                                        <td>: {{ ($siswa->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Agama</td>
                                        <td>: {{ $siswa->agama_id_str ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Kewarganegaraan</td>
                                        <td>: {{ $siswa->kewarganegaraan ?? 'Indonesia' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Anak Ke</td>
                                        <td>: {{ $siswa->anak_keberapa ?? '-' }} (Jml Saudara: {{ $siswa->jumlah_saudara_kandung ?? '-' }})</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Tinggi / Berat</td>
                                        <td>: {{ $siswa->tinggi_badan ?? '-' }} cm / {{ $siswa->berat_badan ?? '-' }} kg</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: AKADEMIK --}}
                    <div class="tab-pane fade" id="navs-akademik" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <td width="35%" class="fw-semibold text-muted">NISN</td>
                                        <td>: {{ $siswa->nisn ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">NIPD / Nomor Induk</td>
                                        <td>: {{ $siswa->nipd ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Kelas Saat Ini</td>
                                        <td>: <span class="badge bg-label-info">{{ $siswa->rombel->nama ?? '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Tingkat</td>
                                        <td>: {{ $siswa->tingkat_pendidikan_id ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Tanggal Masuk</td>
                                        <td>: {{ $siswa->tanggal_masuk_sekolah ? \Carbon\Carbon::parse($siswa->tanggal_masuk_sekolah)->translatedFormat('d F Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Asal Sekolah</td>
                                        <td>: {{ $siswa->sekolah_asal ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">No. Ijazah (SMP/MTs)</td>
                                        <td>: {{ $siswa->no_seri_ijazah ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 3: ORANG TUA / WALI --}}
                    <div class="tab-pane fade" id="navs-wali" role="tabpanel">
                        <h6 class="fw-bold mb-2">Data Ayah</h6>
                        <div class="table-responsive text-nowrap mb-3">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr><td width="35%" class="text-muted">Nama Ayah</td><td>: {{ $siswa->nama_ayah ?? '-' }}</td></tr>
                                    <tr><td class="text-muted">Pekerjaan</td><td>: {{ $siswa->pekerjaan_ayah_id_str ?? '-' }}</td></tr>
                                    <tr><td class="text-muted">Penghasilan</td><td>: {{ $siswa->penghasilan_ayah_id_str ?? '-' }}</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <h6 class="fw-bold mb-2 border-top pt-3">Data Ibu</h6>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr><td width="35%" class="text-muted">Nama Ibu</td><td>: {{ $siswa->nama_ibu ?? '-' }}</td></tr>
                                    <tr><td class="text-muted">Pekerjaan</td><td>: {{ $siswa->pekerjaan_ibu_id_str ?? '-' }}</td></tr>
                                    <tr><td class="text-muted">Penghasilan</td><td>: {{ $siswa->penghasilan_ibu_id_str ?? '-' }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection