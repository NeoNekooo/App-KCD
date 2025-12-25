<!DOCTYPE html>
<html>
<head>
    <title>Cetak Jadwal Pelajaran Full Color</title>

    {{-- LOGIKA DINAMIS UKURAN FONT --}}
    @php
        $jmlKelas = $rombels->count();

        // Default
        $fsMatrix = '9px';
        $padMatrix = '2px';
        $widthWaktu = '75px';

        if ($jmlKelas > 10 && $jmlKelas <= 20) {
            $fsMatrix = '7px';
            $padMatrix = '1px';
            $widthWaktu = '65px';
        }
        elseif ($jmlKelas > 20 && $jmlKelas <= 30) {
            $fsMatrix = '6px';
            $padMatrix = '1px';
            $widthWaktu = '55px';
        }
        elseif ($jmlKelas > 30) {
            $fsMatrix = '5px';
            $padMatrix = '0px';
            $widthWaktu = '45px';
        }
    @endphp

    <style>
        body { font-family: sans-serif; font-size: 11px; color: #000; }
        .page-break { page-break-after: always; }

        /* KOP SURAT */
        .kop-surat { width: 100%; border-bottom: 3px double #000; margin-bottom: 10px; padding-bottom: 5px; }
        .nama-sekolah { font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .alamat-sekolah { font-size: 9px; margin: 1px 0; }

        /* === PALET WARNA-WARNI (VIBRANT PASTEL) === */
        /* Warna background untuk MAPEL */
        .clr-0 { background-color: #FFCDD2; } /* Merah Muda */
        .clr-1 { background-color: #FFE0B2; } /* Orange */
        .clr-2 { background-color: #FFF9C4; } /* Kuning */
        .clr-3 { background-color: #C8E6C9; } /* Hijau */
        .clr-4 { background-color: #BBDEFB; } /* Biru */
        .clr-5 { background-color: #E1BEE7; } /* Ungu */
        .clr-6 { background-color: #B2DFDB; } /* Teal */
        .clr-7 { background-color: #F8BBD0; } /* Pink */

        /* Warna Header HARI (Rainbow Header) */
        .hdr-Senin  { background-color: #EF5350; color: white; } /* Merah */
        .hdr-Selasa { background-color: #FF7043; color: white; } /* Oren */
        .hdr-Rabu   { background-color: #FDD835; color: black; } /* Kuning Emas */
        .hdr-Kamis  { background-color: #66BB6A; color: white; } /* Hijau */
        .hdr-Jumat  { background-color: #42A5F5; color: white; } /* Biru */
        .hdr-Sabtu  { background-color: #AB47BC; color: white; } /* Ungu */
        .hdr-Minggu { background-color: #8D6E63; color: white; } /* Coklat */

        /* Cell Istirahat (Gelap Tegas) */
        .cell-merged {
            background-color: #546E7A; /* Blue Grey Gelap */
            color: #fff;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            border: 1px solid #333;
        }

        /* MATRIX GABUNGAN */
        .table-matrix {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        .table-matrix th, .table-matrix td {
            border: 1px solid #000;
            padding: {{ $padMatrix }};
            font-size: {{ $fsMatrix }};
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: clip;
        }

        /* Header Kolom Matrix (Nama Kelas) */
        .table-matrix th {
            background-color: #37474F; /* Dark Grey */
            color: #fff;
            font-weight: bold;
            height: 20px;
        }

        /* Baris Judul Hari (Di Matrix) */
        .row-hari {
            font-weight: bold;
            text-align: left;
            padding-left: 10px;
            font-size: {{ $fsMatrix }};
            border: 1px solid #000;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* PER KELAS */
        .info-kelas { margin-bottom: 8px; width: 100%; font-weight: bold; font-size: 12px; }
        .jadwal-per-kelas { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .jadwal-per-kelas th, .jadwal-per-kelas td { border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle; }

        .mapel-cell { font-size: 10px; line-height: 1.2; font-weight: bold; color: #333; }
        .kode-guru { font-size: 9px; margin-top: 2px; color: #000; background: rgba(255,255,255,0.5); padding: 0 2px; border-radius: 3px; display: inline-block;}
    </style>
</head>
<body>

    {{-- ================================================================= --}}
    {{-- BAGIAN 1: REKAP GABUNGAN (MATRIX JADWAL - COLORFUL) --}}
    {{-- ================================================================= --}}

    <div>
        <table class="kop-surat">
            <tr>
                <td width="15%" align="center">
                    @if($sekolah && $sekolah->logo)
                        <img src="{{ public_path('storage/' . $sekolah->logo) }}" width="60px" style="height: auto;">
                    @endif
                </td>
                <td align="center">
                    <h2 class="nama-sekolah">{{ $sekolah->nama ?? 'NAMA SEKOLAH' }}</h2>
                    <div class="alamat-sekolah">
                        {{ $sekolah->alamat ?? '' }}
                        @if(!empty($sekolah->telepon)) | Telp: {{ $sekolah->telepon }} @endif
                    </div>
                    <div style="margin-top: 5px; font-weight: bold; text-decoration: underline;">REKAPITULASI JADWAL PELAJARAN</div>
                    <div style="font-size: 10px;">TA: {{ $tapelAktif->tahun_ajaran }} ({{ ucfirst($tapelAktif->semester) }})</div>
                </td>
                <td width="15%"></td>
            </tr>
        </table>

        <table class="table-matrix">
            <thead>
                <tr>
                    <th style="width: {{ $widthWaktu }}; background-color: #263238;">WAKTU</th>
                    @foreach($rombels as $rombel)
                        {{-- Header Nama Kelas (Selang seling warna dikit biar ga bosen) --}}
                        @php $bgHeader = ($loop->iteration % 2 == 0) ? '#455A64' : '#546E7A'; @endphp
                        <th style="background-color: {{ $bgHeader }};" title="{{ $rombel->nama }}">
                            @if($jmlKelas > 30)
                                {{ substr(str_replace(['Kelas ', 'X ', 'XI ', 'XII '], ['', 'X', 'XI', 'XII'], $rombel->nama), 0, 10) }}
                            @else
                                {{ $rombel->nama }}
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($days as $hari)
                    {{-- JUDUL HARI (DENGAN WARNA KHAS HARI) --}}
                    <tr>
                        <td colspan="{{ $rombels->count() + 1 }}" class="row-hari hdr-{{ $hari }}">
                            {{ strtoupper($hari) }}
                        </td>
                    </tr>

                    @php $dailyJams = $allMasterJams[$hari] ?? collect(); @endphp

                    @foreach($dailyJams as $jam)
                        <tr>
                            {{-- Cell Waktu --}}
                            <td style="background-color: #ECEFF1; font-weight: bold;">
                                {{ \Carbon\Carbon::parse($jam->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jam->jam_selesai)->format('H:i') }}
                            </td>

                            {{-- LOGIKA MERGE --}}
                            @if($jam->tipe != 'kbm')
                                <td colspan="{{ $rombels->count() }}" class="cell-merged">
                                    {{ strtoupper($jam->nama) }}
                                </td>
                            @else
                                {{-- LOOP KELAS --}}
                                @foreach($rombels as $rombel)
                                    @php
                                        $data = $jadwalGrouped[$rombel->id][$hari][$jam->urutan] ?? null;
                                        $content = '';
                                        $bgClass = 'background-color: #fff;';

                                        if ($data && $data->pembelajaran) {
                                            $namaMapel = $data->pembelajaran->nama_mata_pelajaran;
                                            $singkatan = $controller->helperSingkatan($namaMapel);
                                            $kodeGuru = $data->pembelajaran->guru->id ?? '?';
                                            $content = $singkatan . " (" . $kodeGuru . ")";

                                            // WARNA-WARNI RANDOM BERDASARKAN NAMA MAPEL
                                            $hash = crc32($namaMapel);
                                            $idx = abs($hash) % 8; // 0-7
                                            // Kita inject class lewat style karena class blade loop kadang tricky
                                            // atau pake class clr-X
                                            $bgClass = ''; // Reset inline style
                                        } else {
                                            $idx = -1; // Kosong
                                        }
                                    @endphp

                                    <td class="{{ $idx >= 0 ? 'clr-'.$idx : '' }}" style="{{ $idx < 0 ? 'background-color:#fff;' : '' }}">
                                        {{ $content }}
                                    </td>
                                @endforeach
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>


    {{-- ================================================================= --}}
    {{-- BAGIAN 2: JADWAL DETAIL PER KELAS (COLORFUL) --}}
    {{-- ================================================================= --}}

    @foreach($rombels as $index => $rombel)
        <div class="{{ $loop->last ? '' : 'page-break' }}">

            <table class="kop-surat">
                <tr>
                    <td width="15%" align="center">
                        @if($sekolah && $sekolah->logo)
                            <img src="{{ public_path('storage/' . $sekolah->logo) }}" width="60px" style="height: auto;">
                        @endif
                    </td>
                    <td align="center">
                        <h2 class="nama-sekolah">{{ $sekolah->nama ?? 'NAMA SEKOLAH' }}</h2>
                        <div class="alamat-sekolah">
                            {{ $sekolah->alamat ?? '' }}
                            @if(!empty($sekolah->telepon)) | Telp: {{ $sekolah->telepon }} @endif
                        </div>
                        <div style="margin-top: 5px; font-weight: bold; text-decoration: underline;">JADWAL PELAJARAN KELAS</div>
                    </td>
                    <td width="15%"></td>
                </tr>
            </table>

            <table class="info-kelas">
                <tr>
                    <td>TA: {{ $tapelAktif->tahun_ajaran }} ({{ ucfirst($tapelAktif->semester) }})</td>
                    <td align="right">Kelas: {{ $rombel->nama }} | Wali: {{ $rombel->waliKelas->nama ?? '-' }}</td>
                </tr>
            </table>

            <table class="jadwal-per-kelas">
                <thead>
                    <tr>
                        <th width="15%" style="background-color: #37474F; color: white;">WAKTU</th>
                        {{-- Header Hari Warna-Warni --}}
                        @foreach($days as $hari)
                            <th width="17%" class="hdr-{{ $hari }}">{{ strtoupper($hari) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php $masterRow = $allMasterJams['Senin'] ?? collect(); @endphp

                    @foreach($masterRow as $jamRef)
                    <tr>
                        {{-- Waktu --}}
                        <td style="font-size: 9px; white-space: nowrap; font-weight: bold; background-color: #ECEFF1;">
                            {{ \Carbon\Carbon::parse($jamRef->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jamRef->jam_selesai)->format('H:i') }}
                        </td>

                        @foreach($days as $hari)
                            @php
                                $jamDataHariIni = $allMasterJams[$hari]->where('urutan', $jamRef->urutan)->first();
                                $content = ''; $style = ''; $cellClass = '';

                                if($jamDataHariIni) {
                                    if($jamDataHariIni->tipe != 'kbm') {
                                        // ISTIRAHAT (GELAP)
                                        $content = strtoupper($jamDataHariIni->nama);
                                        $cellClass = 'cell-merged';
                                    } else {
                                        // MAPEL (WARNA WARNI)
                                        $d = $jadwalGrouped[$rombel->id][$hari][$jamRef->urutan] ?? null;
                                        if($d && $d->pembelajaran) {
                                            $mapel = $controller->helperSingkatan($d->pembelajaran->nama_mata_pelajaran);
                                            $guru = $d->pembelajaran->guru->id ?? '?';
                                            $content = "<div class='mapel-cell'>{$mapel}</div><div class='kode-guru'>{$guru}</div>";

                                            $hash = crc32($d->pembelajaran->nama_mata_pelajaran);
                                            $idx = abs($hash) % 8;
                                            $cellClass = 'clr-' . $idx;
                                        } else {
                                            $cellClass = ''; // Putih Kosong
                                        }
                                    }
                                } else {
                                    $content = '-';
                                    $style = 'background-color: #eee;'; // Tidak ada jam
                                }
                            @endphp

                            <td class="{{ $cellClass }}" style="{{ $style }}">{!! $content !!}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="text-align: right; margin-top: 5px; font-size: 9px; font-style: italic;">
                Dicetak: {{ date('d-m-Y H:i') }}
            </div>
        </div>
    @endforeach

    {{-- ================================================================= --}}
    {{-- BAGIAN 3: DAFTAR GURU (TERANG & BERKOP & HEADER BERWARNA) --}}
    {{-- ================================================================= --}}

    @if($listGuru->isNotEmpty())
        <div class="page-break"></div>

        <table class="kop-surat">
            <tr>
                <td width="15%" align="center">
                    @if($sekolah && $sekolah->logo)
                        <img src="{{ public_path('storage/' . $sekolah->logo) }}" width="60px" style="height: auto;">
                    @endif
                </td>
                <td align="center">
                    <h2 class="nama-sekolah">{{ $sekolah->nama ?? 'NAMA SEKOLAH' }}</h2>
                    <div class="alamat-sekolah">
                        {{ $sekolah->alamat ?? '' }}
                        @if(!empty($sekolah->telepon)) | Telp: {{ $sekolah->telepon }} @endif
                    </div>
                    <div style="margin-top: 5px; font-weight: bold; text-decoration: underline;">DAFTAR KODE GURU</div>
                    <div style="font-size: 10px;">TA: {{ $tapelAktif->tahun_ajaran }}</div>
                </td>
                <td width="15%"></td>
            </tr>
        </table>

        <table style="width: 100%; font-size: 10px; border: none; margin-top: 10px; border-collapse: collapse;">
             <thead>
                {{-- Header Tabel Guru Biru Gelap Elegant --}}
                <tr style="background:#1565C0; color:#fff;">
                    <th style="padding:8px; border:1px solid #000;">Kode</th>
                    <th style="padding:8px; border:1px solid #000;">Nama Guru</th>
                    <th style="border:none; width:20px; background:#fff;"></th>
                    <th style="padding:8px; border:1px solid #000;">Kode</th>
                    <th style="padding:8px; border:1px solid #000;">Nama Guru</th>
                    <th style="border:none; width:20px; background:#fff;"></th>
                    <th style="padding:8px; border:1px solid #000;">Kode</th>
                    <th style="padding:8px; border:1px solid #000;">Nama Guru</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $chunks = $listGuru->chunk(ceil($listGuru->count() / 3));
                    $col1 = $chunks->get(0) ?? collect();
                    $col2 = $chunks->get(1) ?? collect();
                    $col3 = $chunks->get(2) ?? collect();
                    $maxRows = max($col1->count(), $col2->count(), $col3->count());
                @endphp

                @for($i = 0; $i < $maxRows; $i++)
                    {{-- Zebra Striping untuk baris guru --}}
                    @php $bgRow = ($i % 2 == 0) ? '#fff' : '#E3F2FD'; @endphp
                    <tr style="background-color: {{ $bgRow }};">
                        <td style="border:1px solid #ccc; font-weight:bold; text-align:center; padding: 4px;">{{ $col1[$i]->id ?? '' }}</td>
                        <td style="border:1px solid #ccc; padding: 4px;">{{ $col1[$i]->nama ?? '' }}</td>
                        <td style="border:none; background:#fff;"></td>

                        @php $idx2 = $i + $col1->count(); @endphp
                        <td style="border:1px solid #ccc; font-weight:bold; text-align:center; padding: 4px;">{{ $col2[$idx2]->id ?? '' }}</td>
                        <td style="border:1px solid #ccc; padding: 4px;">{{ $col2[$idx2]->nama ?? '' }}</td>
                        <td style="border:none; background:#fff;"></td>

                        @php $idx3 = $idx2 + $col2->count(); @endphp
                        <td style="border:1px solid #ccc; font-weight:bold; text-align:center; padding: 4px;">{{ $col3[$idx3]->id ?? '' }}</td>
                        <td style="border:1px solid #ccc; padding: 4px;">{{ $col3[$idx3]->nama ?? '' }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    @endif

</body>
</html>
