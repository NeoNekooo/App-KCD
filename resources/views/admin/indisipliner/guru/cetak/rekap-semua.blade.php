<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Semua Guru</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 11pt;
            line-height: 1.15;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        @page {
            size: A4;
            /* Margin atas dinaikkan agar kop tidak kepotong */
            margin: 2.2cm 1.5cm 1.5cm 2.5cm;
        }

        .container-print { 
            width: 100%; 
            margin: 0 auto; 
        }

        /* KOP SURAT */
        .kop-surat {
            border-bottom: 3px double black;
            padding-bottom: 6px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            text-align: center;
        }
        .kop-logo {
            width: 80px;
            height: auto;
            position: absolute;
            left: 0;
            top: 8px; /* diturunkan sedikit agar aman */
        }
        .kop-teks h3 {
            margin: 0;
            font-size: 12pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-teks h2 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-teks p {
            font-size: 9pt;
            margin-top: 3px;
            font-style: italic;
        }

        .judul-laporan {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-top: 5px; /* diberi sedikit space supaya tidak dempet */
            margin-bottom: 20px;
        }

        /* TABEL */
        .table-pelanggaran {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10pt;
        }
        .table-pelanggaran th, .table-pelanggaran td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
            text-align: left;
        }
        .table-pelanggaran th {
            text-align: center;
            background-color: #f0f0f0;
        }

        .ttd {
            width: 250px;
            text-align: center;
            float: right;
            margin-top: 50px;
        }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
    </style>
</head>

<body onload="window.print()">
    
    <div class="container-print">

        <div class="kop-surat">
            <img src="{{ asset('assets/img/logo-sekolah.png') }}" class="kop-logo" alt="Logo">
            <div class="kop-teks">
                <h3>PEMERINTAH {{ strtoupper($sekolah->provinsi ?? 'PROVINSI') }}</h3> 
                <h3>DINAS PENDIDIKAN</h3>
                <h2>{{ strtoupper($sekolah->nama) }}</h2>
                <p>
                    {{ $sekolah->alamat_jalan }}, {{ $sekolah->kecamatan }},
                    {{ $sekolah->kabupaten_kota }} - {{ $sekolah->kode_pos }}<br>
                    Telp: {{ $sekolah->nomor_telepon }} |
                    Email: {{ $sekolah->email }}
                </p>
            </div>
        </div>

        <div class="judul-laporan">
            DATA REKAPITULASI KESELURUHAN INDISIPLINER GURU
        </div>

        <table class="table-pelanggaran">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Nama Guru</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 35%;">Pelanggaran</th>
                    <th style="width: 10%;">Poin</th>
                    <th style="width: 10%;">Tahun Pelajaran</th>
                    <th style="width: 10%;">Semester</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pelanggaranList as $key => $p)
                    <tr>
                        <td style="text-align: center;">{{ $key + 1 }}</td>
                        <td>{{ $p->nama_guru }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d/m/Y') }}</td>
                        <td>{{ $p->detailPoinGtk->nama ?? '-' }}</td>
                        <td style="text-align: center;">{{ $p->poin }}</td>
                        <td style="text-align: center;">{{ $p->tapel }}</td>
                        <td style="text-align: center;">{{ ucfirst($p->semester) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 15px;">Tidak ada data pelanggaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="ttd">
            <p>{{ $sekolah->kabupaten_kota ?? 'Tempat' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Kepala Sekolah</p>
            <br><br><br>
            <p><strong><u>RIRI</u></strong></p>
            <p>NIP. ............................................</p>
        </div>

    </div>

</body>
</html>
