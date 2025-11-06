@extends('layouts.admin') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kepegawaian /</span> Tugas Pegawai
    </h4>

    <p class="mb-4">
        Halaman ini menampilkan ringkasan tugas-tugas pegawai yang dialokasikan dari modul lain (seperti Rombel dan Ekstrakurikuler).
    </p>

    <div class="row">
        <div class="col-xl-12">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                            data-bs-target="#wali-kelas" aria-controls="wali-kelas" aria-selected="true">
                            <i class="tf-icons bx bxs-contact me-1"></i> Wali Kelas
                            <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1">
                                {{ $rombels->count() }}
                            </span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#pembina-ekskul" aria-controls="pembina-ekskul" aria-selected="false">
                            <i class="tf-icons bx bx-run me-1"></i> Pembina Ekstrakurikuler
                            <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-info ms-1">
                                {{ $ekskuls->count() }}
                            </span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tugas-tambahan" aria-controls="tugas-tambahan" aria-selected="false">
                            <i class="tf-icons bx bxs-briefcase me-1"></i> Tugas Tambahan
                            <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-success ms-1">
                                {{ $tugasTambahan->count() }}
                            </span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    
                    <div class="tab-pane fade show active" id="wali-kelas" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Rombel</th>
                                        <th>Tingkat</th>
                                        <th>Nama Wali Kelas (dari GTK)</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($rombels as $rombel)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $rombel->nama ?? $rombel->nama_rombel }}</strong></td>
                                            <td>{{ $rombel->tingkat ?? 'N/A' }}</td>
                                            <td>{{ $rombel->wali->nama ?? 'BELUM DIATUR' }}</td>
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

                    <div class="tab-pane fade" id="pembina-ekskul" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Ekstrakurikuler</th>
                                        <th>Nama Pembina (dari GTK)</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($ekskuls as $eskul)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $eskul->nama_ekskul }}</strong></td>
                                            <td>{{ $eskul->pembina->nama ?? 'BELUM DIATUR' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Belum ada data Pembina.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tugas-tambahan" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pegawai (dari GTK)</th>
                                        <th>Jabatan / Tugas</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($tugasTambahan as $tugas)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $tugas->nama }}</strong></td>
                                            <td>{{ $tugas->jabatan_ptk_id_str }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Belum ada data Tugas Tambahan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @endsection