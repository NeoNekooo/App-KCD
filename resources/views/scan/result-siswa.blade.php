<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Siswa - {{ $data->nama }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Custom Utilities */
        .data-group { @apply mb-4; }
        .data-label { @apply text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1 block; }
        .data-value { @apply text-slate-700 font-medium text-sm sm:text-base border-b border-slate-100 pb-1 block; }

        /* Tab Active State */
        .tab-btn.active { @apply text-blue-600 border-b-2 border-blue-600 bg-blue-50/50; }
        .tab-btn { @apply px-4 py-3 text-sm font-medium text-slate-500 hover:text-blue-500 transition-colors whitespace-nowrap; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen pb-10">

    <div class="relative bg-gradient-to-br from-blue-600 to-cyan-600 pb-20 pt-8 px-4 sm:px-8 overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10">
            <i class="fas fa-graduation-cap text-9xl text-white transform rotate-12"></i>
        </div>

        <div class="relative z-10 max-w-5xl mx-auto flex flex-col sm:flex-row items-center gap-6">
            <div class="w-24 h-24 sm:w-28 sm:h-28 bg-white p-1 rounded-full shadow-lg flex-shrink-0">
                <div class="w-full h-full bg-slate-100 rounded-full flex items-center justify-center text-blue-600 text-3xl font-bold border border-slate-200">
                    {{ substr($data->nama, 0, 1) }}
                </div>
            </div>
            <div class="text-center sm:text-left text-white">
                <h1 class="text-2xl sm:text-3xl font-bold mb-1">{{ $data->nama }}</h1>
                <p class="text-blue-100 text-sm mb-3 opacity-90">
                    {{ $data->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }} &bull; {{ $data->tempat_lahir }}, {{ \Carbon\Carbon::parse($data->tanggal_lahir)->format('d M Y') }}
                </p>
                <div class="flex flex-wrap justify-center sm:justify-start gap-2">
                    <span class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-medium border border-white/30">
                        NISN: {{ $data->nisn ?? '-' }}
                    </span>
                    <span class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-medium border border-white/30">
                        NIPD: {{ $data->nipd ?? '-' }}
                    </span>
                    <span class="bg-green-400 text-green-900 px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                        <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 -mt-12 relative z-20">
        <div class="bg-white rounded-xl shadow-xl overflow-hidden min-h-[500px]">

            <div class="border-b border-slate-200 overflow-x-auto">
                <nav class="flex space-x-2 px-4" aria-label="Tabs">
                    <button onclick="switchTab('profil')" id="btn-profil" class="tab-btn active">
                        <i class="fas fa-user-circle mr-2"></i> Profil & Fisik
                    </button>
                    <button onclick="switchTab('alamat')" id="btn-alamat" class="tab-btn">
                        <i class="fas fa-map-marker-alt mr-2"></i> Alamat & Kontak
                    </button>
                    <button onclick="switchTab('keluarga')" id="btn-keluarga" class="tab-btn">
                        <i class="fas fa-users mr-2"></i> Orang Tua
                    </button>
                    <button onclick="switchTab('lainnya')" id="btn-lainnya" class="tab-btn">
                        <i class="fas fa-star mr-2"></i> KIP & Lainnya
                    </button>
                </nav>
            </div>

            <div class="p-6 sm:p-8 bg-white">

                <div id="tab-profil" class="tab-content block fade-in">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-l-4 border-blue-500 pl-3">Identitas & Data Fisik</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                        <div class="data-group">
                            <span class="data-label">Nama Lengkap</span>
                            <span class="data-value">{{ $data->nama }}</span>
                        </div>
                        <div class="data-group">
                            <span class="data-label">NIK</span>
                            <span class="data-value">{{ $data->nik ?? '-' }}</span>
                        </div>
                        <div class="data-group">
                            <span class="data-label">Agama</span>
                            <span class="data-value">{{ $data->agama_id_str ?? '-' }}</span>
                        </div>
                        <div class="data-group">
                            <span class="data-label">No. Registrasi Akta</span>
                            <span class="data-value">{{ $data->no_registrasi_akta_lahir ?? '-' }}</span>
                        </div>
                        <div class="data-group">
                            <span class="data-label">Kebutuhan Khusus</span>
                            <span class="data-value">{{ $data->kebutuhan_khusus_id_str ?? 'Tidak Ada' }}</span>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-slate-50 rounded-lg border border-slate-100">
                        <h4 class="text-sm font-bold text-slate-500 mb-3 uppercase">Data Periodik (Fisik)</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div><div class="text-2xl font-bold text-blue-600">{{ $data->tinggi_badan ?? '-' }}</div><div class="text-xs text-slate-400">Tinggi (cm)</div></div>
                            <div><div class="text-2xl font-bold text-blue-600">{{ $data->berat_badan ?? '-' }}</div><div class="text-xs text-slate-400">Berat (kg)</div></div>
                            <div><div class="text-2xl font-bold text-blue-600">{{ $data->lingkar_kepala ?? '-' }}</div><div class="text-xs text-slate-400">Lingkar Kepala (cm)</div></div>
                            <div><div class="text-2xl font-bold text-blue-600">{{ $data->jumlah_saudara_kandung ?? '-' }}</div><div class="text-xs text-slate-400">Saudara</div></div>
                        </div>
                    </div>
                </div>

                <div id="tab-alamat" class="tab-content hidden fade-in">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-l-4 border-blue-500 pl-3">Domisili & Kontak</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                        <div class="md:col-span-2 data-group">
                            <span class="data-label">Alamat Jalan</span>
                            <span class="data-value">{{ $data->alamat_jalan ?? '-' }}</span>
                        </div>
                        <div class="data-group">
                            <span class="data-label">RT / RW</span>
                            <span class="data-value">{{ $data->rt ?? '0' }} / {{ $data->rw ?? '0' }}</span>
                        </div>
                        <div class="data-group">
                            <span class="data-label">Dusun</span>
                            <span class="data-value">{{ $data->dusun ?? '-' }}</span>
                        </div>
                        <div class="data-group">
                            <span class="data-label">Kelurahan / Desa</span>
                            <span class="data-value">{{ $data->desa_kelurahan ?? '-' }}</span>
                        </div>
                        <div class="data-group">
                            <span class="data-label">Kecamatan</span>
                            <span class="data-value">{{ $data->kecamatan ?? '-' }}</span>
                        </div>
                        <div class="data-group">
                            <span class="data-label">Kode Pos</span>
                            <span class="data-value">{{ $data->kode_pos ?? '-' }}</span>
                        </div>
                        <div class="data-group">
                            <span class="data-label">Jenis Tinggal</span>
                            <span class="data-value">{{ $data->jenis_tinggal_id_str ?? '-' }}</span>
                        </div>
                        <div class="data-group">
                            <span class="data-label">Transportasi</span>
                            <span class="data-value">{{ $data->alat_transportasi_id_str ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-slate-100 pt-4">
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                <i class="fas fa-phone-alt text-blue-500 text-xl mr-3"></i>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">TELEPON / HP</p>
                                    <p class="font-medium">{{ $data->hp ?? $data->telepon ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                <i class="fas fa-envelope text-blue-500 text-xl mr-3"></i>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">EMAIL</p>
                                    <p class="font-medium lowercase">{{ $data->email ?? '-' }}</p>
                                </div>
                            </div>
                         </div>
                    </div>
                </div>

                <div id="tab-keluarga" class="tab-content hidden fade-in">

                    <div class="mb-6">
                        <h3 class="text-base font-bold text-slate-700 mb-3 flex items-center">
                            <span class="bg-blue-100 text-blue-600 py-1 px-2 rounded text-xs mr-2">AYAH</span> Data Ayah Kandung
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 pl-2 border-l-2 border-slate-200">
                            <div class="data-group"><span class="data-label">Nama Ayah</span><span class="data-value">{{ $data->nama_ayah ?? '-' }}</span></div>
                            <div class="data-group"><span class="data-label">NIK Ayah</span><span class="data-value">{{ $data->nik_ayah ?? '-' }}</span></div>
                            <div class="data-group"><span class="data-label">Tahun Lahir</span><span class="data-value">{{ $data->tahun_lahir_ayah ?? '-' }}</span></div>
                            <div class="data-group"><span class="data-label">Pendidikan</span><span class="data-value">{{ $data->jenjang_pendidikan_ayah_id_str ?? '-' }}</span></div>
                            <div class="data-group"><span class="data-label">Pekerjaan</span><span class="data-value">{{ $data->pekerjaan_ayah_id_str ?? '-' }}</span></div>
                            <div class="data-group"><span class="data-label">Penghasilan</span><span class="data-value">{{ $data->penghasilan_ayah_id_str ?? '-' }}</span></div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-base font-bold text-slate-700 mb-3 flex items-center">
                             <span class="bg-pink-100 text-pink-600 py-1 px-2 rounded text-xs mr-2">IBU</span> Data Ibu Kandung
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 pl-2 border-l-2 border-slate-200">
                            <div class="data-group"><span class="data-label">Nama Ibu</span><span class="data-value">{{ $data->nama_ibu_kandung ?? '-' }}</span></div>
                            <div class="data-group"><span class="data-label">NIK Ibu</span><span class="data-value">{{ $data->nik_ibu ?? '-' }}</span></div>
                            <div class="data-group"><span class="data-label">Tahun Lahir</span><span class="data-value">{{ $data->tahun_lahir_ibu ?? '-' }}</span></div>
                            <div class="data-group"><span class="data-label">Pendidikan</span><span class="data-value">{{ $data->jenjang_pendidikan_ibu_id_str ?? '-' }}</span></div>
                            <div class="data-group"><span class="data-label">Pekerjaan</span><span class="data-value">{{ $data->pekerjaan_ibu_id_str ?? '-' }}</span></div>
                            <div class="data-group"><span class="data-label">Penghasilan</span><span class="data-value">{{ $data->penghasilan_ibu_id_str ?? '-' }}</span></div>
                        </div>
                    </div>
                </div>

                <div id="tab-lainnya" class="tab-content hidden fade-in">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-l-4 border-yellow-500 pl-3">Kesejahteraan & Lainnya</h3>

                    @if(($data->penerima_kps ?? '0') == '1' || ($data->penerima_kip ?? '0') == '1')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="data-label text-yellow-600">Penerima KIP (Indonesia Pintar)</span>
                                <span class="text-lg font-bold text-slate-800">{{ ($data->penerima_kip == '1') ? 'Ya' : 'Tidak' }}</span>
                            </div>
                             <div>
                                <span class="data-label text-yellow-600">Nomor KIP</span>
                                <span class="text-lg font-bold text-slate-800">{{ $data->nomor_kip ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="data-label text-yellow-600">Penerima KPS</span>
                                <span class="text-lg font-bold text-slate-800">{{ ($data->penerima_kps == '1') ? 'Ya' : 'Tidak' }}</span>
                            </div>
                            <div>
                                <span class="data-label text-yellow-600">No KPS</span>
                                <span class="text-lg font-bold text-slate-800">{{ $data->no_kps ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-8 text-slate-400 bg-slate-50 rounded-lg border border-dashed border-slate-200">
                        <i class="fas fa-info-circle text-2xl mb-2"></i>
                        <p>Tidak ada data KIP atau KPS yang tercatat.</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="data-group"><span class="data-label">Bank</span><span class="data-value">{{ $data->bank ?? '-' }}</span></div>
                        <div class="data-group"><span class="data-label">No Rekening</span><span class="data-value">{{ $data->nomor_rekening_bank ?? '-' }}</span></div>
                        <div class="data-group"><span class="data-label">Atas Nama</span><span class="data-value">{{ $data->rekening_atas_nama ?? '-' }}</span></div>
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
            // Hide all content
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.add('hidden');
            });
            // Show target content
            document.getElementById('tab-' + tabId).classList.remove('hidden');

            // Reset nav buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            // Active nav button
            document.getElementById('btn-' + tabId).classList.add('active');
        }
    </script>
</body>
</html>
