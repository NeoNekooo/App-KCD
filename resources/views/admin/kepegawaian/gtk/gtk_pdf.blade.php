<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Guru - {{ $gtk->nama }}</title>
    <style>
        @page {
            margin: 0.5cm 1.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #333;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            margin-bottom: 10px;
        }
        .header-table td {
            vertical-align: middle;
            text-align: center;
        }
        .logo {
            width: 70px;
        }
        .kop-title {
            font-size: 16px;
            font-weight: bold;
        }
        .kop-subtitle {
            font-size: 14px;
            font-weight: bold;
        }
        .kop-address {
            font-size: 10px;
        }
        .content-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin: 20px 0;
        }
        .info-date {
            text-align: center;
            font-size: 11px;
            margin-bottom: 25px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .detail-table th, .detail-table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }
        .detail-table th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .detail-table .label-strong {
            font-weight: bold;
        }
        .lampiran-table td {
            text-align: center;
        }
        .lampiran-table .text-left {
            text-align: left;
        }
        .signature-section {
            margin-top: 40px;
            width: 300px;
            float: right;
            text-align: center;
        }
        .catatan {
            margin-top: 50px;
            font-size: 9px;
            text-align: justify;
        }
        .page-break {
            page-break-after: always;
        }
        footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 50px;
            font-size: 10px;
            text-align: right;
        }
    </style>
</head>
<body>
    <footer>
        Profil Guru - Hal <span class="page-number"></span>
    </footer>

    <table class="header-table">
        <tr>
            @if($sekolah && $sekolah->logo)
            <td><img src="{{ public_path('storage/' . $sekolah->logo) }}" alt="Logo" class="logo"></td>
            @else
            <td style="width: 70px;"></td>
            @endif
            <td>
                <div class="kop-title">PEMERINTAH PROVINSI JAWA BARAT</div>
                <div class="kop-title">DINAS PENDIDIKAN</div>
                <div class="kop-subtitle">{{ strtoupper($sekolah->nama ?? 'NAMA SEKOLAH') }}</div>
                <div class="kop-address">
                    NPSN: {{ $sekolah->npsn ?? '-' }} {{ $sekolah->alamat_jalan ?? '-' }}
                    <br>
                    Telp: {{ $sekolah->nomor_telepon ?? '-' }} Fax: {{ $sekolah->nomor_fax ?? '-' }} Email: {{ $sekolah->email ?? '-' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="content-title">PROFIL GURU</div>
    <div class="info-date">
        Data berikut dikeluarkan melalui aplikasi Dapodik Ditjen Paudikdasmen pada tanggal {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} Pukul: {{ \Carbon\Carbon::now()->format('H:i:s') }}
    </div>

    <div class="page-break"></div>

    <h4 style="text-align:center;">Profil Guru</h4>
    <table class="detail-table">
        <thead>
            <tr>
                <th style="width: 30%;">Atribut</th>
                <th style="width: 45%;">Isian</th>
                <th style="width: 25%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="label-strong">Tanggal Perubahan</td><td>{{ $gtk->updated_at ? $gtk->updated_at->format('Y-m-d H:i:s') : '-' }}</td><td>Diperoleh dari tabel GTK</td></tr>
            <tr>
                <td class="label-strong">Nomor Surat Tugas</td>
                <td>{{ $tugasTerbaru->nomor_surat_tugas ?? '-' }}</td>
                <td>Diperoleh dari tabel penugasan</td>
            </tr>
            <tr>
                <td class="label-strong">Tanggal Surat Tugas</td>
                <td>{{ $gtk->tanggal_surat_tugas ? \Carbon\Carbon::parse($gtk->tanggal_surat_tugas)->format('Y-m-d') : '-' }}</td>
                <td>Diperoleh dari tabel GTK</td>
            </tr>
            <tr><td class="label-strong">Tahun Ajaran</td><td>{{ $gtk->tahun_ajaran_id ?? '-' }}</td><td>Diperoleh dari tabel GTK</td></tr>
            <tr><td class="label-strong">Sekolah Induk</td><td>{{ $gtk->ptk_induk == 1 ? 'Ya' : 'Bukan' }}</td><td></td></tr>
            <tr><td class="label-strong">Nama</td><td>{{ $gtk->nama ?? '-' }}</td><td></td></tr>
            <tr><td class="label-strong">NIK</td><td>{{ $gtk->nik ?? '-' }}</td><td></td></tr>
            <tr><td class="label-strong">Jenis Kelamin</td><td>{{ $gtk->jenis_kelamin ?? '-' }}</td><td></td></tr>
            <tr><td class="label-strong">TTL</td><td>{{ $gtk->tempat_lahir ?? '-' }}, {{ $gtk->tanggal_lahir ? \Carbon\Carbon::parse($gtk->tanggal_lahir)->format('d F Y') : '-' }}</td><td>Tempat dan tanggal lahir</td></tr>
            <tr><td class="label-strong">Agama</td><td>{{ $gtk->agama_id_str ?? '-' }}</td><td></td></tr>
            <tr><td class="label-strong">Status Kepegawaian</td><td>{{ $gtk->status_kepegawaian_id_str ?? '-' }}</td><td></td></tr>
            <tr><td class="label-strong">Jenis GTK</td><td>{{ $gtk->jenis_ptk_id_str ?? '-' }}</td><td></td></tr>
            
            <tr>
                <td class="label-strong">Wali Kelas</td>
                <td>{{ $rombelWali->nama ?? '-' }}</td>
                <td>Diperoleh dari tabel Rombel</td>
            </tr>
            <tr><td class="label-strong">Jabatan GTK</td><td>{{ $gtk->jabatan_ptk_id_str ?? '-' }}</td><td></td></tr>
            <tr><td class="label-strong">NUPTK</td><td>{{ $gtk->nuptk ?? '-' }}</td><td></td></tr>
            <tr><td class="label-strong">NIY/NIGK</td><td>-</td><td></td></tr>
            <tr><td class="label-strong">Pendidikan Terakhir</td><td>{{ $gtk->pendidikan_terakhir ?? '-' }}</td><td>Diperoleh dari tabel riwayat pendidikan formal</td></tr>
            <tr><td class="label-strong">Email</td><td>-</td><td>Diperoleh dari akun pengguna</td></tr>
        </tbody>
    </table>

    <div class="page-break"></div>
    
    <h4 style="text-align:center;">Lampiran Rekapitulasi Pembelajaran <br> {{ strtoupper($gtk->nama) }}</h4>
    
    <table class="detail-table lampiran-table">
        <thead>
            <tr>
                <th>No</th>
                <th class="text-left">Informasi Rombel</th>
                <th class="text-left">Mata Pelajaran</th>
                <th>Jumlah Jam/Minggu</th>
            </tr>
        </thead>
        <tbody>
            
            @php
                $totalJam = 0;
                $counter = 1;
            @endphp

            @forelse ($rombelMengajar as $rombel)
                {{-- Tambahkan pengecekan di sini --}}
                @if (isset($rombel->pembelajaran) && is_array($rombel->pembelajaran))
                    @foreach ($rombel->pembelajaran as $mapel)
                        @if (isset($mapel['ptk_id']) && $mapel['ptk_id'] == $gtk->ptk_id)
                            
                            @php
                                $jamMapel = $mapel['jam_mengajar_per_minggu'] ?? 0;
                                $totalJam += (int) $jamMapel;
                            @endphp

                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td class="text-left">
                                    Jenis Rombel: {{ $rombel->jenis_rombel_str ?? 'Kelas' }}<br>
                                    Tingkat: {{ $rombel->tingkat_pendidikan_id ?? '-' }}<br>
                                    Nama: {{ $rombel->nama ?? '-' }}<br>
                                    Kurikulum: {{ $rombel->kurikulum_id_str ?? '-' }}
                                </td>
                                <td class="text-left">{{ $mapel['nama_mata_pelajaran'] ?? 'Mapel tidak ditemukan' }}</td>
                                <td>{{ $jamMapel }}</td>
                            </tr>

                        @endif
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; font-style: italic;">
                        Tidak ada data mengajar yang ditemukan di rombongan belajar manapun.
                    </td>
                </tr>
            @endforelse
            
        </tbody>
        <tfoot>
             <tr>
                <td colspan="3" style="text-align: right; font-weight:bold;">Jumlah Total Jam Mengajar</td>
                <td style="font-weight:bold;">{{ $totalJam }}</td>
            </tr>
        </tfoot>
    </table>
    
    <div style="width:100%; text-align: center; margin-top: 20px;">
        {!! QrCode::size(120)->generate($qrCodeData); !!}
    </div>

    <div class="signature-section">
        Kab. Cianjur, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        <br>
        Menyetujui,
        <br><br><br><br><br>
        <strong><u>{{ strtoupper($gtk->nama) }}</u></strong>
        <br>
        NIY/NIGK.
    </div>
    
    <div style="clear:both;"></div>

    <div class="catatan">
        <p><strong>Catatan:</strong></p>
        <ol>
            <li>Data dalam formulir ini bersifat sangat rahasia, mohon digunakan secara bijak. Menyebarkan data berikut tanpa seizin pemilik data dapat dikenakan sanksi sesuai dengan undang-undang yang berlaku.</li>
            <li>Untuk kepentingan tunjangan dan aneka kebijakan akan dilakukan validasi dan verifikasi oleh Direktorat Jenderal Guru dan Tenaga Kependidikan, Kemendikdasmen.</li>
            <li>Kebenaran data merupakan tanggung jawab dari pendidik yang bersangkutan.</li>
            <li>Untuk kelompok mata pelajaran muatan sekolah dan tambahan tidak diperhitungkan untuk beban mengajar kecuali Guru BK dan Guru TIK pada Kurikulum 2013.</li>
        </ol>
    </div>
</body>
</html>