@extends('layouts.admin')

@section('content')

<div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    <i class="bx bx-user-circle me-2"></i>
                    Rapor Pelanggaran: <strong>{{ $namaGuru }}</strong>
                </h5>

                <div class="d-flex gap-2">

                    {{-- 🔹 Tombol Cetak Surat Otomatis --}}
                    @if ($sanksiAktif)
                        <a href="{{ route('admin.indisipliner.guru.rekapitulasi.cetak.surat', ['namaGuru' => $namaGuru]) }}"
                            target="_blank" class="btn btn-danger btn-sm">
                            <i class="bx bx-file me-1"></i>
                            Cetak {{ strtoupper($sanksiAktif->nama) }}
                        </a>
                    @endif

                    {{-- 🔹 Tombol Cetak Individu --}}
                    <a href="{{ route('admin.indisipliner.guru.rekapitulasi.cetak.individu', ['namaGuru' => $namaGuru]) }}"
                        target="_blank" class="btn btn-secondary btn-sm">
                        <i class="bx bx-printer me-1"></i> Cetak Laporan Guru Ini
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    {{-- 🧍 Kolom Kiri: Info Guru --}}
                    <div class="col-md-7 border-end">
                        <div class="d-flex align-items-center mb-4">
                            <div class="flex-shrink-0 me-3">
                                <img src="https://placehold.co/100x100/17a2b8/white?text={{ strtoupper(substr($namaGuru, 0, 1)) }}"
                                    alt="Avatar" class="d-block rounded-circle" height="100" width="100">
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $namaGuru }}</h5>
                                <p class="text-muted mb-0">Tenaga Pendidik / Kependidikan</p>
                            </div>
                        </div>
                    </div>

                    {{-- 📊 Kolom Kanan: Poin dan Sanksi --}}
                    <div class="col-md-5 d-flex flex-column justify-content-center align-items-center p-4">
                        <h6 class="text-muted mb-3">Total Akumulasi Poin</h6>
                        <div class="display-3 fw-bold text-danger">{{ $totalPoin }}</div>

                        @if ($sanksiAktif)
                            <div class="text-center mt-3">
                                <p class="mb-1 text-muted">Sanksi Aktif:</p>
                                <span class="badge bg-label-danger fs-6">{{ $sanksiAktif->nama }}</span>
                            </div>
                        @else
                            <div class="text-center mt-3">
                                <span class="badge bg-label-success fs-6">Tidak Ada Sanksi</span>
                            </div>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                {{-- 📚 Tabel Riwayat Pelanggaran --}}
                <h6 class="mb-3"><i class="bx bx-list-ul me-2"></i>Riwayat Pelanggaran</h6>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jenis Pelanggaran</th>
                                <th>Poin</th>
                                <th>Tahun Pelajaran</th>
                                <th>Semester</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pelanggaranGuru as $key => $pelanggaran)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d M Y') }}</td>
                                    <td style="white-space: normal;">{{ $pelanggaran->detailPoinGtk->nama ?? '-' }}</td>
                                    <td><span class="badge bg-danger rounded-pill">{{ $pelanggaran->poin }}</span></td>
                                    <td>{{ $pelanggaran->tapel ?? '-' }}</td>
                                    <td>{{ ucfirst($pelanggaran->semester) ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bx bx-check-circle bx-lg text-success"></i>
                                        <p class="text-muted mt-2 mb-0">Guru ini tidak memiliki riwayat pelanggaran.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

@endsection