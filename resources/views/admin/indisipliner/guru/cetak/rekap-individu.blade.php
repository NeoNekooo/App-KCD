<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Pelanggaran Guru</title>
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

    /* Agar pas di-print rapi */
    @media print {
      .no-print { display: none !important; }
      body { margin: 20px; }
      .table th, .table td { border: 1px solid #000 !important; }
    }
  </style>
</head>
<body onload="window.print()"> {{-- langsung buka dialog print --}}
  <div class="kop">
    <h4><strong>SMK NURUL ISLAM</strong></h4>
    <h5>Data Rekapitulasi Pelanggaran Guru</h5>
    <small>Jl. Pendidikan No. 123, Jakarta | Telp: (021) 1234567</small>
  </div>

  <h6><strong>Nama Guru:</strong> {{ $guru->nama }}</h6>
  <h6><strong>NIP:</strong> {{ $guru->nip }}</h6>
  <h6><strong>Status:</strong> {{ $guru->status_kepegawaian ?? '-' }}</h6>
  <hr>

  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Pelanggaran</th>
        <th>Poin</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($pelanggaranGuru as $key => $p)
        <tr>
          <td>{{ $key + 1 }}</td>
          <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
          <td>{{ $p->detailPoinGtk->nama ?? '-' }}</td>
          <td class="text-center">{{ $p->poin }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="text-center text-muted py-3">Tidak ada data pelanggaran.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="mt-3">
    <strong>Total Poin: </strong> {{ $totalPoin }}
    <br>
    <strong>Sanksi Aktif: </strong> {{ $sanksiAktif->nama ?? 'Tidak ada sanksi' }}
  </div>

  <div class="ttd">
    <p>Jakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    <p>Kepala Sekolah</p>
    <br><br><br>
    <p><strong><u>RIRI</u></strong></p>
  </div>
</body>
</html>
