<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Biodata GTK - {{ $gtk->nama }}</title>
    <style>
        @page { margin: 1cm 2cm; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        /* HEADER */
        .header-table { width: 100%; border-bottom: 2px solid #000; margin-bottom: 15px; padding-bottom: 5px; }
        .header-table td { vertical-align: middle; }
        .logo { width: 75px; height: auto; }
        .kop-text { text-align: center; }
        .kop-h1 { font-size: 16px; font-weight: bold; margin: 0; text-transform: uppercase; }
        .kop-h2 { font-size: 14px; font-weight: bold; margin: 2px 0; text-transform: uppercase; }
        .kop-address { font-size: 10px; font-style: italic; }

        /* JUDUL */
        .page-title { text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase; }
        .page-subtitle { text-align: center; font-size: 10px; margin-bottom: 20px; color: #555; }

        /* SECTIONS */
        .section-title { font-size: 12px; font-weight: bold; margin: 15px 0 5px 0; background-color: #f0f0f0; padding: 5px; border-left: 4px solid #333; }

        /* TABEL UMUM */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .data-table td, .data-table th { padding: 4px 6px; vertical-align: top; }

        /* Tabel Polos (Identitas) */
        .table-clean td { border: none; }
        .label { width: 28%; font-weight: bold; color: #444; }
        .sep { width: 2%; text-align: center; }
        .val { width: 70%; }

        /* Tabel Bergaris (Riwayat & Lampiran) */
        .table-bordered th, .table-bordered td { border: 1px solid #999; }
        .table-bordered th { background-color: #e9e9e9; text-align: center; font-weight: bold; font-size: 10px; }
        .table-bordered td { font-size: 10px; }
        .table-footer td { font-weight: bold; background-color: #f9f9f9; }

        /* FOTO */
        .photo-wrapper { width: 100%; text-align: right; position: absolute; top: 130px; right: 0; }
        .photo-container {
            width: 3cm; height: 4cm;
            border: 1px solid #ddd; padding: 3px; object-fit: cover;
            display: inline-block;
        }

        /* TANDA TANGAN */
        .signature-wrapper { margin-top: 30px; page-break-inside: avoid; }
        .signature-box { text-align: center; width: 250px; float: right; }

        /* UTILS */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .page-break { page-break-after: always; }
        .text-muted { color: #777; font-style: italic; }

        footer { position: fixed; bottom: 0; left: 0; right: 0; height: 30px; font-size: 9px; text-align: right; border-top: 1px solid #ddd; padding-top: 5px; color: #888; }
    </style>
</head>
<body>
    <footer>
        Dicetak melalui Sistem Informasi Sekolah pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} | Hal <span class="page-number"></span>
    </footer>

    {{-- KOP SURAT --}}
    <table class="header-table">
        <tr>
            <td style="width: 15%;">
                @if($sekolah && $sekolah->logo)
                    <img src="{{ public_path('storage/' . $sekolah->logo) }}" class="logo">
                @endif
            </td>
            <td class="kop-text">
                <div class="kop-h1">PEMERINTAH PROVINSI JAWA BARAT</div>
                <div class="kop-h1">DINAS PENDIDIKAN</div>
                <div class="kop-h2">{{ strtoupper($sekolah->nama ?? 'NAMA SEKOLAH') }}</div>
                <div class="kop-address">
                    {{ $sekolah->alamat_jalan ?? 'Alamat Sekolah' }}
                    @if($sekolah->nomor_telepon) | Telp: {{ $sekolah->nomor_telepon }} @endif
                    @if($sekolah->email) | Email: {{ $sekolah->email }} @endif
                </div>
            </td>
            <td style="width: 15%;"></td>
        </tr>
    </table>

    <div class="page-title">BIODATA PENDIDIK DAN TENAGA KEPENDIDIKAN</div>
    <div class="page-subtitle">Laporan data per tanggal: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>

   <div class="section-title">A. IDENTITAS DIRI</div>
<table class="data-table table-clean" style="width: 100%;">
    <tr>
        {{-- KOLOM DATA (KIRI) --}}
        <td style="width: 70%; padding: 0; vertical-align: top;">
            <table class="data-table table-clean" style="width: 100%; margin: 0;">
                <tr><td class="label" style="width: 35%;">Nama Lengkap</td><td class="sep">:</td><td class="val bold">{{ $gtk->nama }}</td></tr>
                <tr><td class="label">NIK</td><td class="sep">:</td><td class="val">{{ $gtk->nik ?? '-' }}</td></tr>
                <tr><td class="label">NUPTK</td><td class="sep">:</td><td class="val">{{ $gtk->nuptk ?? '-' }}</td></tr>
                <tr><td class="label">Tempat, Tanggal Lahir</td><td class="sep">:</td><td class="val">{{ $gtk->tempat_lahir }}, {{ $gtk->tanggal_lahir ? \Carbon\Carbon::parse($gtk->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</td></tr>
                <tr><td class="label">Jenis Kelamin</td><td class="sep">:</td><td class="val">{{ $gtk->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                <tr><td class="label">Agama</td><td class="sep">:</td><td class="val">{{ $gtk->agama_id_str ?? '-' }}</td></tr>
                <tr><td class="label">Nama Ibu Kandung</td><td class="sep">:</td><td class="val">{{ $gtk->nama_ibu_kandung ?? '-' }}</td></tr>
                <tr><td class="label">Status Perkawinan</td><td class="sep">:</td><td class="val">{{ $gtk->status_perkawinan ?? '-' }}</td></tr>
                <tr><td class="label">Nama Pasangan</td><td class="sep">:</td><td class="val">{{ $gtk->nama_suami_istri ?? '-' }}</td></tr>
            </table>
        </td>

        {{-- KOLOM FOTO (KANAN) --}}
        <td style="width: 30%; text-align: center; vertical-align: top; padding-top: 5px;">
            <div style="border: 1px solid #ccc; padding: 5px; display: inline-block;">
                @if($gtk->foto && file_exists(storage_path('app/public/' . $gtk->foto)))
                    <img src="{{ public_path('storage/' . $gtk->foto) }}" style="width: 3cm; height: 4cm; object-fit: cover;">
                @else
                    <div style="width: 3cm; height: 4cm; background: #eee; display: flex; align-items: center; justify-content: center; color: #aaa; border: 1px dashed #ccc;">
                        <span style="font-size: 10px;">FOTO</span>
                    </div>
                @endif
            </div>
        </td>
    </tr>
</table>

    {{-- B. ALAMAT & KONTAK --}}
    <div class="section-title">B. ALAMAT & KONTAK</div>
    <table class="data-table table-clean">
        <tr><td class="label">Alamat Rumah</td><td class="sep">:</td><td class="val">{{ $gtk->alamat_jalan ?? '-' }}</td></tr>
        <tr><td class="label">RT / RW</td><td class="sep">:</td><td class="val">{{ $gtk->rt ?? '-' }} / {{ $gtk->rw ?? '-' }}</td></tr>
        <tr><td class="label">Desa/Kelurahan</td><td class="sep">:</td><td class="val">{{ $gtk->desa_kelurahan ?? '-' }}</td></tr>
        <tr><td class="label">Kecamatan</td><td class="sep">:</td><td class="val">{{ $gtk->kecamatan ?? '-' }}</td></tr>
        <tr><td class="label">Nomor HP</td><td class="sep">:</td><td class="val">{{ $gtk->no_hp ?? '-' }}</td></tr>
        <tr><td class="label">Email</td><td class="sep">:</td><td class="val">{{ $gtk->email ?? '-' }}</td></tr>
    </table>

    {{-- C. KEPEGAWAIAN (DENGAN LOGIKA SK OTOMATIS) --}}
    <div class="section-title">C. DATA KEPEGAWAIAN</div>

    @php
        // --- LOGIKA PENGAMBILAN DATA SK (Priority: Utama -> Riwayat Pangkat) ---
        $skPdf = $gtk->sk_pengangkatan;
        $tmtPdf = $gtk->tmt_pengangkatan;

        // Jika data utama kosong, cek riwayat kepangkatan
        if (empty($skPdf)) {
            $hist = json_decode($gtk->rwy_kepangkatan, true);
            if (!empty($hist) && isset($hist[0])) {
                $skPdf = $hist[0]['nomor_sk'] ?? '-';
                $tmtPdf = $hist[0]['tmt_pangkat'] ?? null;
            }
        }
    @endphp

    <table class="data-table table-clean">
        <tr>
            <td width="50%" style="padding:0">
                <table class="data-table table-clean">
                    <tr><td class="label">Status Kepegawaian</td><td class="sep">:</td><td class="val">{{ $gtk->status_kepegawaian_id_str ?? '-' }}</td></tr>
                    <tr><td class="label">NIP</td><td class="sep">:</td><td class="val">{{ $gtk->nip ?? '-' }}</td></tr>
                    <tr><td class="label">NIY / NIGK</td><td class="sep">:</td><td class="val">{{ $gtk->niy_nigk ?? '-' }}</td></tr>
                    <tr><td class="label">Jenis PTK</td><td class="sep">:</td><td class="val">{{ $gtk->jenis_ptk_id_str ?? '-' }}</td></tr>
                </table>
            </td>
            <td width="50%" style="padding:0">
                <table class="data-table table-clean">
                    {{-- Menggunakan Variabel $skPdf dan $tmtPdf yang sudah diproses --}}
                    <tr><td class="label">SK Pengangkatan</td><td class="sep">:</td><td class="val">{{ $skPdf ?: '-' }}</td></tr>
                    <tr><td class="label">TMT Pengangkatan</td><td class="sep">:</td><td class="val">{{ $tmtPdf ? \Carbon\Carbon::parse($tmtPdf)->translatedFormat('d F Y') : '-' }}</td></tr>
                    <tr><td class="label">Lembaga Pengangkat</td><td class="sep">:</td><td class="val">{{ $gtk->lembaga_pengangkat ?? '-' }}</td></tr>
                    <tr><td class="label">Sumber Gaji</td><td class="sep">:</td><td class="val">{{ $gtk->sumber_gaji ?? '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- D. RIWAYAT KEPANGKATAN --}}
    <div class="section-title">D. RIWAYAT KEPANGKATAN</div>
    <table class="data-table table-bordered">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Pangkat / Golongan</th>
                <th>Nomor SK</th>
                <th>Tanggal SK</th>
                <th>TMT Pangkat</th>
                <th>Masa Kerja</th>
            </tr>
        </thead>
        <tbody>
            @php $pangkat = json_decode($gtk->rwy_kepangkatan); @endphp
            @if(!empty($pangkat) && is_array($pangkat))
                @foreach($pangkat as $index => $rw)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $rw->pangkat_golongan_id_str ?? '-' }}</td>
                    <td>{{ $rw->nomor_sk ?? '-' }}</td>
                    <td class="text-center">{{ isset($rw->tanggal_sk) ? \Carbon\Carbon::parse($rw->tanggal_sk)->format('d-m-Y') : '-' }}</td>
                    <td class="text-center">{{ isset($rw->tmt_pangkat) ? \Carbon\Carbon::parse($rw->tmt_pangkat)->format('d-m-Y') : '-' }}</td>
                    <td class="text-center">{{ $rw->masa_kerja_gol_tahun ?? 0 }} Thn {{ $rw->masa_kerja_gol_bulan ?? 0 }} Bln</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="6" class="text-center text-muted">Tidak ada data riwayat kepangkatan.</td></tr>
            @endif
        </tbody>
    </table>

    {{-- E. RIWAYAT PENDIDIKAN --}}
    <div class="section-title">E. RIWAYAT PENDIDIKAN FORMAL</div>
    <table class="data-table table-bordered">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Jenjang</th>
                <th>Nama Satuan Pendidikan</th>
                <th>Tahun Masuk</th>
                <th>Tahun Lulus</th>
                <th>IPK</th>
            </tr>
        </thead>
        <tbody>
            @php $pendidikan = json_decode($gtk->rwy_pend_formal); @endphp
            @if(!empty($pendidikan) && is_array($pendidikan))
                @foreach($pendidikan as $index => $rw)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $rw->jenjang_pendidikan_id_str ?? '-' }}</td>
                    <td>{{ $rw->satuan_pendidikan_formal ?? '-' }}</td>
                    <td class="text-center">{{ $rw->tahun_masuk ?? '-' }}</td>
                    <td class="text-center">{{ $rw->tahun_lulus ?? '-' }}</td>
                    <td class="text-center">{{ $rw->ipk ?? '-' }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="6" class="text-center text-muted">Tidak ada data riwayat pendidikan.</td></tr>
            @endif
        </tbody>
    </table>

    {{-- F. KOMPETENSI --}}
    <div class="section-title">F. KOMPETENSI & KEAHLIAN</div>
    <table class="data-table table-clean">
        <tr>
            <td width="50%">
                Lisensi Kepala Sekolah : {{ $gtk->lisensi_kepsek == 1 ? 'Ya' : 'Tidak' }} <br>
                Nomor Registrasi (NUKS) : {{ $gtk->nuks ?? '-' }} <br>
                Keahlian Laboratorium : {{ $gtk->keahlian_laboratorium ?? '-' }}
            </td>
            <td width="50%">
                Mampu Menangani Keb. Khusus : {{ $gtk->mampu_menangani_kebutuhan_khusus ?? '-' }} <br>
                Keahlian Braille : {{ $gtk->keahlian_braille == 1 ? 'Ya' : 'Tidak' }} <br>
                Bahasa Isyarat : {{ $gtk->keahlian_bahasa_isyarat == 1 ? 'Ya' : 'Tidak' }}
            </td>
        </tr>
    </table>

    {{-- ================================================================= --}}
    {{-- HALAMAN 2: LAMPIRAN PEMBELAJARAN (ROMBEL) --}}
    {{-- ================================================================= --}}

    <div class="page-break"></div>

    <div class="page-title" style="margin-top: 20px;">LAMPIRAN REKAPITULASI PEMBELAJARAN</div>
    <div class="page-subtitle">{{ strtoupper($gtk->nama) }}</div>

    <table class="data-table table-bordered" style="margin-top: 15px;">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="40%" class="text-left">Informasi Rombongan Belajar</th>
                <th class="text-left">Mata Pelajaran</th>
                <th width="15%">Jml Jam/Minggu</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalJam = 0;
                $no = 1;
                $adaJadwal = false;
            @endphp

          @forelse ($rombelMengajar as $rombel)
    @php
        // Pastikan pembelajaran adalah array
        $pembelajaran = is_array($rombel->pembelajaran) ? $rombel->pembelajaran : json_decode($rombel->pembelajaran, true);
    @endphp

    @if ($pembelajaran)
        @foreach ($pembelajaran as $mapel)
            {{-- Bandingkan ptk_id dengan memaksanya menjadi string agar identik --}}
            @if (isset($mapel['ptk_id']) && (string)$mapel['ptk_id'] === (string)$gtk->ptk_id)
                @php
                    $jam = (int) ($mapel['jam_mengajar_per_minggu'] ?? 0);
                    $totalJam += $jam;
                    $adaJadwal = true;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>
                        <strong>{{ $rombel->nama_rombel ?? $rombel->nama }}</strong> <br>
                        <span class="text-muted" style="font-size: 9px;">
                            {{ $rombel->jenis_rombel_str ?? 'Kelas' }} - Tk. {{ $rombel->tingkat_pendidikan_id ?? '-' }} <br>
                            {{ $rombel->kurikulum_id_str ?? '-' }}
                        </span>
                    </td>
                    <td>{{ $mapel['nama_mata_pelajaran'] ?? '-' }}</td>
                    <td class="text-center">{{ $jam }}</td>
                </tr>
            @endif
        @endforeach
    @endif
@empty
@endforelse

            @if(!$adaJadwal)
                <tr><td colspan="4" class="text-center text-muted" style="padding: 10px;">Tidak ada data jam mengajar yang ditemukan.</td></tr>
            @endif
        </tbody>
        <tfoot class="table-footer">
            <tr>
                <td colspan="3" class="text-right">Total Jam Mengajar</td>
                <td class="text-center">{{ $totalJam }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- TANDA TANGAN (Di Halaman Terakhir) --}}
    <div class="signature-wrapper">
        <div class="signature-box">
            <p>Cianjur, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Yang Bersangkutan,</p>

            <div style="height: 60px; margin: 10px auto;">
                @if($gtk->tandatangan && file_exists(storage_path('app/public/' . $gtk->tandatangan)))
                    <img src="{{ public_path('storage/' . $gtk->tandatangan) }}" style="max-height: 60px; max-width: 150px;">
                @endif
            </div>

            <p style="font-weight: bold; text-decoration: underline;">{{ strtoupper($gtk->nama) }}</p>
            <p>NIP/NIY. {{ $gtk->nip ?? $gtk->niy_nigk ?? '-' }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    {{-- QR VALIDATION --}}
    <div style="position: absolute; bottom: 40px; left: 0;">
        <img src="data:image/svg+xml;base64, {!! base64_encode(QrCode::format('svg')->size(70)->generate('Validasi Data: ' . $gtk->nama . ' - ' . $gtk->nik)) !!} ">
        <div style="font-size: 8px; margin-top: 5px;">Dokumen ini digenerate otomatis.</div>
    </div>

</body>
</html>
