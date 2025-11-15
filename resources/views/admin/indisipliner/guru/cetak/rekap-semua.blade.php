<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Pelanggaran Semua Guru</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body { 
      font-family: 'Times New Roman', serif;
      margin: 40px;
    }
    .kop {
      text-align: center;
      border-bottom: 3px solid #000;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    .kop h4, .kop h5 { margin: 0; }
    .table th, .table td { vertical-align: middle; }
    .ttd {
      width: 250px;
      text-align: center;
      float: right;
      margin-top: 50px;
    }

    /* Style print agar tabel tidak terpotong dan rapi */
    @media print {
      body { margin: 20px; }
      .no-print { display: none; }
      .table th, .table td {
        border: 1px solid #000 !important;
        padding: 6px !important;
      }
      .kop {
        border-bottom: 2px solid #000 !important;
        margin-bottom: 10px;
      }
      @page {
        size: A4;
        margin: 20mm;
      }
    }
  </style>
</head>
<body onload="window.print()"> {{-- Langsung buka jendela print --}}

  <div class="kop">
    <h4><strong>SMK NURUL ISLAM</strong></h4>
    <h5>Rekapitulasi Pelanggaran Semua Guru</h5>
    <small>Jl. Pendidikan No. 123, Jakarta | Telp: (021) 1234567</small>
  </div>

  <table class="table table-bordered table-striped">
    <thead class="table-light">
      <tr>
        <th>No</th>
        <th>Nama Guru</th>
        <th>Tanggal</th>
        <th>Pelanggaran</th>
        <th>Poin</th>
        <th>Tahun Pelajaran</th>
        <th>Semester</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($pelanggaranList as $key => $p)
        <tr>
          <td>{{ $key + 1 }}</td>
          <td>{{ $p->nama_guru }}</td>
          <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
          <td style="white-space: normal;">{{ $p->detailPoinGtk->nama ?? '-' }}</td>
          <td class="text-center">{{ $p->poin }}</td>
          <td>{{ $p->tapel }}</td>
          <td>{{ ucfirst($p->semester) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center text-muted py-3">Tidak ada data pelanggaran.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="ttd">
    <p>Jakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    <p>Kepala Sekolah</p>
    <br><br><br>
    <p><strong><u>RIRI</u></strong></p>
  </div>

</body>
</html>
