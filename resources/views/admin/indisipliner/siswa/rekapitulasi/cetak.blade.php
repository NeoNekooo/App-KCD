<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pelanggaran - {{ $siswa->nama }}</title>
    <style>
        /* --- RESET & HALAMAN --- */
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            color: #000;
            background: #fff;
        }
        @page {
            size: A4;
            margin: 2cm;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }

        /* --- KOP SURAT --- */
        .kop-surat {
            border-bottom: 3px double black;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }

        /* LOGO */
        .kop-logo {
            width: 90px;
            height: auto;
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
        }

        /* TEKS KOP */
        .kop-teks {
            width: 100%;
            padding: 0 100px; 
        }
        .kop-teks h3 { margin: 0; font-size: 14pt; text-transform: uppercase; line-height: 1.2; }
        .kop-teks h2 { margin: 5px 0; font-size: 18pt; text-transform: uppercase; font-weight: bold; line-height: 1.2; }
        .kop-teks p { margin: 0; font-size: 10pt; line-height: 1.3; }

        /* --- JUDUL --- */
        .judul-laporan {
            text-align: center;
            margin-bottom: 25px;
        }
        .judul-laporan h4 {
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
        }

        /* --- BIODATA --- */
        .biodata-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .biodata-table td {
            padding: 3px 5px;
            vertical-align: top;
        }
        .label-col { 
            width: 1%;
            font-weight: bold; 
            white-space: nowrap;
        }
        .sep-col { 
            width: 10px; 
            text-align: center;
            white-space: nowrap;
        }
        .val-col { width: auto; }

        /* --- TABEL DATA --- */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        /* SOLUSI AGAR HEADER TIDAK MUNCUL LAGI DI HALAMAN KEDUA */
        .data-table thead {
            display: table-row-group;
        }

        .data-table th, .data-table td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            font-size: 11pt;
        }
        .data-table th {
            background-color: #f0f0f0 !important;
            text-align: center;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .nowrap { white-space: nowrap; }

        /* Baris Total */
        .row-total td {
            font-weight: bold;
            border-top: 2px solid black;
        }

        /* --- TANDA TANGAN --- */
        .signature-section {
            margin-top: 50px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            text-align: center;
        }
        .signature-space { height: 80px; }

        /* --- NAVIGASI LAYAR --- */
        .no-print {
            background: #f1f1f1;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ccc;
            margin-bottom: 20px;
        }
        .btn-print { background-color: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        .btn-back { background-color: #6c757d; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin-right: 10px; }

        @media print {
            .no-print { display: none; }
            @page { margin: 2cm; }
            
            /* Memastikan trik header berfungsi saat print */
            thead { display: table-row-group; } 
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="javascript:history.back()" class="btn-back">Kembali</a>
        <button onclick="window.print()" class="btn-print">Cetak Laporan</button>
    </div>

    <div class="container">
        
        <div class="kop-surat">
            @if(!empty($sekolah->logo))
                <img src="{{ asset('storage/' . $sekolah->logo) }}" 
                     alt="Logo Sekolah" 
                     class="kop-logo"
                     onerror="this.style.display='none'">
            @endif

            <div class="kop-teks">
                <h3>PEMERINTAH {{ strtoupper($sekolah->provinsi ?? 'PROVINSI JAWA BARAT') }}</h3>
                <h3>DINAS PENDIDIKAN</h3>
                <h2>{{ strtoupper($sekolah->nama ?? 'NAMA SEKOLAH BELUM DISET') }}</h2>
                <p>
                    {{ $sekolah->alamat_jalan ?? 'Alamat' }}, {{ $sekolah->kecamatan ?? '' }}, {{ $sekolah->kabupaten_kota ?? '' }} 
                    @if(!empty($sekolah->kode_pos)) - {{ $sekolah->kode_pos }} @endif
                </p>
                <p>
                    @if(!empty($sekolah->nomor_telepon)) Telp: {{ $sekolah->nomor_telepon }} @endif
                    @if(!empty($sekolah->email)) | Email: {{ $sekolah->email }} @endif
                </p>
            </div>
        </div>

        <div class="judul-laporan">
            <h4>LAPORAN REKAPITULASI PELANGGARAN TATA TERTIB</h4>
            <span>Tahun Pelajaran {{ date('Y') }}/{{ date('Y')+1 }}</span>
        </div>

        <table class="biodata-table">
            <tr>
                <td class="label-col">Nama Siswa</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $siswa->nama }}</td>
                
                <td class="label-col" style="padding-left: 30px;">Kelas</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $siswa->rombel->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">NIPD / NISN</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $siswa->nipd }} / {{ $siswa->nisn }}</td>

                <td class="label-col" style="padding-left: 30px;">Tanggal Cetak</td>
                <td class="sep-col">:</td>
                <td class="val-col nowrap">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label-col">Total Poin</td>
                <td class="sep-col">:</td>
                <td class="val-col"><strong>{{ $totalPoin }}</strong></td>

                <td class="label-col" style="padding-left: 30px;">Status Sanksi</td>
                <td class="sep-col">:</td>
                <td class="val-col">
                    @if($sanksiAktif)
                        <span style="color: red; font-weight: bold;">{{ $sanksiAktif->nama }}</span>
                    @else
                        <span>-</span>
                    @endif
                </td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Tanggal</th>
                    <th style="width: 10%;">Waktu</th>
                    <th>Jenis Pelanggaran</th>
                    <th style="width: 10%;">Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelanggaranSiswa as $key => $p)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td class="text-center nowrap">
                        {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y') }}
                    </td>
                    <td class="text-center nowrap">
                        {{ \Carbon\Carbon::parse($p->jam)->format('H:i') }} WIB
                    </td>
                    <td>{{ $p->detailPoinSiswa->nama ?? 'Data dihapus' }}</td>
                    <td class="text-center">{{ $p->poin }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px;">
                        <i>Tidak ada riwayat pelanggaran yang tercatat.</i>
                    </td>
                </tr>
                @endforelse

                <tr class="row-total">
                    <td colspan="4" class="text-right">Total Akumulasi Poin</td>
                    <td class="text-center">{{ $totalPoin }}</td>
                </tr>
            </tbody>
        </table>

        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td width="33%">
                        Mengetahui,<br> Orang Tua / Wali
                        <div class="signature-space"></div>
                        ( ..................................... )
                    </td>
                    <td width="33%">
                        <br> Wali Kelas
                        <div class="signature-space"></div>
                        ( ..................................... )
                    </td>
                    <td width="33%">
                        {{ ucwords(strtolower($sekolah->kabupaten_kota ?? 'Bandung')) }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                        Waka Kesiswaan
                        <div class="signature-space"></div>
                        ( <strong>Nama Waka Kesiswaan</strong> )<br>
                        NIP. .......................
                    </td>
                </tr>
            </table>
        </div>

    </div>
    <script>
        window.print();
    </script>
</body>
</html>