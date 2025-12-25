<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil GTK - {{ $data->nama }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .data-label { @apply text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1; }
        .data-value { @apply text-slate-800 font-medium text-sm sm:text-base border-b border-slate-100 pb-2; }
        .section-title { @apply text-indigo-600 font-bold text-lg mb-4 flex items-center border-b-2 border-indigo-100 pb-2; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-6 px-4 sm:px-6">

    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">

        <div class="relative bg-gradient-to-r from-indigo-700 to-purple-800 p-8 text-white">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 relative z-10">
                <div class="flex-shrink-0">
                    <div class="w-24 h-24 bg-white text-indigo-700 rounded-full flex items-center justify-center text-3xl font-bold shadow-lg border-4 border-indigo-300">
                        {{ substr($data->nama, 0, 1) }}
                    </div>
                </div>

                <div class="text-center sm:text-left flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold mb-2">{{ $data->nama }}</h1>
                    <div class="flex flex-wrap justify-center sm:justify-start gap-3 text-sm text-indigo-100">
                        <span class="bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                            <i class="fas fa-briefcase mr-1"></i> {{ $data->jenis_ptk_id_str ?? 'Tenaga Pendidik' }}
                        </span>
                        <span class="bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                            <i class="fas fa-id-badge mr-1"></i> {{ $data->status_kepegawaian_id_str ?? '-' }}
                        </span>
                    </div>
                </div>

                <div class="absolute top-4 right-4 sm:static">
                    <span class="bg-teal-400 text-teal-900 text-xs font-bold px-3 py-1 rounded shadow">
                        VERIFIED <i class="fas fa-check-circle ml-1"></i>
                    </span>
                </div>
            </div>

            <div class="absolute bottom-0 right-0 opacity-10">
                <i class="fas fa-chalkboard-teacher text-9xl transform -rotate-12 translate-x-4 translate-y-4"></i>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-8">

            <section>
                <h3 class="section-title"><i class="fas fa-user-tie mr-2"></i> Identitas Pribadi</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <p class="data-label">Nama Lengkap (Tanpa Gelar)</p>
                        <p class="data-value">{{ $data->nama }}</p>
                    </div>
                    <div>
                        <p class="data-label">Jenis Kelamin</p>
                        <p class="data-value">{{ ($data->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Tempat, Tanggal Lahir</p>
                        <p class="data-value">{{ $data->tempat_lahir }}, {{ $data->tanggal_lahir }}</p>
                    </div>
                    <div>
                        <p class="data-label">NIK</p>
                        <p class="data-value">{{ $data->nik ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Agama</p>
                        <p class="data-value">{{ $data->agama_id_str ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Nama Ibu Kandung</p>
                        <p class="data-value">{{ $data->nama_ibu_kandung ?? '-' }}</p>
                    </div>
                     <div>
                        <p class="data-label">Status Perkawinan</p>
                        <p class="data-value">{{ $data->status_perkawinan_id_str ?? '-' }}</p>
                    </div>
                    @if(!empty($data->nama_suami_istri))
                    <div>
                        <p class="data-label">Nama Suami/Istri</p>
                        <p class="data-value">{{ $data->nama_suami_istri }}</p>
                    </div>
                    @endif
                </div>
            </section>

            <section>
                <h3 class="section-title"><i class="fas fa-file-contract mr-2"></i> Data Kepegawaian</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                    <div class="bg-indigo-50 p-3 rounded border border-indigo-100">
                        <p class="data-label text-indigo-500">NUPTK</p>
                        <p class="text-lg font-bold text-indigo-900">{{ $data->nuptk ?? 'Belum Ada' }}</p>
                    </div>
                    <div class="bg-indigo-50 p-3 rounded border border-indigo-100">
                        <p class="data-label text-indigo-500">NIP</p>
                        <p class="text-lg font-bold text-indigo-900">{{ $data->nip ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="data-label">Jenis PTK</p>
                        <p class="data-value">{{ $data->jenis_ptk_id_str ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Status Kepegawaian</p>
                        <p class="data-value">{{ $data->status_kepegawaian_id_str ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Lembaga Pengangkat</p>
                        <p class="data-value">{{ $data->lembaga_pengangkat_id_str ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="data-label">Sumber Gaji</p>
                        <p class="data-value">{{ $data->sumber_gaji_id_str ?? '-' }}</p>
                    </div>

                    @if(!empty($data->sk_pengangkatan))
                    <div>
                        <p class="data-label">SK Pengangkatan</p>
                        <p class="data-value">{{ $data->sk_pengangkatan }}</p>
                    </div>
                    <div>
                        <p class="data-label">TMT Pengangkatan</p>
                        <p class="data-value">{{ $data->tmt_pengangkatan ?? '-' }}</p>
                    </div>
                    @endif

                    @if(!empty($data->sk_cpns))
                    <div>
                        <p class="data-label">SK CPNS</p>
                        <p class="data-value">{{ $data->sk_cpns }} (TMT: {{ $data->tmt_cpns }})</p>
                    </div>
                    @endif
                </div>
            </section>

            <section>
                <h3 class="section-title"><i class="fas fa-map-marked-alt mr-2"></i> Alamat & Kontak</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                    <div class="sm:col-span-2">
                        <p class="data-label">Alamat Lengkap</p>
                        <p class="data-value">
                            {{ $data->alamat_jalan ?? '' }}
                            @if(!empty($data->rt) || !empty($data->rw))
                                RT {{ $data->rt }}/RW {{ $data->rw }}
                            @endif
                            @if(!empty($data->nama_dusun))
                                (Dusun {{ $data->nama_dusun }})
                            @endif
                        </p>
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
                        <p class="data-label">Lintang / Bujur</p>
                        <p class="data-value text-xs font-mono">
                            {{ $data->lintang ?? '-' }} / {{ $data->bujur ?? '-' }}
                        </p>
                    </div>

                    <div class="mt-4 sm:col-span-2 pt-4 border-t border-dashed border-slate-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="data-label">No. HP / Telepon</p>
                                <p class="data-value font-bold text-slate-700">
                                    <i class="fas fa-phone-alt text-indigo-400 mr-2"></i>
                                    {{ $data->no_hp ?? $data->hp ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="data-label">Email</p>
                                <p class="data-value lowercase text-indigo-600">
                                    <i class="fas fa-envelope text-indigo-400 mr-2"></i>
                                    {{ $data->email ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                <h3 class="text-slate-700 font-bold mb-3 text-base flex items-center">
                    <i class="fas fa-info-circle mr-2 text-indigo-500"></i> Info Tambahan
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                    <div>
                        <p class="text-xs text-slate-500">Lisensi Kepsek</p>
                        <p class="font-bold text-slate-800">{{ ($data->sudah_lisensi_kepala_sekolah ?? 0) == 1 ? 'Ya' : 'Tidak' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Keahlian Braille</p>
                        <p class="font-bold text-slate-800">{{ ($data->keahlian_braille ?? 0) == 1 ? 'Ya' : 'Tidak' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Bahasa Isyarat</p>
                        <p class="font-bold text-slate-800">{{ ($data->keahlian_bahasa_isyarat ?? 0) == 1 ? 'Ya' : 'Tidak' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Kewarganegaraan</p>
                        <p class="font-bold text-slate-800">{{ $data->kewarganegaraan ?? 'ID' }}</p>
                    </div>
                </div>
            </section>

        </div>

        <div class="bg-slate-800 text-white text-center py-4 text-xs">
             <p>Data GTK diambil dari sinkronisasi Dapodik terakhir.</p>
        </div>
    </div>

</body>
</html>
