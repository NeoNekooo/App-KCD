<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; size: 80mm auto; }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 72mm;
            margin: 0 auto;
            padding: 5px;
            text-align: center;
            color: #000;
            font-size: 12px;
        }
        .header { border-bottom: 1px dashed #000; margin-bottom: 8px; padding-bottom: 4px; }
        .instansi { font-size: 14px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .nomor { font-size: 50px; font-weight: bold; margin: 5px 0; }
        .info { text-align: left; font-size: 11px; border-top: 1px dashed #000; padding-top: 5px; margin-top: 10px; }
        .footer { margin-top: 10px; font-size: 10px; border-top: 1px dashed #000; padding-top: 5px; }
    </style>
</head>
<body onload="window.print();">
    <div class="header">
        <p class="instansi">{{ $instansi->nama_instansi ?? 'KCD WILAYAH' }}</p>
        <p style="margin:2px 0; font-size: 10px;">Provinsi Jawa Barat</p>
    </div>

    <div style="font-size: 12px;">NOMOR ANTRIAN</div>
    <div class="nomor">{{ $antrian->nomor_antrian }}</div>

    <div class="info">
        <div>NAMA   : {{ $antrian->nama }}</div>
        <div>TUJUAN : {{ $antrian->tujuanPegawai ? $antrian->tujuanPegawai->nama : 'UMUM/RESEPSIONIS' }}</div>
    </div>

    <div class="footer">
        Harap menunggu nomor Anda dipanggil.<br>
        Terima Kasih Atas Kunjungan Anda.
    </div>

    <div style="font-size: 9px; margin-top: 5px;">
        {{ $antrian->created_at->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
