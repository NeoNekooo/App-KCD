    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Laporan Absensi {{ $rombel->nama }} - {{ $bulan }} {{ $tahun }}</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            @media print {
                body { -webkit-print-color-adjust: exact; }
                .no-print { display: none; }
            }
            .table-bordered th, .table-bordered td { text-align: center; vertical-align: middle; padding: 0.25rem; }
            .table-responsive { font-size: 0.8rem; }
            td.H { background-color: #d4edda !important; }
            td.S { background-color: #fff3cd !important; }
            td.I { background-color: #cce5ff !important; }
            td.A { background-color: #f8d7da !important; }
            .nama-siswa { min-width: 200px; text-align: left !important; }
        </style>
    </head>
    <body>
        <div class="container-fluid mt-4">
            <button class="btn btn-secondary float-end no-print" onclick="window.print()">Cetak</button>
            <h3 class="text-center">LAPORAN ABSENSI BULANAN SISWA</h3>
            <p class="text-center mb-4">
                <strong>Kelas:</strong> {{ $rombel->nama }} | <strong>Periode:</strong> {{ $bulan }} {{ $tahun }}
            </p>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr class="align-middle">
                            <th rowspan="2">No</th>
                            <th rowspan="2">NIS</th>
                            <th rowspan="2" class="nama-siswa">Nama Siswa</th>
                            <th colspan="{{ $jumlahHari }}">Tanggal</th>
                            <th colspan="4">Rekapitulasi</th>
                        </tr>
                        <tr>
                            @for ($i = 1; $i <= $jumlahHari; $i++)
                                <th>{{ $i }}</th>
                            @endfor
                            <th>H</th>
                            <th>S</th>
                            <th>I</th>
                            <th>A</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftarSiswa as $index => $siswa)
                            @php
                                $data = $dataLaporan[$siswa->id];
                                $rekap = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alfa' => 0];
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data['nis'] }}</td>
                                <td class="nama-siswa">{{ $data['nama'] }}</td>
                                @for ($hari = 1; $hari <= $jumlahHari; $hari++)
                                    @php
                                        $absenHariIni = $data['absensi']->get($hari);
                                        $kode = '-';
                                        if ($absenHariIni) {
                                            $status = $absenHariIni->status;
                                            if (array_key_exists($status, $rekap)) {
                                                $rekap[$status]++;
                                            }
                                            $kode = substr($status, 0, 1);
                                        }
                                    @endphp
                                    <td class="{{ $kode }}">{{ $kode }}</td>
                                @endfor
                                <td>{{ $rekap['Hadir'] }}</td>
                                <td>{{ $rekap['Sakit'] }}</td>
                                <td>{{ $rekap['Izin'] }}</td>
                                <td>{{ $rekap['Alfa'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </body>
    </html>
    
