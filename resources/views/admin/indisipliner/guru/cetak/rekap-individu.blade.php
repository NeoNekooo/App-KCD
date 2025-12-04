<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pelanggaran Guru - {{ $guru->nama }}</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 11pt;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        /* MARGIN CETAK DIPERKECIL AGAR TIDAK TERPOTONG */
        @page {
            size: A4;
            margin: 1cm 1.5cm;
        }

        .container-print {
            width: 100%;
            margin: 0 auto;
            padding: 0;
        }

        /* KOP SURAT */
        .kop-surat {
            border-bottom: 3px double black;
            padding-bottom: 4px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            text-align: center;
            padding-top: 5px !important;
        }

        .kop-logo {
            width: 70px;
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
        }

        .kop-teks h3 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
        }

        .kop-teks h2 {
            margin: 0;
            font-size: 15pt;
            font-weight: bold;
        }

        .kop-teks p {
            margin-top: 2px;
            font-size: 9pt;
            font-style: italic;
        }

        .judul-laporan {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            margin: 10px 0;
        }

        .table-data-guru td {
            padding: 2px 0;
        }

        .table-pelanggaran {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 10pt;
        }

        .table-pelanggaran th, .table-pelanggaran td {
            border: 1px solid #000;
            padding: 4px;
        }

        .table-pelanggaran th {
            background: #f5f5f5;
            text-align: center;
        }

        /* FIX: TANPA FLOAT, biar tidak dorong halaman */
        .ttd {
            width: 100%;
            text-align: right;
            margin-top: 40px;
            display: block;
        }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .container-print { padding: 0; }
        }
    </style>
</head>

<body onload="window.print()">

<div class="container-print">

    <!-- KOP -->
    <div class="kop-surat">
        <img src="{{ asset('assets/img/logo-sekolah.png') }}" class="kop-logo">
        <div class="kop-teks">
            <h3>PEMERINTAH {{ strtoupper($sekolah->provinsi ?? 'DAERAH') }}</h3>
            <h3>DINAS PENDIDIKAN</h3>
            <h2>{{ strtoupper($sekolah->nama) }}</h2>
            <p>{{ $sekolah->alamat_jalan }}, {{ $sekolah->kecamatan }}, {{ $sekolah->kabupaten_kota }} - {{ $sekolah->kode_pos }}
                <br>Telp: {{ $sekolah->nomor_telepon }} | Email: {{ $sekolah->email }}
            </p>
        </div>
    </div>

    <div class="judul-laporan">DATA REKAPITULASI INDISIPLINER GURU</div>

    <table class="table-data-guru">
        <tr>
            <td style="width:150px;">Nama Guru</td>
            <td style="width:10px;">:</td>
            <td><strong>{{ $guru->nama }}</strong></td>
        </tr>
        <tr>
            <td>NIP</td>
            <td>:</td>
            <td><strong>{{ $guru->nip ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td>Status Kepegawaian</td>
            <td>:</td>
            <td>{{ $guru->status_kepegawaian ?? '-' }}</td>
        </tr>
    </table>

    <hr>

    <table class="table-pelanggaran">
        <thead>
        <tr>
            <th style="width:5%;">No</th>
            <th style="width:15%;">Tanggal</th>
            <th>Pelanggaran</th>
            <th style="width:10%;">Poin</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($pelanggaranGuru as $key => $p)
            <tr>
                <td class="text-center">{{ $key+1 }}</td>
                <td>{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y') }}</td>
                <td>{{ $p->detailPoinGtk->nama ?? '-' }}</td>
                <td class="text-center">{{ $p->poin }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:8px;">Tidak ada data pelanggaran.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:10px; font-size:11pt;">
        <strong>Total Poin Akumulasi:</strong>
        <span style="color:red;">{{ $totalPoin }} Poin</span><br>
        <strong>Sanksi Aktif:</strong>
        <strong>{{ $sanksiAktif->nama ?? 'TIDAK ADA SANKSI' }}</strong>
    </div>

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
