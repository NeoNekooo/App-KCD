<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cetak SK</title>
    <style>
        /* === 1. SETUP HALAMAN === */
        @page { 
            margin: 0; /* Margin diatur lewat padding body */
        }
        
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 12pt; 
            line-height: 1.5;
            color: #000;
            padding: 2.54cm; /* Margin Default (Atas/Kanan/Bawah/Kiri) */
            
            /* Pastikan teks panjang membungkus ke bawah (tidak melebar) */
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* === 2. HANDLING PAGE BREAK === */
        /* Class ini otomatis dibuat TinyMCE saat tombol Page Break diklik */
        .mce-pagebreak {
            page-break-after: always;
            border: 0;
            height: 0;
            margin: 0;
            display: block;
        }

        /* Kop Surat (Khusus Halaman 1 jika opsi dicentang) */
        .has-kop {
            padding-top: 5cm !important; 
        }

        /* === 3. STYLE TABEL === */
        /* Kita hanya atur layout. Urusan border diatur oleh Controller & TinyMCE */
        table {
            width: 100% !important;
            border-collapse: collapse;
            table-layout: fixed; /* Wajib agar kolom sesuai persentase */
            margin-bottom: 1em;
            empty-cells: show;
        }
        
        td, th {
            padding: 2px 4px;
            vertical-align: top;
            
            /* Mencegah teks panjang merusak tabel */
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* === 4. GAMBAR & UTILS === */
        img { max-width: 100%; height: auto; }
        
        p { margin-top: 0; margin-bottom: 8px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-justify { text-align: justify; }
    </style>
</head>
<body class="{{ ($template->use_kop == 1) ? 'has-kop' : '' }}">
    
    {{-- Render HTML yang sudah diproses --}}
    {!! $isiSurat !!}

</body>
</html>