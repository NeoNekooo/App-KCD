<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu ID</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        /* --- CONTAINER KARTU --- */
        .id-card-container {
            width: 54mm;
            height: 85.6mm;
            background-color: #fff;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            border: 1px solid #ccc;
            box-sizing: border-box;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;

            /* Background Default */
            background-image: radial-gradient(#696cff 0.5px, transparent 0.5px), radial-gradient(#696cff 0.5px, #fff 0.5px);
            background-size: 15px 15px;
        }

        /* --- HEADER SECTION (SUPER PRESS) --- */
        .header-section {
            width: 100%;
            /* Padding sangat tipis */
            padding: 0 10px; 
            display: flex;
            align-items: center; 
            gap: 8px; 
            
            background: white !important; 
            border-bottom: 0.5px solid #eee; 
            z-index: 5; 
            /* Tinggi dipaksa pendek */
            height: 35px; 
        }

        .header-logo {
            width: 22px; /* Logo dikecilkan menyesuaikan tinggi header */
            height: 22px;
            object-fit: contain;
        }

        .header-text {
            flex: 1;
            font-size: 10px; /* Ukuran text tetap terbaca */
            font-weight: 900; 
            color: #002b5c; 
            text-transform: uppercase;
            line-height: 1;
            text-align: left;
            margin-top: 1px;
        }

        /* --- BODY KARTU --- */
        .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center; 
            padding: 0 10px;
            text-align: center;
            z-index: 2;
        }

        /* Foto Profil */
        .profile-img {
            width: 85px;  
            height: 85px; 
            border-radius: 50%;
            border: none; /* Tanpa Border */
            object-fit: cover;
            margin-bottom: 8px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.3);
        }

        /* Nama (UKURAN DIKECILKAN) */
        .user-name {
            font-size: 10px; /* Dikecilkan dari 12px ke 10px */
            font-weight: 800; /* Bold tetap */
            color: #ffffff; 
            text-transform: uppercase;
            margin-bottom: 2px;
            line-height: 1.3;
            background: transparent; 
            padding: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.9);
        }

        /* NIP */
        .nip-text {
            font-size: 8px;
            color: #ffffff;
            font-weight: 600;
            margin-top: 0;
            background: transparent;
            padding: 0;
            text-shadow: 0 1px 3px rgba(0,0,0,0.9);
        }

        /* --- FOOTER --- */
        .card-footer {
            width: 100%;
            padding-bottom: 15px; 
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 0; 
            background: transparent;
        }

        /* QR CODE DENGAN BORDER PUTIH (PADDING) */
        .qr-box {
            padding: 4px; /* Ini yang jadi bordernya */
            background: white; 
            border-radius: 4px; /* Sudut tumpul sedikit */
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            display: block;
            line-height: 0;
        }

        /* --- PRINT SETUP --- */
        @media print {
            @page {
                size: 54mm 85.6mm;
                margin: 0;
            }
            body {
                background: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .id-card-container {
                border: 0.5px solid #ddd;
                page-break-after: always;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="width: 100%; text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #002b5c; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
            🖨️ Cetak Semua ({{ count($gtks) }})
        </button>
    </div>

    @foreach($gtks as $gtk)
    
    <div class="id-card-container" 
        @if(isset($sekolah) && $sekolah->background_kartu)
            style="background-image: url('{{ asset('storage/' . $sekolah->background_kartu) }}'); background-size: cover; background-repeat: no-repeat;"
        @endif
    >
        
        <div class="header-section">
            @if($sekolah && $sekolah->logo)
                <img src="{{ asset('storage/' . $sekolah->logo) }}" class="header-logo" alt="Logo">
            @else
                <img src="{{ asset('assets/img/logo-sekolah-placeholder.png') }}" class="header-logo" alt="Logo">
            @endif
            
            <div class="header-text">
                @if(str_contains($gtk->jenis_ptk_id_str, 'Guru'))
                    KARTU GURU
                @else
                    KARTU TENAGA KEPENDIDIKAN
                @endif
            </div>
        </div>

        <div class="card-body">
            @if($gtk->foto)
                <img src="{{ asset('storage/' . $gtk->foto) }}" class="profile-img" alt="Foto">
            @else
                <div class="profile-img" style="display: flex; align-items: center; justify-content: center; background: #f0f0f0; color: #aaa;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" style="fill: currentColor;"><path d="M12 2C6.579 2 2 6.579 2 12s4.579 10 10 10 10-4.579 10-10S17.421 2 12 2zm0 5c1.727 0 3 1.272 3 3s-1.273 3-3 3c-1.726 0-3-1.272-3-3s1.274-3 3-3zm-5.106 9.772c.897-1.32 2.393-2.2 4.106-2.2h2c1.714 0 3.209.88 4.106 2.2C15.828 18.14 14.015 19 12 19s-3.828-.86-5.106-2.228z"></path></svg>
                </div>
            @endif

            <div class="user-name">{{ \Illuminate\Support\Str::limit($gtk->nama, 35) }}</div>
            
            @if($gtk->nip || $gtk->nuptk)
                <div class="nip-text">NIP/NUPTK: {{ $gtk->nip ?? $gtk->nuptk }}</div>
            @endif
        </div>

        <div class="card-footer">
            <div class="qr-box">
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(70)->generate($gtk->ptk_id ?? $gtk->id) !!}
            </div>
        </div>

    </div>
    @endforeach

</body>
</html>