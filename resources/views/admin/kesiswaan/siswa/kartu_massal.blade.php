<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Siswa Massal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* --- RESET & BASIC --- */
        * { box-sizing: border-box; }
        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #555;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* --- TOOLBAR --- */
        .toolbar {
            width: 210mm;
            max-width: 95%;
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
            font-size: 14px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-print:hover { background: #5f61e6; }
        .info-text { font-size: 13px; color: #333; line-height: 1.4; }

        /* --- SIMULASI KERTAS A4 --- */
        .a4-page {
            width: 210mm;
            min-height: 297mm;
            background: white;
            margin: 0 auto 30px auto;
            padding: 10mm;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-auto-rows: max-content;
            gap: 10mm;
            justify-content: center;
        }

        /* --- CONTAINER KARTU (UKURAN ID-1) --- */
        .id-card-container {
            width: 54mm;
            height: 86mm;
            background-color: #fff;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            border: 1px solid #eee;
            break-inside: avoid;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            /* Pattern default jika background image sekolah tidak ada */
            background-image: radial-gradient(#696cff 0.5px, transparent 0.5px), radial-gradient(#696cff 0.5px, #fff 0.5px);
            background-size: 15px 15px;
        }

        /* --- HEADER --- */
        .header-section {
            width: 100%;
            padding: 0 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            background: white !important;
            border-bottom: 0.5px solid #eee;
            z-index: 5;
            height: 38px;
            flex-shrink: 0;
        }

        .header-logo { width: 28px; height: 28px; object-fit: contain; }

        .header-text {
            flex: 1;
            font-size: 13px;
            font-weight: 900;
            color: #002b5c;
            text-transform: uppercase;
            line-height: 1;
            text-align: left;
            display: flex;
            align-items: center;
        }

        /* --- CONTENT WRAPPER --- */
        .content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 5px;
            z-index: 2;
            margin-top: -2px;
        }

        /* FOTO */
        .profile-img {
            width: 80px; height: 80px;
            border-radius: 50%; border: 3px solid white;
            object-fit: cover;
            margin-bottom: 8px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2); background: #fff;
        }

        /* NAMA */
        .user-name {
            font-size: 13px; font-weight: 800; color: #ffffff;
            text-transform: uppercase; margin-bottom: 2px; line-height: 1.1;
            text-shadow: 0 1px 3px rgba(0,0,0,0.9);
            text-align: center;
            max-width: 100%; padding: 0 5px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }

        /* NISN */
        .id-text {
            font-size: 10px; color: #ffffff; font-weight: 600; margin-top: 0;
            text-shadow: 0 1px 3px rgba(0,0,0,0.9); opacity: 0.9;
            margin-bottom: 8px;
            text-align: center;
        }

        /* QR CODE BOX */
        .qr-box {
            padding: 3px;
            background: white;
            border-radius: 4px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.3);
            display: block; line-height: 0;
        }

        /* --- PRINT MEDIA QUERY --- */
        @media print {
            body { background: white; padding: 0; display: block; }
            .toolbar { display: none !important; }
            @page { size: A4; margin: 0; }
            .a4-page {
                margin: 0;
                box-shadow: none;
                padding: 10mm;
                page-break-after: always;
                height: auto;
                min-height: auto;
            }
            .id-card-container {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                border: 1px solid #ccc;
            }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <div class="info-text">
            <b>Mode Cetak Kartu Siswa:</b> Gunakan kertas A4, Skala 100%, Margin None/Default.
        </div>
        <button onclick="window.print()" class="btn-print">
            <span>🖨️ Cetak ({{ count($siswas) }} Kartu)</span>
        </button>
    </div>

    <div class="a4-page">
        {{-- Gunakan $siswas jika cetak massal, atau ubah menjadi [$siswa] jika cetak satuan --}}
        @foreach($siswas as $siswa)
        <div class="id-card-container"
            @if(isset($sekolah) && $sekolah->background_kartu)
                style="background-image: url('{{ asset('storage/' . $sekolah->background_kartu) }}'); background-size: cover; background-position: center;"
            @endif
        >

            {{-- HEADER --}}
            <div class="header-section">
                @if(isset($sekolah) && $sekolah->logo)
                    <img src="{{ asset('storage/' . $sekolah->logo) }}" class="header-logo" alt="Logo">
                @else
                    <img src="{{ asset('logo.png') }}" class="header-logo" alt="Logo">
                @endif

                <div class="header-text">KARTU PESERTA DIDIK</div>
            </div>

            {{-- KONTEN UTAMA --}}
            <div class="content-wrapper">

                {{-- FOTO --}}
                @if(!empty($siswa->foto))
                    <img src="{{ asset('storage/' . $siswa->foto) }}" class="profile-img" alt="Foto">
                @else
                    <div class="profile-img" style="display: flex; align-items: center; justify-content: center; background: #e0e0e0; color: #999;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" viewBox="0 0 24 24" style="fill: #ccc;"><path d="M12 2C6.579 2 2 6.579 2 12s4.579 10 10 10 10-4.579 10-10S17.421 2 12 2zm0 5c1.727 0 3 1.272 3 3s-1.273 3-3 3c-1.726 0-3-1.272-3-3s1.274-3 3-3zm-5.106 9.772c.897-1.32 2.393-2.2 4.106-2.2h2c1.714 0 3.209.88 4.106 2.2C15.828 18.14 14.015 19 12 19s-3.828-.86-5.106-2.228z"></path></svg>
                    </div>
                @endif

                {{-- NAMA --}}
                <div class="user-name">{{ $siswa->nama }}</div>

                {{-- NISN --}}
                <div class="id-text">NISN: {{ $siswa->nisn }}</div>

                {{-- QR CODE --}}
                <div class="qr-box">
                    {{-- Menggunakan qr_token seperti di kode awal anda --}}
                    {!! QrCode::size(70)->generate($siswa->qr_token ?? $siswa->nisn) !!}
                </div>

            </div>

        </div>
        @endforeach
    </div>

</body>
</html>
