<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu ID Massal</title>
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
            border: 1px dashed #ccc;
            break-inside: avoid;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            background-image: white;
            background-size: 15px 15px;
        }

        /* --- HEADER (DIPERBAIKI) --- */
        .header-section {
            width: 100%;
            padding: 0 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            background: white !important;
            border-bottom: 0.5px solid #eee;
            z-index: 5;
            /* Tinggi dikurangi sedikit agar lebih compact */
            height: 38px;
            flex-shrink: 0;
        }

        .header-logo { width: 28px; height: 28px; object-fit: contain; }

        .header-text {
            flex: 1;
            /* Font diperbesar lagi */
            font-size: 13px;
            font-weight: 900;
            color: #002b5c;
            text-transform: uppercase;
            line-height: 1;
            text-align: left;
            /* Agar teks tidak mepet ke atas/bawah */
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
            width: 78px; height: 78px;
            border-radius: 50%; border: 3px solid white;
            object-fit: cover;
            margin-bottom: 6px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2); background: #fff;
        }

        /* NAMA */
        .user-name {
            font-size: 13px; font-weight: 800; color: #ffffff;
            text-transform: uppercase; margin-bottom: 3px; line-height: 1.1;
            text-shadow: 0 1px 3px rgba(0,0,0,0.9);
            text-align: center;
            max-width: 100%; padding: 0 2px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }

        /* NIP / NUPTK */
        .nip-text {
            font-size: 9px; color: #ffffff; font-weight: 600; margin-top: 0;
            text-shadow: 0 1px 3px rgba(0,0,0,0.9); opacity: 0.9;
            margin-bottom: 6px;
            text-align: center;
        }

        /* QR CODE BOX (BORDER DIKURANGI) */
        .qr-box {
            /* Padding dikurangi dari 4px jadi 2px */
            padding: 2px;
            background: white;
            border-radius: 4px; /* Radius dikecilkan dikit biar pas */
            box-shadow: 0 3px 6px rgba(0,0,0,0.3);
            display: block; line-height: 0;
        }

        /* --- PRINT MEDIA QUERY --- */
        @media print {
            body { background: white; padding: 0; display: block; }
            .toolbar { display: none !important; }
            @page { size: A4; margin: 0; }
            .a4-page { margin: 0; box-shadow: none; page-break-after: always; height: auto; min-height: auto; }
            .id-card-container { -webkit-print-color-adjust: exact; print-color-adjust: exact; border: 1px solid #ccc; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <div class="info-text">
            <b>Tips Cetak Terbaik:</b> Size A4, Scale 100%, Margins Default/None.
        </div>
        <button onclick="window.print()" class="btn-print">
            🖨️ Cetak ({{ count($gtks) }} Kartu)
        </button>
    </div>

    <div class="a4-page">
        @foreach($gtks as $gtk)
        <div class="id-card-container"
            @if(isset($sekolah) && $sekolah->background_kartu)
                style="background-image: url('{{ asset('storage/' . $sekolah->background_kartu) }}'); background-size: cover; background-position: center;"
            @endif
        >

            {{-- HEADER --}}
            <div class="header-section">
                @if($sekolah && $sekolah->logo)
                    <img src="{{ asset('storage/' . $sekolah->logo) }}" class="header-logo" alt="Logo">
                @else
                    <img src="https://via.placeholder.com/20" class="header-logo" alt="Logo">
                @endif

                <div class="header-text">
                    @if($gtk->jenis_ptk_id == 91 || str_contains(strtolower($gtk->jenis_ptk_id_str ?? ''), 'kepala sekolah'))
                        KARTU KEPALA SEKOLAH
                    @elseif(str_contains(strtolower($gtk->jenis_ptk_id_str ?? ''), 'guru'))
                        KARTU GURU
                    @else
                        KARTU TENDIK
                    @endif
                </div>
            </div>

            {{-- KONTEN UTAMA --}}
            <div class="content-wrapper">

                {{-- FOTO --}}
                @if(!empty($gtk->foto))
                    <img src="{{ asset('storage/' . $gtk->foto) }}" class="profile-img" alt="Foto">
                @else
                    <div class="profile-img" style="display: flex; align-items: center; justify-content: center; background: #e0e0e0; color: #999;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" style="fill: currentColor;"><path d="M12 2C6.579 2 2 6.579 2 12s4.579 10 10 10 10-4.579 10-10S17.421 2 12 2zm0 5c1.727 0 3 1.272 3 3s-1.273 3-3 3c-1.726 0-3-1.272-3-3s1.274-3 3-3zm-5.106 9.772c.897-1.32 2.393-2.2 4.106-2.2h2c1.714 0 3.209.88 4.106 2.2C15.828 18.14 14.015 19 12 19s-3.828-.86-5.106-2.228z"></path></svg>
                    </div>
                @endif

                {{-- NAMA --}}
                <div class="user-name">{{ $gtk->nama }}</div>

                {{-- ID (NIP/NUPTK/NIK) --}}
                @if(!empty($gtk->nip) && trim($gtk->nip) != '-')
                    <div class="nip-text">NIP: {{ $gtk->nip }}</div>
                @elseif(!empty($gtk->nuptk) && trim($gtk->nuptk) != '-')
                    <div class="nip-text">NUPTK: {{ $gtk->nuptk }}</div>
                @elseif(!empty($gtk->nik) && trim($gtk->nik) != '-')
                    <div class="nip-text">NIK: {{ $gtk->nik }}</div>
                @endif

                {{-- QR CODE --}}
                <div class="qr-box">
                    @php
                        $qrContent = $gtk->nama;
                        if(!empty($gtk->nip) && trim($gtk->nip) != '-') $qrContent .= ' - ' . $gtk->nip;
                        elseif(!empty($gtk->nuptk) && trim($gtk->nuptk) != '-') $qrContent .= ' - ' . $gtk->nuptk;
                        elseif(!empty($gtk->nik) && trim($gtk->nik) != '-') $qrContent .= ' - ' . $gtk->nik;
                    @endphp
                    {!! QrCode::format('svg')->size(75)->generate($qrContent) !!}
                </div>

            </div>

        </div>
        @endforeach
    </div>

</body>
</html>
