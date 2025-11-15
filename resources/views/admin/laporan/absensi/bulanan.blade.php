@extends('layouts.admin') {{-- Sesuaikan dengan layout admin Anda --}}

@section('title', 'Laporan Absensi Bulanan')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Absensi Bulanan</h3>
    </div>
    <div class="card-body">
        {{-- FORM FILTER --}}
        <form action="{{ route('admin.laporan.absensi.bulanan') }}" method="GET" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="rombel_id" class="form-label">Pilih Kelas</label>
                    <select name="rombel_id" id="rombel_id" class="form-select" required>
                        <option value="">-- Pilih Rombongan Belajar --</option>
                        @foreach($rombels as $item)
                            <option value="{{ $item->id }}" {{ request('rombel_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="bulan" class="form-label">Bulan</label>
                    <select name="bulan" id="bulan" class="form-select" required>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan', date('m')) == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="tahun" class="form-label">Tahun</label>
                    <input type="number" name="tahun" id="tahun" class="form-control" value="{{ request('tahun', date('Y')) }}" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                </div>
            </div>
        </form>

        {{-- HASIL LAPORAN (HANYA TAMPIL JIKA ADA DATA) --}}
        @if(isset($dataLaporan))
            <hr>
            <div id="laporan-area">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="mb-0">Laporan Absensi Kelas: {{ $rombel->nama }}</h4>
                        <p class="mb-0 text-muted">Periode: {{ $bulan }} {{ $tahun }}</p>
                    </div>
                    <button class="btn btn-success" onclick="window.print()">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        {{-- (Konten tabel sama seperti sebelumnya) --}}
                        <thead class="text-center align-middle">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2" style="min-width: 180px; text-align: left;">Nama Siswa</th>
                                <th colspan="{{ $jumlahHari }}">Tanggal</th>
                                <th colspan="4">Rekapitulasi</th>
                            </tr>
                            <tr>
                                @for ($i = 1; $i <= $jumlahHari; $i++) <th>{{ $i }}</th> @endfor
                                <th>H</th><th>S</th><th>I</th><th>A</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daftarSiswa as $index => $siswa)
                                @php
                                    $data = $dataLaporan[$siswa->id];
                                    $rekap = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alfa' => 0];
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td style="text-align: left;">{{ $data['nama'] }}</td>
                                    @for ($hari = 1; $hari <= $jumlahHari; $hari++)
                                        @php
                                            $absenHariIni = $data['absensi']->get($hari);
                                            $kode = '-';
                                            if ($absenHariIni) {
                                                $status = $absenHariIni->status;
                                                if (array_key_exists($status, $rekap)) $rekap[$status]++;
                                                $kode = substr($status, 0, 1);
                                            }
                                        @endphp
                                        <td class="text-center status-{{ $kode }}">{{ $kode }}</td>
                                    @endfor
                                    <td class="text-center">{{ $rekap['Hadir'] }}</td>
                                    <td class="text-center">{{ $rekap['Sakit'] }}</td>
                                    <td class="text-center">{{ $rekap['Izin'] }}</td>
                                    <td class="text-center">{{ $rekap['Alfa'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- CSS untuk pewarnaan dan print --}}
@push('styles')
<style>
    .status-H { background-color: #d4edda !important; }
    .status-S { background-color: #fff3cd !important; }
    .status-I { background-color: #cce5ff !important; }
    .status-A { background-color: #f8d7da !important; }
    @media print {
        body * { visibility: hidden; }
        #laporan-area, #laporan-area * { visibility: visible; }
        #laporan-area { position: absolute; left: 0; top: 0; width: 100%; }
        .table { font-size: 10px; }
        /* Pastikan background tercetak */
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endpush
@endsection
