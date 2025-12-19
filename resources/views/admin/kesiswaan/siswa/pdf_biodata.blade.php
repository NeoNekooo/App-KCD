<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cetak Biodata Siswa</title> 
    
    <style>
        /* Margin Halaman (Atas Kanan Bawah Kiri) - Bawah agak lebar buat Footer */
        @page { margin: 1cm 2cm 1.5cm 2cm; }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.3;
        }

        /* WRAPPER PER SISWA (PENTING BUAT MULTI-PAGE) */
        .page-container {
            display: block;
            width: 100%;
            page-break-after: always; /* Paksa ganti halaman setelah div ini selesai */
            position: relative;
        }
        /* Hapus break di halaman terakhir agar tidak ada halaman kosong */
        .page-container:last-child {
            page-break-after: auto;
        }

        /* HEADER */
        .header-table { width: 100%; border-bottom: 3px solid #000; margin-bottom: 10px; padding-bottom: 5px; }
        .header-table td { vertical-align: middle; text-align: center; }
        .logo { width: 75px; height: auto; object-fit: contain; }
        
        .kop-text { text-align: center; padding: 0 10px; }
        .kop-h1 { font-size: 14px; font-weight: bold; margin: 0; text-transform: uppercase; }
        .kop-h2 { font-size: 16px; font-weight: bold; margin: 2px 0; text-transform: uppercase; }
        .kop-address { font-size: 10px; font-style: italic; margin-top: 2px; }

        /* JUDUL */
        .page-title { text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin-top: 15px; margin-bottom: 5px; text-transform: uppercase; }
        .page-subtitle { text-align: center; font-size: 10px; margin-bottom: 20px; color: #333; }

        /* SECTIONS */
        .section-title { font-size: 11px; font-weight: bold; margin: 10px 0 5px 0; background-color: #e0e0e0; padding: 4px; border-left: 3px solid #000; text-transform: uppercase; }
        .sub-header { font-weight: bold; border-bottom: 1px solid #000; margin-bottom: 5px; padding-bottom: 2px; text-transform: uppercase; font-size: 10px; }

        /* TABEL */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .data-table td { padding: 2px 0; vertical-align: top; }
        
        /* Lebar Kolom Standar */
        .label { width: 30%; font-weight: bold; }
        .sep { width: 3%; text-align: center; }
        .val { width: 67%; }

        /* Lebar Kolom Tabel Belah Dua (Ortu) */
        .label-half { width: 35%; font-weight: bold; }
        .sep-half { width: 5%; text-align: center; }
        .val-half { width: 60%; }

        /* FOTO & TTD */
        .photo-wrapper { text-align: center; vertical-align: top; padding-top: 5px; }
        .photo-container { width: 3cm; height: 4cm; border: 1px solid #000; padding: 2px; object-fit: cover; display: inline-block; }
        
        /* Tanda Tangan Wrapper - Pastikan tidak pecah */
        .signature-wrapper { 
            margin-top: 30px; 
            page-break-inside: avoid; /* Jangan potong tanda tangan ke halaman lain */
            width: 100%;
        }
        .signature-box { text-align: center; width: 250px; float: right; margin-right: 10px; }

        /* Footer Fixed di Bawah Setiap Halaman */
        footer { position: fixed; bottom: 0; left: 0; right: 0; height: 20px; font-size: 8px; text-align: right; border-top: 1px solid #ccc; padding-top: 2px; color: #555; }
    </style>
</head>
<body>
    <footer>
        Dicetak melalui Sistem Informasi Sekolah pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
    </footer>

    {{-- LOOPING UTAMA --}}
    @foreach($siswas as $siswa)
    <div class="page-container">
        
        {{-- LOGIC KELAS --}}
        @php
            $namaKelas = $siswa->nama_rombel; 
            if (empty($namaKelas)) $namaKelas = optional($siswa->rombel)->nama;
            if (empty($namaKelas) && $siswa->peserta_didik_id) {
                $rombelJson = \App\Models\Rombel::where('anggota_rombel', 'like', '%'.$siswa->peserta_didik_id.'%')->first();
                if ($rombelJson) $namaKelas = $rombelJson->nama;
            }
            $namaKelas = $namaKelas ?? 'Belum Masuk Kelas';
        @endphp

        {{-- KOP SURAT (Muncul di setiap halaman siswa) --}}
        <table class="header-table">
            <tr>
                <td style="width: 15%; text-align: left;">
                    @if(isset($sekolah->logo) && file_exists(storage_path('app/public/' . $sekolah->logo)))
                        <img src="{{ public_path('storage/' . $sekolah->logo) }}" class="logo">
                    @else
                        {{-- Placeholder Logo --}}
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

        {{-- A. IDENTITAS --}}
        <div class="section-title">A. KETERANGAN PRIBADI</div>
        <table class="data-table">
            <tr>
                <td style="width: 75%; padding-right: 10px;">
                    <table class="data-table">
                        <tr><td class="label">Nama Lengkap</td><td class="sep">:</td><td class="val"><b>{{ $siswa->nama }}</b></td></tr>
                        <tr><td class="label">NIPD / NISN</td><td class="sep">:</td><td class="val">{{ $siswa->nipd ?? '-' }} / {{ $siswa->nisn ?? '-' }}</td></tr>
                        <tr><td class="label">NIK</td><td class="sep">:</td><td class="val">{{ $siswa->nik ?? '-' }}</td></tr>
                        <tr><td class="label">Tempat, Tgl Lahir</td><td class="sep">:</td><td class="val">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</td></tr>
                        <tr><td class="label">Jenis Kelamin</td><td class="sep">:</td><td class="val">{{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                        <tr><td class="label">Agama</td><td class="sep">:</td><td class="val">{{ $siswa->agama_id_str ?? '-' }}</td></tr>
                        <tr><td class="label">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $siswa->kewarganegaraan ?? 'Indonesia' }}</td></tr>
                        <tr><td class="label">Anak ke</td><td class="sep">:</td><td class="val">{{ $siswa->anak_keberapa ?? '-' }} (dari {{ $siswa->jumlah_saudara_kandung ?? '-' }} bersaudara)</td></tr>
                        <tr><td class="label">Alamat Rumah</td><td class="sep">:</td><td class="val">
                            {{ $siswa->alamat_jalan ?? '-' }} <br>
                            RT {{ $siswa->rt ?? '-' }} / RW {{ $siswa->rw ?? '-' }} <br>
                            {{ $siswa->desa_kelurahan ?? '-' }}, {{ $siswa->kecamatan ?? '-' }} <br>
                            {{ $siswa->kabupaten_kota ?? '-' }} - {{ $siswa->kode_pos ?? '-' }}
                        </td></tr>
                        <tr><td class="label">No. Handphone</td><td class="sep">:</td><td class="val">{{ $siswa->nomor_telepon_seluler ?? '-' }}</td></tr>
                        <tr><td class="label">Email</td><td class="sep">:</td><td class="val">{{ $siswa->email ?? '-' }}</td></tr>
                    </table>
                </td>
                <td class="photo-wrapper" style="width: 25%;">
                    @if($siswa->foto && file_exists(storage_path('app/public/' . $siswa->foto)))
                        <img src="{{ public_path('storage/' . $siswa->foto) }}" class="photo-container">
                    @else
                        <div class="photo-container" style="display: flex; align-items: center; justify-content: center; color: #aaa; background: #eee;">
                            <br><br>FOTO<br>3x4
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        {{-- B. TRANSPORT --}}
        <div class="section-title">B. KETERANGAN TEMPAT TINGGAL & TRANSPORT</div>
        <table class="data-table">
            <tr><td class="label">Jenis Tinggal</td><td class="sep">:</td><td class="val">{{ $siswa->jenis_tinggal_id_str ?? '-' }}</td></tr>
            <tr><td class="label">Alat Transportasi</td><td class="sep">:</td><td class="val">{{ $siswa->alat_transportasi_id_str ?? '-' }}</td></tr>
            <tr><td class="label">Jarak ke Sekolah</td><td class="sep">:</td><td class="val">{{ $siswa->jarak_rumah_ke_sekolah_km ? $siswa->jarak_rumah_ke_sekolah_km.' km' : '-' }}</td></tr>
            <tr><td class="label">Waktu Tempuh</td><td class="sep">:</td><td class="val">{{ $siswa->waktu_tempuh_menit ? $siswa->waktu_tempuh_menit.' menit' : '-' }}</td></tr>
        </table>

        {{-- C. ORTU --}}
        <div class="section-title">C. KETERANGAN ORANG TUA / WALI</div>
        <table class="data-table" style="width: 100%;">
            <tr>
                <td style="width: 48%; padding-right: 2%;">
                    <div class="sub-header">AYAH KANDUNG</div>
                    <table class="data-table" style="width: 100%;">
                        <tr><td class="label-half">Nama</td><td class="sep-half">:</td><td class="val-half">{{ $siswa->nama_ayah ?? '-' }}</td></tr>
                        <tr><td class="label-half">Tahun Lahir</td><td class="sep-half">:</td><td class="val-half">{{ $siswa->tahun_lahir_ayah ?? '-' }}</td></tr>
                        <tr><td class="label-half">Pendidikan</td><td class="sep-half">:</td><td class="val-half">{{ $siswa->pendidikan_ayah_id_str ?? '-' }}</td></tr>
                        <tr><td class="label-half">Pekerjaan</td><td class="sep-half">:</td><td class="val-half">{{ $siswa->pekerjaan_ayah_id_str ?? '-' }}</td></tr>
                        <tr><td class="label-half">Penghasilan</td><td class="sep-half">:</td><td class="val-half">{{ $siswa->penghasilan_ayah_id_str ?? '-' }}</td></tr>
                    </table>
                </td>
                <td style="width: 48%; padding-left: 2%;">
                    <div class="sub-header">IBU KANDUNG</div>
                    <table class="data-table" style="width: 100%;">
                        <tr><td class="label-half">Nama</td><td class="sep-half">:</td><td class="val-half">{{ $siswa->nama_ibu ?? '-' }}</td></tr>
                        <tr><td class="label-half">Tahun Lahir</td><td class="sep-half">:</td><td class="val-half">{{ $siswa->tahun_lahir_ibu ?? '-' }}</td></tr>
                        <tr><td class="label-half">Pendidikan</td><td class="sep-half">:</td><td class="val-half">{{ $siswa->pendidikan_ibu_id_str ?? '-' }}</td></tr>
                        <tr><td class="label-half">Pekerjaan</td><td class="sep-half">:</td><td class="val-half">{{ $siswa->pekerjaan_ibu_id_str ?? '-' }}</td></tr>
                        <tr><td class="label-half">Penghasilan</td><td class="sep-half">:</td><td class="val-half">{{ $siswa->penghasilan_ibu_id_str ?? '-' }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
        
        @if(!empty($siswa->nama_wali))
        <table class="data-table" style="margin-top: 5px;">
            <tr>
                <td style="width: 100%;">
                    <div class="sub-header">WALI</div>
                    <table class="data-table" style="width: 50%;"> 
                        <tr><td class="label-half">Nama Wali</td><td class="sep-half">:</td><td class="val-half">{{ $siswa->nama_wali }}</td></tr>
                        <tr><td class="label-half">Tahun Lahir</td><td class="sep-half">:</td><td class="val-half">{{ $siswa->tahun_lahir_wali ?? '-' }}</td></tr>
                        <tr><td class="label-half">Pekerjaan</td><td class="sep-half">:</td><td class="val-half">{{ $siswa->pekerjaan_wali_id_str ?? '-' }}</td></tr>
                        <tr><td class="label-half">Penghasilan</td><td class="sep-half">:</td><td class="val-half">{{ $siswa->penghasilan_wali_id_str ?? '-' }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
        @endif

        {{-- D. AKADEMIK --}}
        <div class="section-title">D. DATA AKADEMIK</div>
        <table class="data-table">
            <tr><td class="label">Sekolah Asal</td><td class="sep">:</td><td class="val">{{ $siswa->sekolah_asal ?? '-' }} (NPSN: {{ $siswa->npsn_sekolah_asal ?? '-' }})</td></tr>
            <tr><td class="label">No. Ijazah SMP</td><td class="sep">:</td><td class="val">{{ $siswa->no_seri_ijazah ?? '-' }}</td></tr>
            <tr><td class="label">No. SKHUN</td><td class="sep">:</td><td class="val">{{ $siswa->no_seri_skhun ?? '-' }}</td></tr>
            <tr><td class="label">No. Peserta UN</td><td class="sep">:</td><td class="val">{{ $siswa->no_ujian_nasional ?? '-' }}</td></tr>
            <tr><td class="label">Diterima di Kelas</td><td class="sep">:</td><td class="val">{{ $namaKelas }}</td></tr>
            <tr><td class="label">Tanggal Masuk</td><td class="sep">:</td><td class="val">{{ $siswa->tanggal_masuk_sekolah ? \Carbon\Carbon::parse($siswa->tanggal_masuk_sekolah)->translatedFormat('d F Y') : '-' }}</td></tr>
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
                <p style="font-weight: bold; text-decoration: underline;">{{ $sekolah->nama_kepala_sekolah ?? '(...........................................)' }}</p>
                <p>NIP. {{ $sekolah->nip_kepala_sekolah ?? '...........................' }}</p>
            </div>
            <div style="clear: both;"></div>
        </div>

    </div> {{-- END PAGE CONTAINER --}}
    @endforeach
</body>
</html>