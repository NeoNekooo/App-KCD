<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Resi Pendaftaran - {{ $calon->nama_lengkap }}</title>
  <style>
    @page {
      size: A4;
      margin: 15mm; /* biar ada jarak tepi */
    }

    body {
      font-family: Arial, sans-serif;
      font-size: 11px;
      margin: 0;
      padding: 0;
    }

    .page-border {
      border: 1px solid #000;
      padding: 10px 15px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    td {
      vertical-align: top;
      padding: 3px;
    }

    .header td {
      border: none;
    }

    .header-logo {
      width: 80px;
      text-align: center;
    }

    .header-title {
      text-align: center;
      font-size: 12px;
      line-height: 1.4;
    }

    .header-right {
      width: 80px;
      text-align: center;
      border-left: 1px solid #000;
      font-size: 12px;
    }

    .header-right strong {
      font-size: 16px;
    }

    hr {
      border: none;
      border-top: 1px solid #000;
      margin: 5px 0;
    }

    .photo-box {
      border: 1px solid #000;
      width: 100px;
      height: 130px;
      text-align: center;
      vertical-align: middle;
      font-size: 10px;
    }

    .note {
      font-size: 10px;
      margin-top: 10px;
    }

    .center {
      text-align: center;
    }
  </style>
</head>
<body onload="window.print()">
  <div class="page-border">
    <!-- HEADER -->
    <table class="header" style="width:100%;">
      <tr>
        <td class="header-logo">
            <img src="{{ $profilSekolah->logo ? asset('storage/' .$profilSekolah->logo) : asset('profil/default.png') }}" alt="Logo Sekolah" width="70">
        </td>
        <td class="header-title">
          <strong>PANITIA PENERIMAAN PESERTA DIDIK BARU (PPDB)</strong><br>
          <strong>SMK NURUL ISLAM CIANJUR</strong><br>
          <small>Jl. Raya Cianjur Bandung Km. 09 RT/RW 001/007 Desa Selajambe Kec. Sukaluyu Kabupaten Cianjur, Kec. Sukaluyu - Cianjur 43284</small>
        </td>
        <td class="header-right">
          Urt Daftar<br>
          <strong>{{ substr($calon->nomor_resi, -3) }}</strong>
        </td>
      </tr>
    </table>

    <hr>

    <!-- JUDUL -->
    <div class="center" style="margin-bottom:8px;">
      <strong>RESI PENDAFTARAN</strong><br>
      No. Resi : {{ $calon->nomor_resi }}
    </div>

    <!-- DATA SISWA -->
    <table>
      <tr>
        <td style="width: 180px;">Nama Pendaftar</td>
        <td>: {{ $calon->nama_lengkap }}</td>
        <td rowspan="8" class="photo-box">Pas Photo<br>3 x 4</td>
      </tr>
      <tr>
        <td>Jenis Kelamin</td>
        <td>: {{ $calon->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
      </tr>
      <tr>
        <td>Tempat, tanggal lahir</td>
        <td>: {{ $calon->tempat_lahir }}, {{ \Carbon\Carbon::parse($calon->tgl_lahir)->translatedFormat('d F Y') }}</td>
      </tr>
      <tr>
        <td>Nama Orang Tua</td>
        <td>: {{ $calon->nama_ayah }} / {{ $calon->nama_ibu }}</td>
      </tr>
      <tr>
        <td>Alamat</td>
        <td>: {{ $calon->alamat }}</td>
      </tr>
      <tr>
        <td>Sekolah Asal</td>
        <td>: {{ $calon->asal_sekolah }}</td>
      </tr>
      <tr>
        <td>Paket Keahlian yang diminati</td>
        <td>: {{ $calon->jurusan ?? '-' }}</td>
      </tr>
      <tr>
        @php
        $semuaSyarat = $calon->jalurPendaftaran
            ? $calon->jalurPendaftaran->syaratPendaftaran()->get()
            : collect();
            
        $syaratTerpenuhiIds = $calon->syarat
            ->where('pivot.is_checked', true)
            ->pluck('id')
            ->toArray();
        @endphp
            
                <<td>Persyaratan</td>
        <td>
          @foreach($semuaSyarat as $s)
            @php
                $isChecked = in_array($s->id, $syaratTerpenuhiIds);
            @endphp
            {!! $isChecked ? '✓' : '✗' !!} {{ $s->syarat }}<br>
          @endforeach
        </td>
      </tr>
    </table>

    <!-- TANDA TANGAN -->
    @php
        $namaCalon = strtoupper($calon->nama_lengkap);
        $namaAdmin = 'Administrator';
    @endphp
    
    <table style="width:100%; margin-top:50px;">
      <tr style="vertical-align:bottom;">
        <!-- Calon Siswa -->
        <td style="width:50%; text-align:center;">
          Calon Siswa<br><br><br><br><br> <!-- tinggi area tanda tangan -->
          <div style="display:inline-block; border-top:1px solid #000; width:{{ strlen($namaCalon) * 7 }}px;"></div><br>
          <strong>{{ $namaCalon }}</strong>
        </td>
    
        <!-- Administrator -->
        <td style="width:50%; text-align:center;">
          Cianjur, {{ now()->translatedFormat('d F Y') }}<br>
          Piket PPDB<br><br><br><br>
          <div style="display:inline-block; border-top:1px solid #000; width:{{ strlen($namaAdmin) * 7 }}px;"></div><br>
          <strong>{{ $namaAdmin }}</strong>
        </td>
      </tr>
    </table>

    <!-- CATATAN -->
    <div class="note">
      <strong>Catatan :</strong><br>
      1. (✓) sudah ada, (✗) belum ada/harus segera dilengkapi<br>
      2. Resi Pendaftaran ini wajib dibawa pada saat melengkapi persyaratan/daftar ulang
    </div>
  </div>
</body>
</html>
