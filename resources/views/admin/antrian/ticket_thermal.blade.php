<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Tiket Antrian</title>
    <style>
        @page {
            margin: 0;
            size: 80mm 200mm; /* Atur sesuai lebar kertas (biasanya 58mm atau 80mm) */
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 72mm; /* Sesuaikan dengan area cetak */
            margin: 0 auto;
            padding: 10px;
            text-align: center;
            color: #000;
        }
        .header {
            border-bottom: 1px dashed #000;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }
        .instansi {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .alamat {
            font-size: 10px;
            margin: 2px 0;
        }
        .title {
            font-size: 12px;
            margin: 10px 0 5px 0;
        }
        .nomor {
            font-size: 48px;
            font-weight: bold;
            margin: 5px 0;
        }
        .info {
            text-align: left;
            font-size: 11px;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        .footer {
            margin-top: 15px;
            font-size: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        .waktu {
            font-size: 9px;
            margin-top: 5px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <p class="instansi">{{ $instansi->nama_instansi ?? 'KCD WILAYAH' }}</p>
        <p class="alamat">Provinsi Jawa Barat</p>
    </div>

    <div class="title">NOMOR ANTRIAN</div>
    <div class="nomor">{{ $antrian->nomor_antrian }}</div>

    <div class="info">
        <div>NAMA   : {{ $antrian->nama }}</div>
        <div>TUJUAN : {{ $antrian->tujuanPegawai ? $antrian->tujuanPegawai->nama : 'UMUM/RESEPSIONIS' }}</div>
    </div>

    <div class="footer">
        Harap menunggu nomor Anda dipanggil.<br>
        Terima Kasih Atas Kunjungan Anda.
    </div>

    <div class="waktu">
        {{ $antrian->created_at->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
