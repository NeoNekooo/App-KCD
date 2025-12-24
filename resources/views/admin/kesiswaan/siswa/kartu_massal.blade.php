<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Massal - {{ $rombel->nama }}</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root {
            --card-width: 60mm;
            --card-height: 95mm;
        }

        body {
            background-color: #e9e9e9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .a4-sheet {
            background: white;
            width: 210mm;
            height: 297mm;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            box-sizing: border-box;
            justify-items: center;
            padding: 5mm 9mm;
            gap: 1mm 6mm;
        }
        
        .page-break {
            page-break-after: always;
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
        }
        
        .id-card-header {
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #333;
            flex-shrink: 0;
        }
        
        .school-logo {
            width: 32px;
            height: 32px;
        }

        .card-photo-area {
            flex-grow: 1;
            position: relative;
            overflow: hidden;
        }

        .card-photo-area img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        
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
            text-shadow: 1px 1px 5px rgba(0,0,0,1);
        }
        
        .student-info .nisn {
            font-size: 11px;
            background-color: transparent;
            padding: 2px 0;
            display: inline-block;
            text-shadow: 1px 1px 3px rgba(0,0,0,1);
        }

        /* --- ATURAN BARU UNTUK KONDISI TANPA FOTO --- */
        .card-photo-area.no-photo .card-content {
            color: #333; /* Ubah warna teks menjadi gelap */
        }

        .card-photo-area.no-photo .student-info .nama,
        .card-photo-area.no-photo .student-info .nisn {
            text-shadow: none; /* Hapus bayangan teks */
        }
        /* --- AKHIR ATURAN BARU --- */

        .qr-code {
            position: absolute;
            bottom: 15px;
            right: 15px;
            z-index: 2;
            background: white;
            padding: 4px;
            border-radius: 4px;
        }
        
        .qr-code svg {
            width: 22mm;
            height: 22mm;
            display: block;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        @media print {
            body { background-color: white; }
            .a4-sheet { margin: 0; box-shadow: none; }
            .id-card { border: 1px dashed #999; }
            .print-button { display: none; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn btn-primary print-button">
        <i class='bx bx-printer'></i> Cetak Halaman
    </button>
    
    @php $kartuPerPage = 9; @endphp
    @forelse ($siswas->chunk($kartuPerPage) as $chunk)
        <div class="a4-sheet {{ !$loop->last ? 'page-break' : '' }}">
            @foreach ($chunk as $siswa)
            <div class="id-card">
                <div class="id-card-header">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="school-logo">
                    <span>KARTU PESERTA DIDIK</span>
                </div>
                
                <div class="card-photo-area {{ !$siswa->foto ? 'no-photo' : '' }}">
                    <img src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : 'https://via.placeholder.com/400x600' }}" alt="Foto {{ $siswa->nama }}">

                    <div class="card-content">
                        <div class="student-info">
                            <div class="nama">{{ $siswa->nama }}</div>
                            <div class="nisn">{{ $siswa->nisn }}</div>
                        </div>
                    </div>

                    <div class="qr-code">
                        {!! QrCode::size(150)->generate($siswa->qr_token) !!}
                    </div>
                </div>

            </div>
            @endforeach
        </div>
    @empty
        <div style="text-align: center; padding: 50px; font-size: 1.2rem;">
            <i class='bx bx-user-x' style="font-size: 5rem; color: #ccc;"></i>
            <h3 class="mt-3">Tidak Ada Siswa di Kelas Ini</h3>
            <p>Tidak ada kartu yang dapat dicetak.</p>
        </div>
    @endforelse

</body>
</html>