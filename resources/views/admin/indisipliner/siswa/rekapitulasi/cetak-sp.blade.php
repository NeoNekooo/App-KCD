<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sanksi->nama }} - {{ $siswa->nama }}</title>
    <style>
        /* Reset & Font Standar Surat Resmi */
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt; /* Ukuran 11pt lebih aman untuk muat 1 halaman */
            line-height: 1.15; /* Spasi baris standar surat dinas (agak rapat) */
            margin: 0;
            padding: 0;
            color: #000;
            background: #fff;
        }

        /* Kertas A4 - Margin Dioptimalkan */
        @page {
            size: A4;
            /* Margin: Atas 1.5cm, Kanan 2cm, Bawah 1.5cm, Kiri 2.5cm */
            margin: 1.5cm 2cm 1.5cm 2.5cm; 
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        /* =========================================
           KOP SURAT
           ========================================= */
        .kop-surat {
            border-bottom: 3px double black;
            padding-bottom: 5px;
            margin-bottom: 15px; /* Jarak ke bawah dirapatkan */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }
        .kop-logo {
            width: 75px;
            height: auto;
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
        }
        .kop-teks {
            width: 100%;
            padding-left: 85px; 
            padding-right: 10px;
            box-sizing: border-box;
        }
        .kop-teks h3 { 
            margin: 0; 
            font-size: 12pt; 
            text-transform: uppercase; 
            font-weight: normal;
            letter-spacing: 1px;
        }
        .kop-teks h2 { 
            margin: 0;
            font-size: 16pt; 
            text-transform: uppercase; 
            font-weight: bold; 
            letter-spacing: 1px;
        }
        .kop-teks p { 
            margin: 2px 0 0 0; 
            font-size: 9pt; 
            font-style: italic;
        }

        /* =========================================
           BAGIAN NOMOR & PERIHAL
           ========================================= */
        .header-surat {
            margin-bottom: 15px;
        }
        .tabel-header-surat {
            width: 100%;
            border-collapse: collapse;
        }
        .tabel-header-surat td {
            vertical-align: top;
            padding-bottom: 2px; 
        }
        .kolom-label { width: 80px; }
        .kolom-sep { width: 10px; text-align: center; }

        /* =========================================
           ISI SURAT
           ========================================= */
        .isi-surat {
            text-align: justify;
            margin-bottom: 10px;
        }
        .paragraf {
            text-indent: 35px; /* Indentasi awal paragraf */
            margin-bottom: 8px;
        }
        .tujuan-surat {
            margin-bottom: 15px;
        }
        
        /* Tabel Data di Dalam Surat */
        .tabel-data {
            margin: 5px 0 10px 35px; /* Indentasi tabel sejajar paragraf */
            width: 95%;
            border-collapse: collapse;
        }
        .tabel-data td {
            padding: 1px 0; /* Baris tabel rapat */
            vertical-align: top;
        }
        .td-label { width: 150px; }
        .td-sep { width: 15px; text-align: center; }
        .td-isi { font-weight: bold; }

        /* =========================================
           TANDA TANGAN
           ========================================= */
        .ttd-container {
            margin-top: 20px;
            width: 100%;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid; /* Mencegah potong halaman */
        }
        .ttd-box {
            width: 40%; 
            text-align: left;
        }
        .ttd-box .kota-tanggal {
            margin-bottom: 5px;
        }
        .ttd-box .jabatan {
            font-weight: bold;
            margin-bottom: 70px; /* Ruang tanda tangan */
        }
        .ttd-box .nama-pejabat {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        /* =========================================
           TOMBOL CETAK & MEDIA QUERY
           ========================================= */
        .toolbar-cetak {
            background: #333; 
            padding: 10px;
            text-align: center;
            position: fixed; 
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
        }
        .btn {
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 4px;
            border: none;
            font-weight: bold;
            color: white;
            margin: 0 5px;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        .btn-print { background-color: #28a745; }
        .btn-back { background-color: #6c757d; }
        .spacer-top { height: 50px; display: block; }

        @media print {
            .no-print, .toolbar-cetak, .spacer-top { display: none !important; } 
            body { background: none; }
            @page { margin: 1.5cm 2cm 1.5cm 2.5cm; }
        }
    </style>
</head>
<body>

    <div class="toolbar-cetak no-print">
        <a href="javascript:history.back()" class="btn btn-back">&#8592; Kembali</a>
        <button onclick="window.print()" class="btn btn-print">🖨️ Cetak Surat</button>
    </div>

    <div class="spacer-top no-print"></div>

    <div class="container">
        
        <!-- KOP SURAT -->
        <div class="kop-surat">
            {{-- <img src="{{ asset('assets/img/logo-sekolah.png') }}" alt="Logo" class="kop-logo"> --}}
            <div class="kop-teks">
                <h3>PEMERINTAH {{ strtoupper($sekolah->provinsi ?? 'PROVINSI JAWA BARAT') }}</h3>
                <h3>DINAS PENDIDIKAN</h3>
                <h2>{{ strtoupper($sekolah->nama ?? 'NAMA SEKOLAH BELUM DISET') }}</h2>
                <p>
                    Alamat: {{ $sekolah->alamat_jalan ?? 'Alamat Sekolah' }}, 
                    {{ $sekolah->kecamatan ?? '' }}, 
                    {{ $sekolah->kabupaten_kota ?? '' }} - {{ $sekolah->kode_pos ?? '' }}
                    <br>
                    @if(!empty($sekolah->nomor_telepon)) Telp: {{ $sekolah->nomor_telepon }} @endif
                    @if(!empty($sekolah->email)) | Email: {{ $sekolah->email }} @endif
                    @if(!empty($sekolah->website)) | Website: {{ str_replace(['http://', 'https://'], '', $sekolah->website) }} @endif
                </p>
            </div>
        </div>

        <!-- HEADER SURAT -->
        <div class="header-surat">
            <table class="tabel-header-surat">
                <tr>
                    <td class="kolom-label">Nomor</td>
                    <td class="kolom-sep">:</td>
                    <td>421.5 / ........... / BK / {{ date('Y') }}</td>
                    <td style="text-align: right;">{{ ucwords(strtolower($sekolah->kabupaten_kota ?? 'Bandung')) }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="kolom-label">Lampiran</td>
                    <td class="kolom-sep">:</td>
                    <td colspan="2">-</td>
                </tr>
                <tr>
                    <td class="kolom-label">Perihal</td>
                    <td class="kolom-sep">:</td>
                    <td colspan="2" style="font-weight: bold; text-decoration: underline;">
                        {{ strtoupper($sanksi->nama) }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- TUJUAN SURAT -->
        <div class="tujuan-surat">
            Yth. Bapak/Ibu Orang Tua/Wali Peserta Didik<br>
            dari <strong>{{ $siswa->nama }}</strong><br>
            di<br>
            <span style="padding-left: 25px">Tempat</span>
        </div>

        <!-- ISI SURAT -->
        <div class="isi-surat">
            <p class="paragraf">
                Dengan hormat,<br>
                Puji syukur kita panjatkan ke hadirat Tuhan Yang Maha Esa, semoga Bapak/Ibu senantiasa dalam lindungan-Nya. 
                Berdasarkan rekapitulasi data kedisiplinan siswa yang tercatat pada Tim Ketertiban/BK sekolah, 
                dengan ini kami memberitahukan bahwa putra/putri Bapak/Ibu:
            </p>

            <table class="tabel-data">
                <tr>
                    <td class="td-label">Nama Peserta Didik</td>
                    <td class="td-sep">:</td>
                    <td class="td-isi">{{ $siswa->nama }}</td>
                </tr>
                <tr>
                    <td class="td-label">Kelas</td>
                    <td class="td-sep">:</td>
                    <td class="td-isi">{{ $siswa->rombel->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="td-label">NISN / NIPD</td>
                    <td class="td-sep">:</td>
                    <td class="td-isi">{{ $siswa->nisn }} / {{ $siswa->nipd }}</td>
                </tr>
                <tr>
                    <td class="td-label">Total Poin Pelanggaran</td>
                    <td class="td-sep">:</td>
                    <td class="td-isi" style="color: red;">{{ $totalPoin }} Poin</td>
                </tr>
            </table>

            <p class="paragraf">
                Telah mencapai batas akumulasi poin pelanggaran yang ditetapkan dalam Tata Tertib Sekolah (Rentang {{ $sanksi->poin_min }} s.d. {{ $sanksi->poin_max }} Poin). 
                Sesuai dengan peraturan yang berlaku, siswa yang bersangkutan dikenakan sanksi administratif berupa: <strong>{{ $sanksi->nama }}</strong>.
            </p>

            <p class="paragraf">
                Sehubungan dengan hal tersebut, demi pembinaan dan kelancaran pendidikan putra/putri Bapak/Ibu, kami mengharapkan kehadiran Bapak/Ibu di sekolah pada:
            </p>

            <table class="tabel-data">
                <tr>
                    <td class="td-label">Hari, Tanggal</td>
                    <td class="td-sep">:</td>
                    <td>......................................................................</td>
                </tr>
                <tr>
                    <td class="td-label">Waktu</td>
                    <td>:</td>
                    <td>08.00 WIB s.d. Selesai</td>
                </tr>
                <tr>
                    <td class="td-label">Tempat</td>
                    <td>:</td>
                    <td>Ruang BK / Kesiswaan</td>
                </tr>
                <tr>
                    <td class="td-label">Acara</td>
                    <td>:</td>
                    <td>Konsultasi dan Pembinaan Siswa</td>
                </tr>
            </table>

            <p class="paragraf">
                Mengingat pentingnya hal ini, dimohon Bapak/Ibu hadir tepat waktu. Demikian surat panggilan ini kami sampaikan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
            </p>
        </div>

        <!-- TANDA TANGAN -->
        <div class="ttd-container">
            <div class="ttd-box">
                <div class="kota-tanggal">
                    {{ ucwords(strtolower($sekolah->kabupaten_kota ?? 'Bandung')) }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </div>
                <div class="jabatan">
                    Mengetahui,<br>
                    Waka Kesiswaan
                </div>
                <div style="height: 70px;"></div>
                <div class="nama-pejabat">
                    ( ........................................................... )
                </div>
                <div class="nip">
                    NIP. ..........................................
                </div>
            </div>
        </div>

        <!-- TEMBUSAN -->
        {{-- <div style="margin-top: 10px; font-size: 9pt;">
            <strong>Tembusan:</strong>
            <ol style="margin-top: 0; padding-left: 20px;">
                <li>Kepala Sekolah</li>
                <li>Wali Kelas {{ $siswa->rombel->nama ?? '' }}</li>
                <li>Arsip BK</li>
            </ol>
        </div> --}}

    </div>
    
    <script>
        window.print();
    </script>
</body>
</html>