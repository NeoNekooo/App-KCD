<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu - {{ $siswa->nama }}</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root {
            --card-width: 60mm;
            --card-height: 95mm;
        }

        body {
            background-color: #e9e9e9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            flex-direction: column;
        }

        .id-card {
            width: var(--card-width);
            height: var(--card-height);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            background-color: white;
            border: 1px solid #eee;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .id-card-header {
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 10px;
            font-weight: bold;
            color: #333;
            flex-shrink: 0;
        }
        
        .school-logo {
            width: 24px;
            height: 24px;
        }

        .card-photo-area {
            flex-grow: 1;
            position: relative;
            background-size: cover;
            background-position: center;
        }
        
        /* PERUBAHAN: Selector .card-overlay dihapus dari CSS */

        .card-content {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 0 12px 95px 12px;
            box-sizing: border-box;
            z-index: 2;
            color: white;
        }

        .student-info .nama {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
            /* Bayangan dipertegas untuk keterbacaan */
            text-shadow: 1px 1px 5px rgba(0,0,0,1); 
        }
        
        /* PERUBAHAN: Latar belakang NISN dihapus dan diganti text-shadow */
        .student-info .nisn {
            font-size: 11px;
            background-color: transparent; /* Latar dihilangkan */
            padding: 2px 0; /* Padding disesuaikan */
            display: inline-block;
            text-shadow: 1px 1px 3px rgba(0,0,0,1); /* Ditambah bayangan */
        }

        .qr-code {
            position: absolute;
            bottom: 15px;
            right: 15px;
            z-index: 2;
        }
        
        .qr-code svg {
            width: 22mm;
            height: 22mm;
            display: block;
        }

        .print-button {
            margin-top: 25px;
            min-width: var(--card-width);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        @media print {
            body {
                background-color: white;
                display: block;
                padding: 0;
            }
            .id-card {
                margin: 0;
                box-shadow: none;
                border: 1px dashed #999;
                border-radius: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="id-card">
        <div class="id-card-header">
            <img src="{{ asset('path/ke/logo_sekolah.png') }}" alt="Logo" class="school-logo">
            <span>KARTU PESERTA DIDIK</span>
        </div>
        
        <div class="card-photo-area" style="background-image: url('{{ $siswa->foto ? asset('storage/' . $siswa->foto) : 'https://via.placeholder.com/400x600' }}');">
            <div class="card-content">
                <div class="student-info">
                    <div class="nama">{{ $siswa->nama }}</div>
                    <div class="nisn">{{ $siswa->nisn }}</div>
                </div>
            </div>

            <div class="qr-code">
                @php
                    $svg = QrCode::size(150)->generate($siswa->qr_token);
                    $svg = preg_replace('/<rect.*?fill="#ffffff".*?\/>/', '', $svg);
                    $svg = str_replace('fill="#000000"', 'fill="#ffffff"', $svg);
                @endphp
                
                {!! $svg !!}
            </div>
        </div>
    </div>

    <button onclick="window.print()" class="btn btn-primary no-print print-button">
        <i class='bx bx-printer'></i>
        <span>Cetak Kartu</span>
    </button>

</body>
</html>