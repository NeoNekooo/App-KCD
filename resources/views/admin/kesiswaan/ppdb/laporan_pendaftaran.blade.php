@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">PPDB /</span> Laporan Pendaftaran PPDB
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">Laporan Pendaftaran</h5>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 70%">Kriteria</th>
                            <th style="width: 25%">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data statis dulu, nanti bisa diganti dari controller --}}
                        <tr>
                            <td><strong>1</strong></td>
                            <td>Total Pendaftaran</td>
                            <td>{{ $tp }}</td>
                        </tr>
                        <tr>
                            <td><strong>2</strong></td>
                            <td>Jumlah Pendaftar Laki-Laki</td>
                            <td>{{ $l }}</td>
                        </tr>
                        <tr>
                            <td><strong>3</strong></td>
                            <td>Jumlah Pendaftar Perempuan</td>
                            <td>{{ $p }}</td>
                        </tr>

                        {{-- Jalur --}}
                        <tr class="table-active">
                            <td><strong>4</strong></td>
                            <td colspan="2"><strong>Laporan berdasarkan jalur pendaftaran</strong></td>
                        </tr>
                        @forelse ($laporanJalur as $jalur)
                            <tr>
                                <td></td>
                                <td class="ps-4">- {{ $jalur['nama'] ?? '-' }}</td>
                                <td>{{ $jalur['jumlah'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td></td>
                                <td class="ps-4 text-muted">Tidak ada jalur aktif</td>
                                <td>0</td>
                            </tr>
                        @endforelse

                        {{-- Laporan Jurusan --}}
                        <tr class="table-active">
                            <td><strong>5</strong></td>
                            <td colspan="2"><strong>Laporan berdasarkan Jurusan</strong></td>
                        </tr>
                        @forelse ($laporanJurusan as $jur)
                            <tr>
                                <td></td>
                                <td class="ps-4">- {{ $jur['nama'] }}</td>
                                <td>{{ $jur['jumlah'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td></td>
                                <td class="ps-4 text-muted">Tidak ada jurusan terdaftar di rombel</td>
                                <td>0</td>
                            </tr>
                        @endforelse

                        {{-- Laporan Jurusan yang sudah memenuhi syarat --}}
                        <tr class="table-active">
                            <td><strong>6</strong></td>
                            <td colspan="2"><strong>Laporan calon siswa registrasi (syarat lengkap) berdasarkan jurusan</strong></td>
                        </tr>
                        @forelse ($laporanJurusanRegistrasi as $jur)
                            <tr>
                                <td></td>
                                <td class="ps-4">- {{ $jur['nama'] }}</td>
                                <td>{{ $jur['jumlah'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td></td>
                                <td class="ps-4 text-muted">Belum ada data registrasi</td>
                                <td>0</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
