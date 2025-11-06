@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">PPDB /</span> Laporan Quota PPDB</h4>

{{-- Pesan Sukses --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="card-header text-center">
        <h5 class="mb-1">LAPORAN QUOTA PPDB</h5>
        <h6 class="mb-0">SMK NURUL ISLAM CIANJUR</h6>
        <small class="text-muted">TAHUN PELAJARAN {{ $tahunAktif->tahun_pelajaran ?? '----' }}</small>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Paket Keahlian</th>
                        <th>Jumlah Kelas</th>
                        <th>Quota</th>
                        <th>Jumlah Pendaftar</th>
                        <th>Sisa Quota</th>
                        <th>Sudah Registrasi</th>
                        <th>Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalKelas = 0;
                        $totalQuota = 0;
                        $totalPendaftar = 0;
                        $totalRegistrasi = 0;
                    @endphp

                    @foreach ($laporan as $i => $row)
                        @php
                            $sisaQuota = $row->quota - $row->jumlah_pendaftar;
                            $persentase = $row->quota > 0
                                ? number_format(($row->jumlah_registrasi / $row->quota) * 100, 2)
                                : 0;
                            $totalKelas += $row->jumlah_kelas;
                            $totalQuota += $row->quota;
                            $totalPendaftar += $row->jumlah_pendaftar;
                            $totalRegistrasi += $row->jumlah_registrasi;
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="text-start">{{ $row->paket_keahlian }}</td>
                            <td>{{ $row->jumlah_kelas }}</td>
                            <td>{{ $row->quota }}</td>
                            <td>{{ $row->jumlah_pendaftar }}</td>
                            <td>{{ $sisaQuota }}</td>
                            <td>{{ $row->jumlah_registrasi }}</td>
                            <td>{{ $persentase }} %</td>
                        </tr>
                    @endforeach

                    {{-- TOTAL --}}
                    <tr class="fw-bold table-light">
                        <td colspan="2" class="text-center">TOTAL</td>
                        <td>{{ $totalKelas }}</td>
                        <td>{{ $totalQuota }}</td>
                        <td>{{ $totalPendaftar }}</td>
                        <td>{{ $totalQuota - $totalPendaftar }}</td>
                        <td>{{ $totalRegistrasi }}</td>
                        <td>{{ $totalQuota > 0 ? number_format(($totalRegistrasi / $totalQuota) * 100, 2) : 0 }} %</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
