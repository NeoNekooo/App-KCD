<!DOCTYPE html>
<html>
<head>
    <title>SK - {{ $data->nama_guru }}</title>
    <style>
        /* 1. RESET CSS UNTUK PDF */
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 12pt; 
            line-height: 1.5; 
            margin: 0; 
        }
        
        /* 2. MARGIN HALAMAN DINAMIS (DARI DATABASE) */
        @page {
            margin-top: {{ $template->margin_top ?? 2.5 }}cm;
            margin-right: {{ $template->margin_right ?? 2.5 }}cm;
            margin-bottom: {{ $template->margin_bottom ?? 2.5 }}cm;
            margin-left: {{ $template->margin_left ?? 2.5 }}cm;
        }

        /* 3. STYLE KOP SURAT */
        .kop-header { width: 100%; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 25px; }
        .kop-header td { vertical-align: middle; text-align: center; }
        .kop-logo { width: 85px; }
        .kop-text h3 { margin: 0; font-size: 14pt; font-weight: normal; text-transform: uppercase; }
        .kop-text h2 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .kop-text p { margin: 0; font-size: 10pt; font-style: italic; }

        /* 4. STYLE KONTEN (DARI EDITOR) */
        .konten-surat { text-align: justify; }
        
        /* Fix tabel dari editor biar rapi di PDF */
        .konten-surat table { width: 100%; margin-top: 10px; margin-bottom: 10px; border-collapse: collapse; }
        .konten-surat td { vertical-align: top; padding: 2px 5px; }

        /* 5. STYLE TANDA TANGAN */
        .ttd-table { width: 100%; margin-top: 50px; border: none; }
        .ttd-table td { vertical-align: top; }
        .ttd-kanan { width: 45%; text-align: left; padding-left: 30px; } 
        .ttd-nama { font-weight: bold; text-decoration: underline; margin-top: 70px; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    {{-- Kalau di Template Editor kamu sudah bikin KOP sendiri, hapus table ini --}}
    <table class="kop-header">
        <tr>
            <td width="15%">
                {{-- Gunakan public_path agar gambar terbaca oleh DomPDF --}}
                <img src="{{ public_path('img/logo-jabar.png') }}" class="kop-logo">
            </td>
            <td width="85%" class="kop-text">
                <h3>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h3>
                <h2>DINAS PENDIDIKAN</h2>
                <h3>CABANG DINAS PENDIDIKAN WILAYAH XII</h3>
                <p>Jalan Dr. Sukardjo No.XXX, Kota Tasikmalaya - Jawa Barat</p>
            </td>
        </tr>
    </table>

    {{-- ISI SURAT (FULL DARI TEMPLATE DATABASE) --}}
    <div class="konten-surat">
        {!! $konten !!}
    </div>

    {{-- TANDA TANGAN --}}
    {{-- Ini opsional, bisa disimpan di sini atau dimasukkan ke dalam Template Editor juga --}}
    <table class="ttd-table">
        <tr>
            <td width="55%"></td> {{-- Spacer Kiri --}}
            <td class="ttd-kanan">
                <div>Ditetapkan di: Tasikmalaya</div>
                <div>Pada Tanggal: {{ \Carbon\Carbon::parse($data->tgl_selesai)->isoFormat('D MMMM Y') }}</div>
                <br>
                <div><strong>KEPALA CABANG DINAS PENDIDIKAN <br>WILAYAH XII,</strong></div>
                
                {{-- Space Tanda Tangan --}}
                <div style="height: 60px;"></div>

                <div class="ttd-nama">DEDI SURYADIN, S.Pd., M.Pd.</div>
                <div>NIP. 19700101 200012 1 001</div>
            </td>
        </tr>
    </table>

</body>
</html>