<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sanksi->nama ?? 'Peringatan' }} - {{ $guru->nama }}</title>

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
            margin: 1.5cm 2cm 1.5cm 2.5cm;
        }

        .container { width: 100%; margin: 0 auto; }

        /* KOP SURAT */
        .kop-surat {
            border-bottom: 3px double black;
            padding-bottom: 5px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            text-align: center;
        }
        .kop-logo {
            width: 85px;
            height: auto;
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
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
            margin-top: 2px;
            font-style: italic;
        }

        /* HEADER SURAT */
        .tabel-header-surat {
            width: 100%;
            margin-bottom: 15px;
        }
        .tabel-header-surat td { padding-bottom: 2px; }

        /* ISI SURAT */
        .paragraf {
            text-indent: 35px;
            margin-bottom: 8px;
            text-align: justify;
        }

        .tabel-data {
            margin-left: 35px;
            width: 95%;
        }
        .tabel-data td { padding: 2px 0; }

        /* TTD */
        .ttd-container {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        .ttd-box {
            width: 45%;
        }
        .jabatan { font-weight: bold; margin-bottom: 60px; }
        .nama-pejabat { font-weight: bold; text-decoration: underline; }

        /* PRINT */
        /* Menambahkan kelas .no-print agar toolbar tidak tercetak */
        .no-print {
            background: #333;
            padding: 10px;
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 9999;
            text-align: center;
        }
        .btn {
            padding: 8px 15px;
            color: white;
            border-radius: 4px;
            font-weight: bold;
            text-decoration: none;
        }
        .btn-print { background: #28a745; }
        .btn-back { background: #6c757d; }
        .spacer-top { height: 50px; display: block; }

        @media print {
            .no-print, .spacer-top { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="javascript:history.back()" class="btn btn-back">← Kembali</a> 
    </div>

    <div class="spacer-top"></div>

    <div class="container">

        <div class="kop-surat">
            <img src="{{ asset('assets/img/logo-sekolah.png') }}" class="kop-logo" alt="Logo">
            <div class="kop-teks">
                <h3>PEMERINTAH {{ strtoupper($sekolah->provinsi ?? 'DAERAH') }}</h3> 
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

        <table class="tabel-header-surat">
            <tr>
                <td>Nomor</td><td>:</td>
                <td>423 / ......... / BK / {{ date('Y') }}</td>
                <td style="text-align:right;">
                    {{ $sekolah->kabupaten_kota }}, 
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr><td>Lampiran</td><td>:</td><td colspan="2">-</td></tr>
            <tr>
                <td>Perihal</td><td>:</td>
                <td colspan="2" style="font-weight:bold; text-decoration:underline;">
                    {{ strtoupper($sanksi->nama ?? 'SURAT PERINGATAN / PEMBINAAN') }}
                </td>
            </tr>
        </table>

        <p>Kepada Yth.</p>
        <p><strong>{{ $guru->nama }}</strong></p>
        <p>di Tempat</p>

        <p class="paragraf">
            Dengan hormat, berdasarkan hasil monitoring dan evaluasi kedisiplinan tenaga pendidik,
            kami memberitahukan bahwa:
        </p>

        <table class="tabel-data">
            <tr><td>Nama Guru</td><td>:</td><td><strong>{{ $guru->nama }}</strong></td></tr>
            <tr><td>NIP</td><td>:</td><td><strong>{{ $guru->nip ?? '-' }}</strong></td></tr>
            <tr>
                <td>Total Poin Pelanggaran</td>
                <td>:</td>
                <td style="color:red;"><strong>{{ $totalPoin }} Poin</strong></td> 
            </tr>
        </table>

        <p class="paragraf">
            Telah mencapai batas poin pelanggaran dalam ketentuan tata tertib guru
            @if ($sanksi)
            (rentang {{ $sanksi->poin_min }}–{{ $sanksi->poin_max }} poin).
            Sehubungan dengan itu, guru yang bersangkutan dikenakan sanksi:
            <strong>{{ $sanksi->nama }}</strong>.
            @else
            (poin 0 atau di bawah batas minimum sanksi).
            Sehubungan dengan itu, guru yang bersangkutan dikenakan pembinaan ringan.
            @endif
        </p>

        <p class="paragraf">
            Dimohon kepada yang bersangkutan untuk hadir dalam pembinaan pada:
        </p>

        <table class="tabel-data">
            <tr><td>Hari/Tanggal</td><td>:</td><td>.......................</td></tr>
            <tr><td>Waktu</td><td>:</td><td>08.00 WIB s.d selesai</td></tr>
            <tr><td>Tempat</td><td>:</td><td>Ruang BK / Kesiswaan</td></tr>
            <tr><td>Agenda</td><td>:</td><td>Pembinaan Tenaga Pendidik</td></tr>
        </table>

        <p class="paragraf">
            Demikian surat ini disampaikan. Atas perhatian dan kerjasamanya kami ucapkan terima kasih.
        </p>

        <div class="ttd-container">
            <div class="ttd-box">
                <div>{{ $sekolah->kabupaten_kota }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div class="jabatan">Waka Kesiswaan</div>
                <div class="nama-pejabat">( ................................................ )</div>
                <div>NIP. ............................................</div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Memicu dialog cetak secara otomatis
            window.print();
        });
    </script>
</body>
</html>