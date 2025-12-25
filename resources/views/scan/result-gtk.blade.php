<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil GTK - {{ $data->nama }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        .data-group { @apply mb-4; }
        .data-label { @apply text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1 block; }
        .data-value { @apply text-slate-700 font-medium text-sm sm:text-base border-b border-slate-100 pb-1 block; }

        .tab-btn.active { @apply text-indigo-600 border-b-2 border-indigo-600 bg-indigo-50/50; }
        .tab-btn { @apply px-4 py-3 text-sm font-medium text-slate-500 hover:text-indigo-500 transition-colors whitespace-nowrap; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen pb-10">

    <div class="relative bg-gradient-to-br from-indigo-700 to-purple-800 pb-20 pt-8 px-4 sm:px-8 overflow-hidden">
        <div class="absolute bottom-0 right-0 p-4 opacity-10">
            <i class="fas fa-chalkboard-teacher text-9xl text-white transform -rotate-12"></i>
        </div>

        <div class="relative z-10 max-w-5xl mx-auto flex flex-col sm:flex-row items-center gap-6">
            <div class="w-24 h-24 sm:w-28 sm:h-28 bg-white p-1 rounded-full shadow-lg flex-shrink-0">
                <div class="w-full h-full bg-slate-100 rounded-full flex items-center justify-center text-indigo-700 text-3xl font-bold border border-slate-200">
                    {{ substr($data->nama, 0, 1) }}
                </div>
            </div>
            <div class="text-center sm:text-left text-white">
                <h1 class="text-2xl sm:text-3xl font-bold mb-1">{{ $data->nama }}</h1>
                <p class="text-indigo-100 text-sm mb-3 opacity-90">
                     {{ $data->jenis_ptk_id_str ?? 'Pendidik/Tenaga Kependidikan' }}
                </p>
                <div class="flex flex-wrap justify-center sm:justify-start gap-2">
                    <span class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-medium border border-white/30">
                        NUPTK: {{ $data->nuptk ?? 'Belum Ada' }}
                    </span>
                    <span class="bg-teal-400 text-teal-900 px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                        <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 -mt-12 relative z-20">
        <div class="bg-white rounded-xl shadow-xl overflow-hidden min-h-[500px]">

            <div class="border-b border-slate-200 overflow-x-auto">
                <nav class="flex space-x-2 px-4">
                    <button onclick="switchTab('profil')" id="btn-profil" class="tab-btn active">
                        <i class="fas fa-user-tie mr-2"></i> Profil
                    </button>
                    <button onclick="switchTab('kepegawaian')" id="btn-kepegawaian" class="tab-btn">
                        <i class="fas fa-file-contract mr-2"></i> Kepegawaian
                    </button>
                    <button onclick="switchTab('alamat')" id="btn-alamat" class="tab-btn">
                        <i class="fas fa-map-marked-alt mr-2"></i> Alamat
                    </button>
                    <button onclick="switchTab('lainnya')" id="btn-lainnya" class="tab-btn">
                        <i class="fas fa-tasks mr-2"></i> Penugasan
                    </button>
                </nav>
            </div>

            <div class="p-6 sm:p-8 bg-white">

                <div id="tab-profil" class="tab-content block fade-in">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-l-4 border-indigo-500 pl-3">Identitas Pribadi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                        <div class="data-group"><span class="data-label">Nama Lengkap</span><span class="data-value">{{ $data->nama }}</span></div>
                        <div class="data-group"><span class="data-label">NIK</span><span class="data-value">{{ $data->nik ?? '-' }}</span></div>
                        <div class="data-group"><span class="data-label">Jenis Kelamin</span><span class="data-value">{{ ($data->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan' }}</span></div>
                        <div class="data-group"><span class="data-label">Tempat, Tgl Lahir</span><span class="data-value">{{ $data->tempat_lahir }}, {{ \Carbon\Carbon::parse($data->tanggal_lahir)->format('d M Y') }}</span></div>
                        <div class="data-group"><span class="data-label">Nama Ibu Kandung</span><span class="data-value">{{ $data->nama_ibu_kandung ?? '-' }}</span></div>
                        <div class="data-group"><span class="data-label">Status Perkawinan</span><span class="data-value">{{ $data->status_perkawinan_id_str ?? '-' }}</span></div>
                        <div class="data-group"><span class="data-label">Nama Suami/Istri</span><span class="data-value">{{ $data->nama_suami_istri ?? '-' }}</span></div>
                        <div class="data-group"><span class="data-label">Agama</span><span class="data-value">{{ $data->agama_id_str ?? '-' }}</span></div>
                         <div class="data-group"><span class="data-label">NPWP</span><span class="data-value">{{ $data->npwp ?? '-' }}</span></div>
                         <div class="data-group"><span class="data-label">Kewarganegaraan</span><span class="data-value">{{ $data->kewarganegaraan ?? 'Indonesia' }}</span></div>
                    </div>
                </div>

                <div id="tab-kepegawaian" class="tab-content hidden fade-in">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-l-4 border-indigo-500 pl-3">Data Kepegawaian</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                            <span class="text-xs font-bold text-indigo-500 uppercase">Status Kepegawaian</span>
                            <div class="text-lg font-bold text-slate-800 mt-1">{{ $data->status_kepegawaian_id_str ?? '-' }}</div>
                        </div>
                         <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                            <span class="text-xs font-bold text-indigo-500 uppercase">NIP</span>
                            <div class="text-lg font-bold text-slate-800 mt-1">{{ $data->nip ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                        <div class="data-group"><span class="data-label">NUPTK</span><span class="data-value">{{ $data->nuptk ?? '-' }}</span></div>
                        <div class="data-group"><span class="data-label">Jenis PTK</span><span class="data-value">{{ $data->jenis_ptk_id_str ?? '-' }}</span></div>
                        <div class="data-group"><span class="data-label">Lembaga Pengangkat</span><span class="data-value">{{ $data->lembaga_pengangkat_id_str ?? '-' }}</span></div>
                        <div class="data-group"><span class="data-label">Sumber Gaji</span><span class="data-value">{{ $data->sumber_gaji_id_str ?? '-' }}</span></div>
                        <div class="data-group"><span class="data-label">SK Pengangkatan</span><span class="data-value">{{ $data->sk_pengangkatan ?? '-' }}</span></div>
                        <div class="data-group"><span class="data-label">TMT Pengangkatan</span><span class="data-value">{{ $data->tmt_pengangkatan ?? '-' }}</span></div>
                        @if(!empty($data->sk_cpns))
                        <div class="data-group"><span class="data-label">SK CPNS</span><span class="data-value">{{ $data->sk_cpns }}</span></div>
                        <div class="data-group"><span class="data-label">TMT CPNS</span><span class="data-value">{{ $data->tmt_cpns }}</span></div>
                        @endif
                    </div>
                </div>

                <div id="tab-alamat" class="tab-content hidden fade-in">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-l-4 border-indigo-500 pl-3">Domisili & Kontak</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                        <div class="md:col-span-2 data-group">
                            <span class="data-label">Alamat Lengkap</span>
                            <span class="data-value">
                                {{ $data->alamat_jalan ?? '' }}
                                @if(!empty($data->rt)) RT {{ $data->rt }} @endif
                                @if(!empty($data->rw)) RW {{ $data->rw }} @endif
                                @if(!empty($data->nama_dusun)) ({{ $data->nama_dusun }}) @endif
                            </span>
                        </div>
                        <div class="data-group"><span class="data-label">Desa / Kelurahan</span><span class="data-value">{{ $data->desa_kelurahan ?? '-' }}</span></div>
                        <div class="data-group"><span class="data-label">Kecamatan</span><span class="data-value">{{ $data->kecamatan ?? '-' }}</span></div>
                        <div class="data-group"><span class="data-label">Kode Pos</span><span class="data-value">{{ $data->kode_pos ?? '-' }}</span></div>
                         <div class="data-group"><span class="data-label">Lintang / Bujur</span><span class="data-value font-mono text-xs">{{ $data->lintang ?? '-' }} / {{ $data->bujur ?? '-' }}</span></div>
                    </div>

                     <div class="mt-6 border-t border-slate-100 pt-4">
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center p-3 bg-indigo-50 rounded-lg">
                                <i class="fas fa-mobile-alt text-indigo-500 text-xl mr-3"></i>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">NO HP</p>
                                    <p class="font-medium">{{ $data->no_hp ?? $data->hp ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-indigo-50 rounded-lg">
                                <i class="fas fa-at text-indigo-500 text-xl mr-3"></i>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">EMAIL</p>
                                    <p class="font-medium lowercase">{{ $data->email ?? '-' }}</p>
                                </div>
                            </div>
                         </div>
                    </div>
                </div>

                <div id="tab-lainnya" class="tab-content hidden fade-in">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-l-4 border-indigo-500 pl-3">Data Penugasan & Kompetensi</h3>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center mb-6">
                        <div class="bg-slate-50 p-3 rounded border">
                            <p class="text-xs text-slate-500">Lisensi Kepsek</p>
                            <p class="font-bold {{ ($data->sudah_lisensi_kepala_sekolah ?? 0) == 1 ? 'text-green-600' : 'text-slate-400' }}">
                                {{ ($data->sudah_lisensi_kepala_sekolah ?? 0) == 1 ? 'YA' : 'TIDAK' }}
                            </p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded border">
                            <p class="text-xs text-slate-500">Diklat Pengawas</p>
                            <p class="font-bold {{ ($data->pernah_diklat_kepengawasan ?? 0) == 1 ? 'text-green-600' : 'text-slate-400' }}">
                                {{ ($data->pernah_diklat_kepengawasan ?? 0) == 1 ? 'YA' : 'TIDAK' }}
                            </p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded border">
                            <p class="text-xs text-slate-500">Braille</p>
                            <p class="font-bold {{ ($data->keahlian_braille ?? 0) == 1 ? 'text-green-600' : 'text-slate-400' }}">
                                {{ ($data->keahlian_braille ?? 0) == 1 ? 'YA' : 'TIDAK' }}
                            </p>
                        </div>
                         <div class="bg-slate-50 p-3 rounded border">
                            <p class="text-xs text-slate-500">Bahasa Isyarat</p>
                            <p class="font-bold {{ ($data->keahlian_bahasa_isyarat ?? 0) == 1 ? 'text-green-600' : 'text-slate-400' }}">
                                {{ ($data->keahlian_bahasa_isyarat ?? 0) == 1 ? 'YA' : 'TIDAK' }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                         <h4 class="text-sm font-bold text-yellow-700 mb-2"><i class="fas fa-exclamation-triangle mr-1"></i> Status Keaktifan</h4>
                         <p class="text-sm text-slate-700">Pastikan data penugasan di Dapodik selalu diperbarui setiap semester untuk memastikan validitas Tunjangan Profesi Pendidik.</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="text-center mt-6 text-slate-400 text-xs">
            &copy; {{ date('Y') }} Sistem Informasi Sekolah. Data Sinkronisasi Dapodik.
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById('btn-' + tabId).classList.add('active');
        }
    </script>
</body>
</html>
