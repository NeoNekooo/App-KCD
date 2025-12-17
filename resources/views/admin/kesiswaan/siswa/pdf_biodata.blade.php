<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Biodata Peserta Didik - {{ $siswa->nama }}</title> 
    
    <style>
        @page { margin: 1cm 2cm; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.3;
        }
        /* HEADER KOP SURAT */
        .header-table { width: 100%; border-bottom: 3px solid #000; margin-bottom: 10px; padding-bottom: 5px; }
        .header-table td { vertical-align: middle; text-align: center; }
        .logo { width: 75px; height: auto; object-fit: contain; }
        
        .kop-text { text-align: center; padding: 0 10px; }
        .kop-h1 { font-size: 14px; font-weight: bold; margin: 0; text-transform: uppercase; }
        .kop-h2 { font-size: 16px; font-weight: bold; margin: 2px 0; text-transform: uppercase; }
        .kop-address { font-size: 10px; font-style: italic; margin-top: 2px; }

        /* JUDUL HALAMAN */
        .page-title { text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin-top: 15px; margin-bottom: 5px; text-transform: uppercase; }
        .page-subtitle { text-align: center; font-size: 10px; margin-bottom: 20px; color: #333; }

        /* SECTIONS */
        .section-title { font-size: 11px; font-weight: bold; margin: 10px 0 5px 0; background-color: #e0e0e0; padding: 4px; border-left: 3px solid #000; text-transform: uppercase; }
        
        /* SUB-HEADER (AYAH/IBU) - GARIS TEGAS */
        .sub-header { 
            font-weight: bold; 
            border-bottom: 1px solid #000; /* Garis Solid Hitam */
            margin-bottom: 5px; 
            padding-bottom: 2px; 
            text-transform: uppercase;
            font-size: 10px;
        }

        /* TABEL DATA */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .data-table td { padding: 3px; vertical-align: top; }
        
        /* Layout Baris Data */
        .label { width: 32%; font-weight: bold; }
        .sep { width: 2%; text-align: center; }
        .val { width: 66%; }

        /* FOTO */
        .photo-wrapper { text-align: center; vertical-align: top; padding-top: 10px; }
        .photo-container {
            width: 3cm; height: 4cm;
            border: 1px solid #000; padding: 2px; 
            object-fit: cover; display: inline-block;
        }

        /* TANDA TANGAN */
        .signature-wrapper { margin-top: 30px; page-break-inside: avoid; }
        .signature-box { text-align: center; width: 250px; float: right; margin-right: 20px; }

        footer { position: fixed; bottom: 0; left: 0; right: 0; height: 20px; font-size: 8px; text-align: right; border-top: 1px solid #ccc; padding-top: 2px; color: #555; }
    </style>
</head>
<body>
    <footer>
        Dicetak melalui Sistem Informasi Sekolah pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
    </footer>

    {{-- KOP SURAT --}}
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: left;">
                @if(isset($sekolah->logo) && file_exists(storage_path('app/public/' . $sekolah->logo)))
                    <img src="{{ public_path('storage/' . $sekolah->logo) }}" class="logo">
                @else
                    <img src="https://upload.wikimedia.org/wikipedia/commons/9/9c/Logo_Tut_Wuri_Handayani.png" class="logo" style="opacity: 0.6"> 
                @endif
            </td>
            <td class="kop-text" style="width: 70%;">
                <div class="kop-h1">PEMERINTAH PROVINSI JAWA BARAT</div>
                <div class="kop-h1">DINAS PENDIDIKAN</div>
                <div class="kop-h2">{{ strtoupper($sekolah->nama ?? 'SMK MERDEKA BANDUNG') }}</div>
                <div class="kop-address">
                    {{ $sekolah->alamat_jalan ?? 'Jl. Pahlawan No. 123' }} 
                    {{ isset($sekolah->kecamatan) ? ', Kec. '.$sekolah->kecamatan : '' }}
                    {{ isset($sekolah->kabupaten_kota) ? ', '.$sekolah->kabupaten_kota : '' }}
                    <br>
                    Website: {{ $sekolah->website ?? '-' }} | Email: {{ $sekolah->email ?? '-' }}
                </div>
            </td>
            <td style="width: 15%;"></td>
        </tr>
    </table>

    <div class="page-title">BIODATA PESERTA DIDIK</div>
    <div class="page-subtitle">Laporan data per tanggal: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>

    {{-- A. IDENTITAS PRIBADI --}}
    <div class="section-title">A. KETERANGAN PRIBADI</div>
    <table class="data-table">
        <tr>
            {{-- KOLOM DATA (KIRI) --}}
            <td style="width: 75%; padding: 0;">
                <table class="data-table" style="margin: 0;">
                    <tr><td class="label">Nama Lengkap</td><td class="sep">:</td><td class="val"><b>{{ $siswa->nama }}</b></td></tr>
                    <tr><td class="label">NIPD / NISN</td><td class="sep">:</td><td class="val">{{ $siswa->nipd ?? '-' }} / {{ $siswa->nisn ?? '-' }}</td></tr>
                    <tr><td class="label">NIK</td><td class="sep">:</td><td class="val">{{ $siswa->nik ?? '-' }}</td></tr>
                    <tr><td class="label">Tempat, Tanggal Lahir</td><td class="sep">:</td><td class="val">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</td></tr>
                    <tr><td class="label">Jenis Kelamin</td><td class="sep">:</td><td class="val">{{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                    <tr><td class="label">Agama</td><td class="sep">:</td><td class="val">{{ $siswa->agama_id_str ?? '-' }}</td></tr>
                    <tr><td class="label">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $siswa->kewarganegaraan ?? 'Indonesia' }}</td></tr>
                    <tr><td class="label">Anak ke</td><td class="sep">:</td><td class="val">{{ $siswa->anak_keberapa ?? '-' }} (dari {{ $siswa->jumlah_saudara_kandung ?? '-' }} bersaudara)</td></tr>
                    <tr><td class="label">No. Handphone</td><td class="sep">:</td><td class="val">{{ $siswa->nomor_telepon_seluler ?? '-' }}</td></tr>
                    <tr><td class="label">Email</td><td class="sep">:</td><td class="val">{{ $siswa->email ?? '-' }}</td></tr>
                </table>
            </td>

            {{-- KOLOM FOTO (KANAN) --}}
            <td class="photo-wrapper" style="width: 25%;">
                @if($siswa->foto && file_exists(storage_path('app/public/' . $siswa->foto)))
                    <img src="{{ public_path('storage/' . $siswa->foto) }}" class="photo-container">
                @else
                    <div class="photo-container" style="display: flex; align-items: center; justify-content: center; color: #aaa; background: #eee;">
                        <br><br><br>FOTO<br>3x4
                    </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- B. ALAMAT & TEMPAT TINGGAL (DIPERBAIKI) --}}
    <div class="section-title">B. KETERANGAN TEMPAT TINGGAL</div>
    <table class="data-table">
        <tr><td class="label">Alamat Jalan</td><td class="sep">:</td><td class="val">{{ $siswa->alamat_jalan ?? '-' }}</td></tr>
        <tr><td class="label">RT / RW</td><td class="sep">:</td><td class="val">RT. {{ $siswa->rt ?? '-' }} / RW. {{ $siswa->rw ?? '-' }}</td></tr>
        <tr><td class="label">Dusun / Lingkungan</td><td class="sep">:</td><td class="val">{{ $siswa->dusun ?? ($siswa->nama_dusun ?? '-') }}</td></tr>
        <tr><td class="label">Desa / Kelurahan</td><td class="sep">:</td><td class="val">{{ $siswa->desa_kelurahan ?? '-' }}</td></tr>
        <tr><td class="label">Kecamatan</td><td class="sep">:</td><td class="val">{{ $siswa->kecamatan ?? '-' }}</td></tr>
        <tr><td class="label">Kabupaten / Kota</td><td class="sep">:</td><td class="val">{{ $siswa->kabupaten_kota ?? '-' }}</td></tr>
        <tr><td class="label">Provinsi</td><td class="sep">:</td><td class="val">{{ $siswa->provinsi ?? '-' }}</td></tr>
        <tr><td class="label">Kode Pos</td><td class="sep">:</td><td class="val">{{ $siswa->kode_pos ?? '-' }}</td></tr>
        <tr><td colspan="3" style="height: 5px;"></td></tr> {{-- Spacer --}}
        <tr><td class="label">Jenis Tinggal</td><td class="sep">:</td><td class="val">{{ $siswa->jenis_tinggal_id_str ?? '-' }}</td></tr>
        <tr><td class="label">Alat Transportasi</td><td class="sep">:</td><td class="val">{{ $siswa->alat_transportasi_id_str ?? '-' }}</td></tr>
        <tr><td class="label">Jarak ke Sekolah</td><td class="sep">:</td><td class="val">{{ $siswa->jarak_rumah_ke_sekolah_km ? $siswa->jarak_rumah_ke_sekolah_km.' km' : '-' }}</td></tr>
    </table>

    {{-- C. KETERANGAN ORANG TUA / WALI (JUDUL LEBIH TEGAS) --}}
    <div class="section-title">C. KETERANGAN ORANG TUA / WALI</div>
    <table class="data-table">
        <tr>
            {{-- KIRI: AYAH --}}
            <td width="50%" style="padding:0; padding-right: 15px;">
                <div class="sub-header">AYAH KANDUNG</div>
                <table class="data-table" style="margin: 0;">
                    <tr><td style="width: 35%;">Nama</td><td style="width: 5%;">:</td><td>{{ $siswa->nama_ayah ?? '-' }}</td></tr>
                    <tr><td>Tahun Lahir</td><td>:</td><td>{{ $siswa->tahun_lahir_ayah ?? '-' }}</td></tr>
                    <tr><td>Pendidikan</td><td>:</td><td>{{ $siswa->pendidikan_ayah_id_str ?? '-' }}</td></tr>
                    <tr><td>Pekerjaan</td><td>:</td><td>{{ $siswa->pekerjaan_ayah_id_str ?? '-' }}</td></tr>
                    <tr><td>Penghasilan</td><td>:</td><td>{{ $siswa->penghasilan_ayah_id_str ?? '-' }}</td></tr>
                </table>
            </td>
            {{-- KANAN: IBU --}}
            <td width="50%" style="padding:0; padding-left: 15px;">
                <div class="sub-header">IBU KANDUNG</div>
                <table class="data-table" style="margin: 0;">
                    <tr><td style="width: 35%;">Nama</td><td style="width: 5%;">:</td><td>{{ $siswa->nama_ibu ?? '-' }}</td></tr>
                    <tr><td>Tahun Lahir</td><td>:</td><td>{{ $siswa->tahun_lahir_ibu ?? '-' }}</td></tr>
                    <tr><td>Pendidikan</td><td>:</td><td>{{ $siswa->pendidikan_ibu_id_str ?? '-' }}</td></tr>
                    <tr><td>Pekerjaan</td><td>:</td><td>{{ $siswa->pekerjaan_ibu_id_str ?? '-' }}</td></tr>
                    <tr><td>Penghasilan</td><td>:</td><td>{{ $siswa->penghasilan_ibu_id_str ?? '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>
    
    {{-- WALI (Jika Ada) --}}
    @if(!empty($siswa->nama_wali))
    <table class="data-table" style="margin-top: 10px;">
        <tr>
            <td width="100%" style="padding:0;">
                <div class="sub-header">WALI</div>
                <table class="data-table" style="margin: 0;">
                    <tr><td style="width: 17%;">Nama Wali</td><td style="width: 2%;">:</td><td>{{ $siswa->nama_wali }}</td></tr>
                    <tr><td>Tahun Lahir</td><td>:</td><td>{{ $siswa->tahun_lahir_wali ?? '-' }}</td></tr>
                    <tr><td>Pekerjaan</td><td>:</td><td>{{ $siswa->pekerjaan_wali_id_str ?? '-' }}</td></tr>
                    <tr><td>Penghasilan</td><td>:</td><td>{{ $siswa->penghasilan_wali_id_str ?? '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>
    @endif

    {{-- D. PERKEMBANGAN SISWA --}}
    <div class="section-title">D. DATA AKADEMIK</div>
    <table class="data-table">
        <tr><td class="label">Sekolah Asal</td><td class="sep">:</td><td class="val">{{ $siswa->sekolah_asal ?? '-' }} (NPSN: {{ $siswa->npsn_sekolah_asal ?? '-' }})</td></tr>
        <tr><td class="label">No. Ijazah SMP/MTs</td><td class="sep">:</td><td class="val">{{ $siswa->no_seri_ijazah ?? '-' }}</td></tr>
        <tr><td class="label">No. SKHUN</td><td class="sep">:</td><td class="val">{{ $siswa->no_seri_skhun ?? '-' }}</td></tr>
        <tr><td class="label">No. Peserta UN</td><td class="sep">:</td><td class="val">{{ $siswa->no_ujian_nasional ?? '-' }}</td></tr>
        <tr><td class="label">Diterima di Kelas</td><td class="sep">:</td><td class="val">{{ $namaKelas }}</td></tr>
        <tr><td class="label">Tanggal Diterima</td><td class="sep">:</td><td class="val">{{ $siswa->tanggal_masuk_sekolah ? \Carbon\Carbon::parse($siswa->tanggal_masuk_sekolah)->translatedFormat('d F Y') : '-' }}</td></tr>
        <tr><td class="label">Status Siswa</td><td class="sep">:</td><td class="val">{{ $siswa->status ?? 'Aktif' }}</td></tr>
        <tr><td class="label">Penerima Bantuan</td><td class="sep">:</td><td class="val">
            KIP: {{ $siswa->penerima_kip ?? 'Tidak' }} ({{ $siswa->no_kip ?? '-' }}) | 
            PIP: {{ $siswa->layak_pip ?? 'Tidak' }}
        </td></tr>
    </table>

    {{-- TANDA TANGAN --}}
    <div class="signature-wrapper">
        <div class="signature-box">
            <p>{{ $sekolah->kabupaten_kota ?? 'Bandung' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Kepala Sekolah,</p>
            <br><br><br><br>
            {{-- Ganti nama Kepsek dinamis dari table Sekolah jika ada --}}
            <p style="font-weight: bold; text-decoration: underline;">{{ $sekolah->nama_kepala_sekolah ?? '(...........................................)' }}</p>
            <p>NIP. {{ $sekolah->nip_kepala_sekolah ?? '...........................' }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>