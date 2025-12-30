<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Siswa Massal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        /* --- RESET & BASIC --- */
        * { box-sizing: border-box; }
        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #555;
            margin: 0;
            padding: 0;
        }

        /* --- TOOLBAR --- */
        .toolbar {
            width: 210mm;
            margin: 20px auto;
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-print {
            padding: 10px 25px;
            background: #696cff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        /* --- SIMULASI KERTAS A4 --- */
        .a4-sheet {
            width: 210mm;
            min-height: 297mm;
            background: white;
            margin: 0 auto 20px auto;
            padding: 10mm;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            gap: 8mm;
            justify-items: center;
            align-items: center;
            page-break-after: always;
        }

        /* --- CONTAINER KARTU --- */
        .id-card-container {
            width: 54mm;
            height: 86mm;
            background-color: #fff;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            border: 1px solid #eee;
            display: flex;
            flex-direction: column;
            background-image: radial-gradient(#696cff 0.5px, transparent 0.5px), radial-gradient(#696cff 0.5px, #fff 0.5px);
            background-size: 15px 15px;
        }

        .header-section {
            width: 100%;
            padding: 0 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            background: white !important;
            border-bottom: 0.5px solid #eee;
            height: 38px;
            flex-shrink: 0;
        }

        .header-logo { width: 26px; height: 26px; object-fit: contain; }
        .header-text { font-size: 11px; font-weight: 900; color: #002b5c; text-transform: uppercase; line-height: 1.1; }

        .content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 5px;
            text-align: center;
        }

        .profile-img {
            width: 75px; height: 75px;
            border-radius: 50%; border: 3px solid white;
            object-fit: cover;
            margin-bottom: 8px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
            background: #f0f0f0;
        }

        .user-name {
            font-size: 13px; font-weight: 800; color: #ffffff;
            text-transform: uppercase; margin-bottom: 2px; line-height: 1.2;
            text-shadow: 0 1px 4px rgba(0,0,0,0.9);
            padding: 0 5px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }

        .id-text {
            font-size: 10px; color: #ffffff; font-weight: 600;
            text-shadow: 0 1px 3px rgba(0,0,0,0.9); opacity: 0.9;
            margin-bottom: 8px;
        }

        .qr-box {
            padding: 3px; background: white; border-radius: 4px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.3); line-height: 0;
        }

        @media print {
            body { background: white; padding: 0; }
            .toolbar { display: none; }
            .a4-sheet { margin: 0; box-shadow: none; padding: 10mm; }
            .id-card-container { -webkit-print-color-adjust: exact; print-color-adjust: exact; border: 1px solid #ddd; }
        }
    </style>
</head>
<body>

    {{-- LOGIKA DETEKSI DATA --}}
    @php
        // Normalisasi data agar loop tidak error baik satuan maupun massal
        if(isset($siswa) && !isset($siswas)) {
            $dataSiswa = collect([$siswa]);
        } else {
            $dataSiswa = $siswas ?? collect();
        }
        $jumlahSiswa = count($dataSiswa);
    @endphp

    <div class="toolbar">
        <div>
            <h4 style="margin:0">Cetak Kartu Peserta Didik Massal</h4>
            <small>Total: {{ $jumlahSiswa }} Kartu ditemukan</small>
        </div>
        <button onclick="window.print()" class="btn-print">
            <i class='bx bx-printer'></i> Cetak Semua Kartu
        </button>
    </div>

    {{-- CHUNK(9): Membagi data setiap 9 siswa per halaman A4 --}}
    @forelse ($dataSiswa->chunk(9) as $chunk)
        <div class="a4-sheet">
            @foreach ($chunk as $s)
            <div class="id-card-container"
    style="
        @if(isset($sekolah) && $sekolah->background_kartu)
            background-image: url('{{ asset('storage/' . $sekolah->background_kartu) }}') !important;
            background-size: cover !important;
            background-position: center !important;
        @endif
    "
>
    </div>
                <div class="header-section">
                    @if(isset($sekolah) && $sekolah->logo)
                        <img src="{{ asset('storage/' . $sekolah->logo) }}" class="header-logo" alt="Logo">
                    @else
                        <img src="{{ asset('logo.png') }}" class="header-logo" alt="Logo">
                    @endif
                    <div class="header-text">KARTU PESERTA DIDIK</div>
                </div>

                <div class="content-wrapper">
                    {{-- FOTO SISWA --}}
                    @if(!empty($s->foto))
                        <img src="{{ asset('storage/' . $s->foto) }}" class="profile-img" alt="Foto">
                    @else
                        <div class="profile-img" style="display: flex; align-items: center; justify-content: center;">
                            <i class='bx bxs-user' style="font-size: 40px; color: #ccc;"></i>
                        </div>
                    @endif

                    <div class="user-name">{{ $s->nama }}</div>
                    <div class="id-text">NISN: {{ $s->nisn }}</div>

                    {{-- QR CODE --}}
                    <div class="qr-box">
                        {!! QrCode::size(70)->generate($s->qr_token ?? $s->nisn) !!}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @empty
        <div style="text-align: center; background: white; padding: 50px; width: 210mm; margin: 20px auto; border-radius: 8px;">
            <p>Tidak ada data siswa yang tersedia untuk dicetak.</p>
        </div>
    @endforelse

</body>
</html>
