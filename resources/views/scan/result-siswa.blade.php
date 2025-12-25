<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Siswa - {{ $data->nama }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .data-label { @apply text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1; }
        .data-value { @apply text-slate-800 font-medium text-sm sm:text-base border-b border-slate-100 pb-2; }
        .section-title { @apply text-blue-600 font-bold text-lg mb-4 flex items-center border-b-2 border-blue-100 pb-2; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-6 px-4 sm:px-6">

    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">

        <div class="relative bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-white">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 relative z-10">
                <div class="flex-shrink-0">
                    <div class="w-24 h-24 bg-white text-blue-600 rounded-full flex items-center justify-center text-3xl font-bold shadow-lg border-4 border-blue-400">
                        {{ substr($data->nama, 0, 1) }}
                    </div>
                </div>

                <div class="text-center sm:text-left flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold mb-2">{{ $data->nama }}</h1>
                    <div class="flex flex-wrap justify-center sm:justify-start gap-3 text-sm text-blue-100">
                        <span class="bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                            <i class="fas fa-id-card mr-1"></i> NISN: {{ $data->nisn ?? '-' }}
                        </span>
                        <span class="bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                            <i class="fas fa-fingerprint mr-1"></i> NIPD: {{ $data->nipd ?? '-' }}
                        </span>
                        <span class="bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                            <i class="fas fa-venus-mars mr-1"></i> {{ $data->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                    </div>
                </div>

                <div class="absolute top-4 right-4 sm:static">
                    <span class="bg-green-400 text-green-900 text-xs font-bold px-3 py-1 rounded shadow">
                        VERIFIED <i class="fas fa-check-circle ml-1"></i>
                    </span>
                </div>
            </div>

            <div class="absolute top-0 right-0 opacity-10">
                <i class="fas fa-graduation-cap text-9xl transform rotate-12 translate-x-4 -translate-y-2"></i>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-8">

            <section>
                <h3 class="section-title"><i class="fas fa-user-circle mr-2"></i> Identitas Pribadi</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <p class="data-label">Nama Lengkap</p>
                        <p class="data-value">{{ $data->nama }}</p>
                    </div>
                    <div>
                        <p class="data-label">NIK</p>
                        <p class="data-value">{{ $data->nik ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Tempat, Tanggal Lahir</p>
                        <p class="data-value">{{ $data->tempat_lahir }}, {{ $data->tanggal_lahir }}</p>
                    </div>
                    <div>
                        <p class="data-label">Agama</p>
                        <p class="data-value">{{ $data->agama_id_str ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">No. Registrasi Akta Lahir</p>
                        <p class="data-value">{{ $data->no_registrasi_akta_lahir ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Berkebutuhan Khusus</p>
                        <p class="data-value">{{ $data->kebutuhan_khusus_id_str ?? 'Tidak' }}</p>
                    </div>
                </div>
            </section>

            <section>
                <h3 class="section-title"><i class="fas fa-map-marker-alt mr-2"></i> Alamat & Kontak</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                    <div class="sm:col-span-2">
                        <p class="data-label">Alamat Jalan</p>
                        <p class="data-value">{{ $data->alamat_jalan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">RT / RW</p>
                        <p class="data-value">{{ $data->rt ?? '0' }} / {{ $data->rw ?? '0' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Dusun</p>
                        <p class="data-value">{{ $data->dusun ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Desa / Kelurahan</p>
                        <p class="data-value">{{ $data->desa_kelurahan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Kecamatan</p>
                        <p class="data-value">{{ $data->kecamatan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Kode Pos</p>
                        <p class="data-value">{{ $data->kode_pos ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Jenis Tinggal</p>
                        <p class="data-value">{{ $data->jenis_tinggal_id_str ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Transportasi</p>
                        <p class="data-value">{{ $data->alat_transportasi_id_str ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">No. HP / Telepon</p>
                        <p class="data-value">{{ $data->hp ?? '-' }} / {{ $data->telepon ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Email</p>
                        <p class="data-value lowercase text-blue-600">{{ $data->email ?? '-' }}</p>
                    </div>
                </div>
            </section>

            <section>
                <h3 class="section-title"><i class="fas fa-ruler-combined mr-2"></i> Data Periodik (Fisik)</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                    <div class="text-center">
                        <i class="fas fa-ruler-vertical text-slate-400 mb-1"></i>
                        <p class="text-xs text-slate-500">Tinggi Badan</p>
                        <p class="font-bold text-slate-800">{{ $data->tinggi_badan ?? '-' }} cm</p>
                    </div>
                    <div class="text-center border-l border-slate-200">
                        <i class="fas fa-weight text-slate-400 mb-1"></i>
                        <p class="text-xs text-slate-500">Berat Badan</p>
                        <p class="font-bold text-slate-800">{{ $data->berat_badan ?? '-' }} kg</p>
                    </div>
                    <div class="text-center border-l border-slate-200">
                        <i class="fas fa-hat-cowboy-side text-slate-400 mb-1"></i>
                        <p class="text-xs text-slate-500">Lingkar Kepala</p>
                        <p class="font-bold text-slate-800">{{ $data->lingkar_kepala ?? '-' }} cm</p>
                    </div>
                    <div class="text-center border-l border-slate-200">
                        <i class="fas fa-users text-slate-400 mb-1"></i>
                        <p class="text-xs text-slate-500">Jml Saudara</p>
                        <p class="font-bold text-slate-800">{{ $data->jumlah_saudara_kandung ?? '-' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                     <div>
                        <p class="data-label">Jarak ke Sekolah</p>
                        <p class="data-value">{{ $data->jarak_rumah_ke_sekolah_km ?? '0' }} KM</p>
                    </div>
                     <div>
                        <p class="data-label">Waktu Tempuh</p>
                        <p class="data-value">{{ $data->waktu_tempuh_menit ?? '0' }} Menit</p>
                    </div>
                </div>
            </section>

            <section>
                <h3 class="section-title"><i class="fas fa-male mr-2"></i> Data Ayah</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <p class="data-label">Nama Ayah</p>
                        <p class="data-value">{{ $data->nama_ayah ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Tahun Lahir</p>
                        <p class="data-value">{{ $data->tahun_lahir_ayah ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Pendidikan</p>
                        <p class="data-value">{{ $data->jenjang_pendidikan_ayah_id_str ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Pekerjaan</p>
                        <p class="data-value">{{ $data->pekerjaan_ayah_id_str ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Penghasilan</p>
                        <p class="data-value">{{ $data->penghasilan_ayah_id_str ?? '-' }}</p>
                    </div>
                     <div>
                        <p class="data-label">NIK Ayah</p>
                        <p class="data-value">{{ $data->nik_ayah ?? '-' }}</p>
                    </div>
                </div>
            </section>

             <section>
                <h3 class="section-title"><i class="fas fa-female mr-2"></i> Data Ibu</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <p class="data-label">Nama Ibu Kandung</p>
                        <p class="data-value">{{ $data->nama_ibu_kandung ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Tahun Lahir</p>
                        <p class="data-value">{{ $data->tahun_lahir_ibu ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Pendidikan</p>
                        <p class="data-value">{{ $data->jenjang_pendidikan_ibu_id_str ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Pekerjaan</p>
                        <p class="data-value">{{ $data->pekerjaan_ibu_id_str ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Penghasilan</p>
                        <p class="data-value">{{ $data->penghasilan_ibu_id_str ?? '-' }}</p>
                    </div>
                     <div>
                        <p class="data-label">NIK Ibu</p>
                        <p class="data-value">{{ $data->nik_ibu ?? '-' }}</p>
                    </div>
                </div>
            </section>

             @if(!empty($data->nama_wali))
             <section>
                <h3 class="section-title"><i class="fas fa-user-friends mr-2"></i> Data Wali</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <p class="data-label">Nama Wali</p>
                        <p class="data-value">{{ $data->nama_wali }}</p>
                    </div>
                    <div>
                        <p class="data-label">Tahun Lahir</p>
                        <p class="data-value">{{ $data->tahun_lahir_wali ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Pendidikan</p>
                        <p class="data-value">{{ $data->jenjang_pendidikan_wali_id_str ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Pekerjaan</p>
                        <p class="data-value">{{ $data->pekerjaan_wali_id_str ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Penghasilan</p>
                        <p class="data-value">{{ $data->penghasilan_wali_id_str ?? '-' }}</p>
                    </div>
                </div>
            </section>
            @endif

            @if(($data->penerima_kps ?? '0') == '1' || ($data->penerima_kip ?? '0') == '1')
             <section class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <h3 class="text-yellow-700 font-bold mb-3"><i class="fas fa-star mr-2"></i> Data Kesejahteraan (KIP/KPS)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                     <div>
                        <p class="data-label">Penerima KPS</p>
                        <p class="data-value font-bold text-slate-800">{{ ($data->penerima_kps == '1') ? 'Ya' : 'Tidak' }}</p>
                    </div>
                    <div>
                        <p class="data-label">No. KPS</p>
                        <p class="data-value">{{ $data->no_kps ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Penerima KIP</p>
                        <p class="data-value font-bold text-slate-800">{{ ($data->penerima_kip == '1') ? 'Ya' : 'Tidak' }}</p>
                    </div>
                    <div>
                        <p class="data-label">No. KIP</p>
                        <p class="data-value">{{ $data->nomor_kip ?? '-' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="data-label">Nama Tertera di KIP</p>
                        <p class="data-value">{{ $data->nama_di_kip ?? '-' }}</p>
                    </div>
                </div>
            </section>
            @endif

        </div>

        <div class="bg-slate-800 text-white text-center py-4 text-xs">
             <p>Data diambil dari sinkronisasi Dapodik terakhir.</p>
        </div>
    </div>

</body>
</html>
