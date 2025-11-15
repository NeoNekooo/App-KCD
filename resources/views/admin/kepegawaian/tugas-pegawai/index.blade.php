@extends('layouts.admin') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kepegawaian /</span> Tugas Pegawai
    </h4>

    {{-- 1. BAGIAN TUGAS POKOK --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Tugas Pokok</h5>
            <small class="text-muted">{{ $tahunAktifTampil }} / Semester {{ $semesterAktifTampil }}</small>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pegawai</th>
                            <th>Tugas Pokok</th>
                            <th>Jumlah Jam</th>
                            <th>Keterangan (TMT & No. SK)</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($tugasPokok as $tugas)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $tugas->gtk->nama ?? 'N/A' }}</strong>
                                </td>
                                <td>{{ $tugas->tugas_pokok }}</td>
                                <td>{{ $tugas->jumlah_jam }}</td>
                                <td>
                                    TMT: {{ $tugas->tmt ? \Carbon\Carbon::parse($tugas->tmt)->format('d-m-Y') : '-' }}
                                    <br>
                                    No. SK: {{ $tugas->nomor_sk ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    Belum ada data Tugas Pokok untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    {{-- 2. BAGIAN TUGAS TAMBAHAN --}}
    
    <h5 class="fw-bold py-3 mb-2">
        Tugas Tambahan
    </h5>

    <div class="row">
        <div class="col-xl-12">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-tabs" role="tablist">
                    
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                            data-bs-target="#wali-kelas" aria-controls="wali-kelas" aria-selected="true">
                            <i class="tf-icons bx bxs-contact me-1"></i> Wali Kelas
                            <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1">
                                {{ $waliKelas->count() }}
                            </span>
                        </button>
                    </li>
                    
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#jabatan-struktural" aria-controls="jabatan-struktural" aria-selected="false">
                            <i class="tf-icons bx bxs-briefcase me-1"></i> Jabatan Struktural
                            <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-success ms-1">
                                {{ $jabatanStruktural->count() }}
                            </span>
                        </button>
                    </li>
                    
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tendik" aria-controls="tendik" aria-selected="false">
                            <i class="tf-icons bx bxs-user-pin me-1"></i> Tenaga Kependidikan
                            <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-info ms-1">
                                {{ $tendik->count() }}
                            </span>
                        </button>
                    </li>

                    {{-- TOMBOL TAB PEMBINA ESKUL (Datanya belum ada) --}}
                    {{-- 
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#pembina-ekskul" aria-controls="pembina-ekskul" aria-selected="false">
                            <i class="tf-icons bx bx-run me-1"></i> Pembina Ekstrakurikuler
                            <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-warning ms-1">0</span>
                        </button>
                    </li>
                    --}}
                    
                </ul>
                <div class="tab-content">
                    
                    {{-- TAB WALI KELAS --}}
                    <div class="tab-pane fade show active" id="wali-kelas" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Rombel</th>
                                        <th>Tingkat</th>
                                        <th>Nama Wali Kelas</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($waliKelas as $rombel)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $rombel->nama ?? $rombel->nama_rombel }}</strong></td>
                                            <td>{{ $rombel->tingkat_pendidikan_id_str ?? 'N/A' }}</td>
                                            <td>
                                                {{ $rombel->waliKelas->nama ?? 'BELUM DIATUR' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Belum ada data Wali Kelas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB JABATAN STRUKTURAL --}}
                    <div class="tab-pane fade" id="jabatan-struktural" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pegawai</th>
                                        <th>Jabatan / Tugas</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($jabatanStruktural as $tugas)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $tugas->nama }}</strong></td>
                                            <td>{{ $tugas->jabatan_ptk_id_str }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Belum ada data Jabatan Struktural.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB BARU UNTUK TENDIK --}}
                    <div class="tab-pane fade" id="tendik" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pegawai</th>
                                        <th>Jabatan / Tugas</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($tendik as $tugas)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $tugas->nama }}</strong></td>
                                            <td>{{ $tugas->jabatan_ptk_id_str }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Belum ada data Tenaga Kependidikan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB PEMBINA ESKUL (Datanya belum ada) --}}
                    {{-- 
                    <div class="tab-pane fade" id="pembina-ekskul" role="tabpanel">
                        <p class="text-center">Belum ada data Pembina Ekstrakurikuler.</p>
                    </div>
                    --}}

                </div>
            </div>
        </div>
    </div>
@endsection