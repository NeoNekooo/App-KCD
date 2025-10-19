<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PPDB SMAKNIS - Pendaftaran Siswa Baru</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://unpkg.com/lucide@latest"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                        },
                        colors: {
                            'primary-blue': '#1e3a8a', // Warna biru tua SMAKNIS
                            'secondary-green': '#10b981', // Warna hijau cerah untuk sukses
                            'neutral-light': '#f3f4f6', // Latar belakang ringan
                            'footer-dark': '#0f172a', // Latar belakang footer yang gelap
                        }
                    }
                }
            }
        </script>
        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f7f9fc;
                /* Lighter background */
            }

            /* Style untuk Landing Page Sections */
            .shadow-custom {
                box-shadow: 0 10px 15px -3px rgba(30, 64, 175, 0.2), 0 4px 6px -2px rgba(30, 64, 175, 0.1);
            }

            /* Style Form Progresif (Progress Bar & Circles) */
            #progressBar {
                transition: width 0.5s ease-in-out;
            }

            .step-circle {
                transition: all 0.3s ease;
                box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.7);
                /* Efek cincin putih */
            }

            /* Style untuk notifikasi kustom */
            #notification {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1000;
                transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
                transform: translateY(-100px);
                opacity: 0;
            }

            #notification.show {
                transform: translateY(0);
                opacity: 1;
            }

            /* Style untuk Input Error (Validasi Form) */
            .border-red-500 {
                border-color: #ef4444 !important;
            }
            .text-red-500 {
                color: #ef4444 !important;
            }
            .ring-red-500\/50 {
                box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.5);
            }

            /* Style untuk Map Container agar iframe mengisi penuh */
            .map-iframe-container {
                /* Pastikan container peta di CSS tidak mengganggu elemen lain */
                border-radius: 0.75rem;
                /* rounded-xl */
            }

            /* --- Custom CSS untuk Efek Hover Sosial Media di Footer --- */
            .social-link {
                transition: color 0.3s ease-in-out, transform 0.2s ease;
            }

            .social-link:hover {
                transform: translateY(-2px);
                /* Efek angkat sedikit */
            }

            .social-link.facebook:hover {
                color: #1877F2;
                /* Warna Facebook */
            }

            .social-link.instagram:hover {
                /* Warna khas gradien Instagram */
                color: #E1306C;
            }

            .social-link.youtube:hover {
                color: #FF0000;
                /* Warna YouTube */
            }

            /* CUSTOM CSS untuk Active Link */
            /* Link container di desktop agar bisa menampung border bawah */
            .nav-link-container {
                position: relative;
                height: 100%;
                /* Penting agar border tidak terpotong */
                display: flex;
                align-items: center;
            }

            /* Class untuk tautan aktif di desktop */
            .nav-link-desktop-active {
                color: #10b981 !important;
                /* secondary-green */
                font-weight: 700 !important;
                /* font-bold */
                border-bottom-width: 2px !important;
                border-color: #10b981 !important;
                padding-bottom: 12px;
                /* Sesuaikan dengan padding header */
            }

            /* Class untuk tautan aktif di mobile (menggunakan border-l) */
            .nav-link-mobile-active {
                color: #1e3a8a !important;
                /* primary-blue */
                font-weight: 700 !important;
                /* font-bold */
                border-left-width: 4px !important;
                border-color: #10b981 !important;
                /* secondary-green */
                background-color: #f0fdf4;
                /* bg-green-50 */
            }
        </style>
    </head>

    <body class="antialiased">

        <div id="notification" class="text-white p-4 rounded-lg shadow-xl flex items-center space-x-3 max-w-sm">
            <i data-lucide="check-circle" class="w-6 h-6"></i>
            <span id="notification-message">Pendaftaran berhasil!</span>
        </div>

        <header class="bg-white shadow-md sticky top-0 z-50">
            </header>

        <section id="beranda" class="bg-primary-blue pt-16 pb-24 sm:pt-24 sm:pb-32 relative overflow-hidden">
            </section>

        <section id="daftar-siswa-list" class="py-20 bg-neutral-light">
            </section>


        <section id="program" class="py-20 bg-white">
            </section>

        <section id="alur" class="py-20 bg-gray-100">
            </section>

        <section id="jurusan" class="py-20 bg-gray-50">
            </section>

        <section id="daftar" class="py-20 bg-gray-100">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" data-aos="fade-up" data-aos-delay="100"
                data-aos-duration="800">

                <div class="w-full bg-white shadow-2xl rounded-xl p-6 sm:p-10 border border-gray-100">

                    <h1 class="text-3xl font-extrabold text-primary-blue mb-2">Pendaftaran Calon Siswa</h1>
                    <p class="text-gray-500 mb-8">Lengkapi semua langkah untuk menyelesaikan proses pendaftaran Anda.
                    </p>

                    <div class="relative mb-12">
                        <div
                            class="absolute w-full h-1 bg-neutral-light top-1/2 transform -translate-y-1/2 rounded-full">
                        </div>
                        <div id="progressBar"
                            class="absolute h-1 bg-secondary-green top-1/2 transform -translate-y-1/2 rounded-full w-0"
                            style="width: 0%;"></div>
                        <div id="stepIndicators" class="relative flex justify-between">
                            </div>
                    </div>

                    <form id="ppdb-form" class="space-y-6" method="POST" action="{{route('submitForm')}}" enctype="multipart/form-data">
                        @csrf

                        <div id="formContent" class="min-h-[350px] bg-neutral-light p-6 rounded-lg shadow-inner">

                            <div id="step-1" class="form-step space-y-6">
                                <h2 class="text-2xl font-bold text-primary-blue mb-4">Langkah 1: Data Diri</h2>
                                <p class="text-gray-700 mb-6">Silakan isi informasi pribadi Anda seperti Nama, Tempat/Tanggal Lahir, Kontak, dan Alamat Domisili.</p>
                                
                                @php
                                    // Helper class untuk input
                                    $inputClasses = 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary-blue focus:border-primary-blue transition duration-200';
                                    $errorClasses = 'border-red-500 ring-2 ring-red-500/50';
                                @endphp

                                <div>
                                    <label for="nama-lengkap" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap Calon Siswa <span class="text-red-500">*</span></label>
                                    <input type="text" id="nama-lengkap" name="nama-lengkap" value="{{ old('nama-lengkap') }}" required
                                        class="{{ $inputClasses }} @error('nama-lengkap') {{ $errorClasses }} @enderror" placeholder="Masukkan nama lengkap siswa">
                                    @error('nama-lengkap') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="tempat-lahir" class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                                        <input type="text" id="tempat-lahir" name="tempat-lahir" value="{{ old('tempat-lahir') }}" required
                                            class="{{ $inputClasses }} @error('tempat-lahir') {{ $errorClasses }} @enderror" placeholder="Contoh: Jakarta">
                                        @error('tempat-lahir') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="tanggal-lahir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                                        <input type="date" id="tanggal-lahir" name="tanggal-lahir" value="{{ old('tanggal-lahir') }}" required
                                            class="{{ $inputClasses }} @error('tanggal-lahir') {{ $errorClasses }} @enderror">
                                        @error('tanggal-lahir') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap (Domisili) <span class="text-red-500">*</span></label>
                                    <textarea id="alamat" name="alamat" rows="3" required
                                        class="{{ $inputClasses }} @error('alamat') {{ $errorClasses }} @enderror" placeholder="Masukkan alamat lengkap siswa saat ini">{{ old('alamat') }}</textarea>
                                    @error('alamat') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="jenis-kelamin" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                                        <select id="jenis-kelamin" name="jenis-kelamin" required
                                                class="{{ $inputClasses }} appearance-none bg-white @error('jenis-kelamin') {{ $errorClasses }} @enderror">
                                            <option value="">-- Pilih Jenis Kelamin --</option>
                                            <option value="Laki-laki" {{ old('jenis-kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('jenis-kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('jenis-kelamin') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="nisn" class="block text-sm font-medium text-gray-700 mb-2">NISN <span class="text-red-500">*</span></label>
                                        <input type="number" id="nisn" name="nisn" value="{{ old('nisn') }}" required
                                            class="{{ $inputClasses }} @error('nisn') {{ $errorClasses }} @enderror" placeholder="Nomor Induk Siswa Nasional (10 Digit)">
                                        @error('nisn') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label for="kontak-wa" class="block text-sm font-medium text-gray-700 mb-2">Nomor HP/WhatsApp (Aktif) <span class="text-red-500">*</span></label>
                                    <input type="tel" id="kontak-wa" name="kontak-wa" value="{{ old('kontak-wa') }}" required
                                        class="{{ $inputClasses }} @error('kontak-wa') {{ $errorClasses }} @enderror" placeholder="Contoh: 081234567890">
                                    @error('kontak-wa') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <p class="text-sm text-gray-500 pt-2 border-t border-gray-200">Data Orang Tua (Opsional, disarankan):</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="nama-ayah" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap Ayah</label>
                                        <input type="text" id="nama-ayah" name="nama-ayah" value="{{ old('nama-ayah') }}"
                                            class="{{ $inputClasses }} @error('nama-ayah') {{ $errorClasses }} @enderror" placeholder="Nama lengkap ayah/wali">
                                        @error('nama-ayah') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="nama-ibu" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap Ibu</label>
                                        <input type="text" id="nama-ibu" name="nama-ibu" value="{{ old('nama-ibu') }}"
                                            class="{{ $inputClasses }} @error('nama-ibu') {{ $errorClasses }} @enderror" placeholder="Nama lengkap ibu/wali">
                                        @error('nama-ibu') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div id="step-2" class="form-step space-y-6 hidden">
                                <h2 class="text-2xl font-bold text-primary-blue mb-4">Langkah 2: Data Sekolah</h2>
                                <p class="text-gray-700 mb-6">Masukkan informasi asal sekolah dan detail pendaftaran awal Anda.</p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="asal-sekolah" class="block text-sm font-medium text-gray-700 mb-2">Asal Sekolah <span class="text-red-500">*</span></label>
                                        <input type="text" id="asal-sekolah" name="asal-sekolah" value="{{ old('asal-sekolah') }}" required
                                            class="{{ $inputClasses }} @error('asal-sekolah') {{ $errorClasses }} @enderror" placeholder="Contoh: SMP Negeri 1">
                                        @error('asal-sekolah') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas Terakhir/Saat Ini <span class="text-red-500">*</span></label>
                                        <select id="kelas" name="kelas" required
                                                class="{{ $inputClasses }} appearance-none bg-white @error('kelas') {{ $errorClasses }} @enderror">
                                            <option value="">-- Pilih Kelas Terakhir --</option>
                                            @foreach (['IX A', 'IX B', 'IX C', 'IX D', 'IX E', 'IX F'] as $kelas)
                                                <option value="{{ $kelas }}" {{ old('kelas') == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                                            @endforeach
                                        </select>
                                        @error('kelas') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="ukuran-baju" class="block text-sm font-medium text-gray-700 mb-2">Ukuran Baju Seragam <span class="text-red-500">*</span></label>
                                        <select id="ukuran-baju" name="ukuran-baju" required
                                                class="{{ $inputClasses }} appearance-none bg-white @error('ukuran-baju') {{ $errorClasses }} @enderror">
                                            <option value="">-- Pilih Ukuran Baju --</option>
                                            @foreach (['S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $size)
                                                <option value="{{ $size }}" {{ old('ukuran-baju') == $size ? 'selected' : '' }}>{{ $size }}</option>
                                            @endforeach
                                        </select>
                                        @error('ukuran-baju') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="jalur-pendaftaran" class="block text-sm font-medium text-gray-700 mb-2">Jalur Pendaftaran <span class="text-red-500">*</span></label>
                                        <select id="jalur-pendaftaran" name="jalur-pendaftaran" required
                                                class="{{ $inputClasses }} appearance-none bg-white @error('jalur-pendaftaran') {{ $errorClasses }} @enderror">
                                            <option value="">-- Pilih Jalur Pendaftaran --</option>
                                            <option value="Reguler" {{ old('jalur-pendaftaran') == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                                            <option value="Prestasi" {{ old('jalur-pendaftaran') == 'Prestasi' ? 'selected' : '' }}>Prestasi (Rapor/Akademik/Non-Akademik)</option>
                                            <option value="Afirmasi" {{ old('jalur-pendaftaran') == 'Afirmasi' ? 'selected' : '' }}>Afirmasi (KIP/KKS)</option>
                                            <option value="Perpindahan" {{ old('jalur-pendaftaran') == 'Perpindahan' ? 'selected' : '' }}>Perpindahan Tugas Orang Tua</option>
                                        </select>
                                        @error('jalur-pendaftaran') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div id="step-3" class="form-step space-y-6 hidden">
                                <h2 class="text-2xl font-bold text-primary-blue mb-4">Langkah 3: Pilih Jurusan</h2>
                                <p class="text-gray-700 mb-6">Pilih satu kompetensi keahlian yang paling Anda minati di SMAKNIS.</p>

                                <div>
                                    <label for="jurusan-minat" class="block text-sm font-medium text-gray-700 mb-2">Pilihan Utama Jurusan <span class="text-red-500">*</span></label>
                                    <select id="jurusan-minat" name="jurusan-minat" required
                                            class="{{ $inputClasses }} appearance-none bg-white @error('jurusan-minat') {{ $errorClasses }} @enderror">
                                        <option value="">-- Pilih Jurusan Pilihan Utama --</option>
                                        @php
                                            $jurusanOptions = [
                                                "PPLG" => "PPLG - Pengembangan Perangkat Lunak dan GIM",
                                                "TJKT" => "TJKT - Teknik Jaringan Komputer dan Telekomunikasi",
                                                "AKL" => "AKL - Akuntansi dan Keuangan Lembaga",
                                                "DKV" => "DKV - Desain Komunikasi Visual",
                                                "MPLB" => "MPLB - Manajemen Perkantoran dan Layanan Bisnis",
                                                "Otomotif" => "Otomotif - Teknik Otomotif"
                                            ];
                                        @endphp
                                        @foreach ($jurusanOptions as $value => $text)
                                            <option value="{{ $value }}" {{ old('jurusan-minat') == $value ? 'selected' : '' }}>{{ $text }}</option>
                                        @endforeach
                                    </select>
                                    @error('jurusan-minat') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <p class="text-sm text-gray-500 pt-2 border-t border-gray-200">Memilih jurusan yang sesuai minat akan meningkatkan peluang karir Anda setelah lulus.</p>
                            </div>

                            <div id="step-4" class="form-step space-y-6 hidden">
                                <h2 class="text-2xl font-bold text-primary-blue mb-4">Langkah 4: Upload Persyaratan</h2>
                                <p class="text-gray-700 mb-6">Unggah semua dokumen persyaratan pendaftaran. Dokumen wajib adalah Kartu Keluarga.</p>

                                <div class="p-4 border border-dashed border-gray-300 rounded-lg bg-gray-50 space-y-4">
                                    <p class="text-sm text-gray-700 font-semibold">
                                        Dokumen wajib ditandai <span class="text-red-500">*</span>. Ukuran maksimum per file adalah 1MB (Format PDF atau Gambar).
                                    </p>
                                    <div>
                                        <label for="upload-kk" class="block text-sm font-medium text-gray-700 mb-2">Unggah Scan Kartu Keluarga <span class="text-red-500">*</span></label>
                                        <input type="file" id="upload-kk" name="upload-kk" required accept=".pdf,image/*"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white @error('upload-kk') {{ $errorClasses }} @enderror">
                                        @error('upload-kk') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="upload-rapor" class="block text-sm font-medium text-gray-700 mb-2">Unggah Foto Rapor SMP/MTS (Semester Akhir)</label>
                                        <input type="file" id="upload-rapor" name="upload-rapor" accept=".pdf,image/*"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white @error('upload-rapor') {{ $errorClasses }} @enderror">
                                        @error('upload-rapor') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="upload-sertifikat" class="block text-sm font-medium text-gray-700 mb-2">Unggah Sertifikat Prestasi (Jika ada)</label>
                                        <input type="file" id="upload-sertifikat" name="upload-sertifikat" accept=".pdf,image/*"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white @error('upload-sertifikat') {{ $errorClasses }} @enderror">
                                        @error('upload-sertifikat') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div id="step-5" class="form-step hidden">
                                <h2 class="text-2xl font-bold text-primary-blue mb-4">Langkah 5: Selesai</h2>
                                <p class="text-gray-700 mb-6">Anda telah melengkapi semua data. Klik tombol "Kirim Pendaftaran" di bawah untuk menyelesaikan proses.</p>

                                <div class="text-center py-10">
                                    <i data-lucide="send-check" class="w-16 h-16 text-secondary-green mx-auto mb-4"></i>
                                    <h3 class="text-2xl font-extrabold text-primary-blue mb-3">Pendaftaran Siap Dikirim!</h3>
                                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                                        Pastikan semua data sudah benar. Setelah dikirim, Anda akan menerima konfirmasi.
                                    </p>
                                    <div class="text-left p-4 bg-gray-100 rounded-lg max-w-sm mx-auto">
                                        <p class="text-sm font-semibold text-gray-700">Ringkasan Data Utama:</p>
                                        <p class="text-xs text-gray-600">Nama: <span id="summary-nama" class="font-semibold">-</span></p>
                                        <p class="text-xs text-gray-600">Jurusan: <span id="summary-jurusan" class="font-semibold">-</span></p>
                                        <p class="text-xs text-gray-600">Kontak: <span id="summary-kontak" class="font-semibold">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between pt-4">
                            <button type="button" id="prevBtn" onclick="prevStep()"
                                class="px-6 py-3 bg-gray-300 text-gray-700 font-semibold rounded-lg shadow-md hover:bg-gray-400 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                                <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i> Sebelumnya
                            </button>

                            <button type="button" id="nextBtn" onclick="nextStep()"
                                class="px-6 py-3 bg-primary-blue text-white font-semibold rounded-lg shadow-lg hover:bg-blue-800 transition duration-150 flex items-center">
                                Lanjut <i data-lucide="arrow-right" class="w-5 h-5 ml-2"></i>
                            </button>

                            <button type="submit" id="submitBtn"
                                class="px-6 py-3 bg-secondary-green text-white font-bold rounded-lg shadow-lg transform hover:scale-[1.01] transition duration-300 hidden flex items-center">
                                Kirim Pendaftaran <i data-lucide="send" class="w-5 h-5 ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section id="kontak" class="py-20 bg-white">
            </section>

        <footer class="py-10 bg-footer-dark text-white">
            </footer>

        <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

        <script type="module">

            // Data langkah-langkah formulir BARU (5 langkah total) - Tetap diperlukan untuk Progress Bar
            const STEPS = [
                { id: 1, title: 'Data Diri', name: 'data-diri' },
                { id: 2, title: 'Data Sekolah', name: 'data-sekolah' },
                { id: 3, title: 'Pilih Jurusan', name: 'pilih-jurusan' },
                { id: 4, title: 'Upload Persyaratan', name: 'upload-dokumen' },
                { id: 5, title: 'Selesai', name: 'selesai' }
            ];

            // Data Jurusan SMAKNIS dan Peta Jurusan - Tetap diperlukan untuk Real-time Table & Summary
            const JURUSAN = [
                { value: "PPLG", text: "PPLG - Pengembangan Perangkat Lunak dan GIM" },
                { value: "TJKT", text: "TJKT - Teknik Jaringan Komputer dan Telekomunikasi" },
                { value: "AKL", text: "AKL - Akuntansi dan Keuangan Lembaga" },
                { value: "DKV", text: "DKV - Desain Komunikasi Visual" },
                { value: "MPLB", text: "MPLB - Manajemen Perkantoran dan Layanan Bisnis" },
                { value: "Otomotif", text: "Otomotif - Teknik Otomotif" }
            ];

            const JURUSAN_MAP = {
                "PPLG": "PPLG (Pengembangan Perangkat Lunak)",
                "TJKT": "TJKT (Teknik Jaringan Komputer)",
                "AKL": "AKL (Akuntansi dan Keuangan)",
                "DKV": "DKV (Desain Komunikasi Visual)",
                "MPLB": "MPLB (Manajemen Perkantoran)",
                "Otomotif": "Otomotif (Teknik Otomotif)"
            };

            let currentStep = 1;
            const totalSteps = STEPS.length;
            // let formData = {}; // <-- DIHAPUS, data sekarang ada di DOM

            // Elemen-elemen DOM
            const stepIndicatorsEl = document.getElementById('stepIndicators');
            const formContentEl = document.getElementById('formContent');
            const progressBarEl = document.getElementById('progressBar');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            const ppdbForm = document.getElementById('ppdb-form');
            const notification = document.getElementById('notification');
            const userIdDisplayEl = document.getElementById('user-id-display');

            // Elemen untuk daftar siswa
            const tableContainerEl = document.getElementById('applicants-table-container');

            // Elemen dan Fungsi untuk Active Link Highlighting (Tetap sama)
            const sections = document.querySelectorAll('section[id]');
            const navLinksDesktop = document.querySelectorAll('.nav-link-desktop');
            const navLinksMobile = document.querySelectorAll('.nav-link-mobile');


            /**
             * Memperbarui tautan navigasi agar aktif sesuai dengan section yang sedang terlihat.
             */
            function updateActiveLink() {
                // ... (Fungsi ini tetap sama, tidak perlu diubah) ...
                let currentSectionId = 'beranda'; // Default ke Beranda
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    if (window.scrollY >= sectionTop - 150) {
                        currentSectionId = section.getAttribute('id');
                    }
                });
                navLinksDesktop.forEach(link => {
                    link.classList.remove('nav-link-desktop-active');
                    if (link.getAttribute('href').includes(currentSectionId)) {
                        link.classList.add('nav-link-desktop-active');
                    }
                });
                navLinksMobile.forEach(link => {
                    link.classList.remove('nav-link-mobile-active');
                    if (link.getAttribute('href').includes(currentSectionId)) {
                        link.classList.add('nav-link-mobile-active');
                    }
                });
            }


            // ======================= FIREBASE DAN LOGIKA DAFTAR SISWA =======================
            // NOTE: Bagian ini tetap dipertahankan agar tabel real-time tetap berfungsi.
            // Backend Laravel Anda idealnya juga perlu menulis ke Firebase collection ini
            // agar tabelnya tetap "real-time" setelah data dikirim ke MySQL.

            /**
             * Merender tabel pendaftar dari data Firestore.
             */

            /**
             * Memulai listener Firestore untuk mengambil daftar dokumen pendaftar secara real-time.
             */


            /**
             * Fungsi untuk menambah dokumen registrasi baru saat form disubmit.
             * FUNGSI INI SUDAH TIDAK DIPAKAI KARENA SUBMIT DITANGANI LARAVEL.
             * window.saveRegistration = async function (data) { ... } // <-- DIHAPUS
             */


            /**
             * Menginisialisasi Firebase, melakukan autentikasi, dan mendengarkan daftar pendaftar.
             */

            // ======================= LOGIKA FORM DAN UI =======================

            /**
             * Fungsi utilitas untuk menampilkan notifikasi kustom.
             */
            function showNotification(message, isError = false) {
                // ... (Fungsi ini tetap sama, tidak perlu diubah) ...
                notification.classList.remove('bg-secondary-green', 'bg-red-500');
                notification.classList.add(isError ? 'bg-red-500' : 'bg-secondary-green');

                const iconHtml = `<i data-lucide="${isError ? 'x-octagon' : 'check-circle'}" class="w-6 h-6"></i>`;
                notification.innerHTML = `${iconHtml}<span id="notification-message">${message}</span>`;

                notification.classList.add('show');
                setTimeout(() => {
                    notification.classList.remove('show');
                }, 5000);
                lucide.createIcons();
            }

            /**
             * Validasi input wajib pada langkah saat ini.
             * FUNGSI INI DIMODIFIKASI untuk membaca dari div step yang aktif.
             */
            function validateStep(stepId) {
                // MODIFIKASI: Cari input hanya di dalam div step yang aktif
                const currentStepEl = document.getElementById('step-' + stepId);
                if (!currentStepEl) return true; // Lewati jika elemen tidak ditemukan

                const requiredInputs = currentStepEl.querySelectorAll('[required]');
                let valid = true;
                let errorMessages = [];

                // Helper function to mark input as invalid
                const markInvalid = (input) => {
                    valid = false;
                    input.classList.add('border-red-500', 'ring-2', 'ring-red-500/50');
                };

                // Remove previous error markings
                const allInputsInStep = currentStepEl.querySelectorAll('input, select, textarea');
                allInputsInStep.forEach(input => {
                    input.classList.remove('border-red-500', 'ring-2', 'ring-red-500/50');
                });

                // 1. Check for empty required fields
                requiredInputs.forEach(input => {
                    if (input.type === 'file') {
                        // Cek jika input file wajib dan belum ada file yang dipilih
                        if (input.files.length === 0) {
                            markInvalid(input);
                            if (!errorMessages.includes('Mohon lengkapi semua kolom bertanda *.')) {
                                errorMessages.push('Mohon lengkapi semua kolom bertanda *.');
                            }
                        }
                    } else if (input.value.trim() === "" || (input.tagName === 'SELECT' && input.value === "")) {
                        markInvalid(input);
                        if (!errorMessages.includes('Mohon lengkapi semua kolom bertanda *.')) {
                            errorMessages.push('Mohon lengkapi semua kolom bertanda *.');
                        }
                    }
                });

                if (!valid) {
                    showNotification(errorMessages[0] || `Mohon lengkapi semua kolom bertanda * di langkah ${stepId}.`, true);
                    return false;
                }

                // 2. Perform specific validation based on the step (Tetap sama)
                if (stepId === 1) { // Data Diri
                    const nisnInput = currentStepEl.querySelector('#nisn');
                    const waInput = currentStepEl.querySelector('#kontak-wa');

                    if (nisnInput && nisnInput.value.trim().length > 0) {
                        const nisnValue = nisnInput.value.trim();
                        if (!/^\d{10}$/.test(nisnValue)) {
                            markInvalid(nisnInput);
                            errorMessages.push('NISN harus tepat 10 digit angka.');
                        }
                    }
                    if (waInput && waInput.value.trim().length > 0) {
                        const waValue = waInput.value.trim();
                        if (!/^\d{10,}$/.test(waValue)) {
                            markInvalid(waInput);
                            errorMessages.push('Nomor WhatsApp tidak valid. Masukkan minimal 10 digit angka.');
                        }
                    }
                }

                if (errorMessages.length > 0) {
                    const specificError = errorMessages.find(msg => msg !== 'Mohon lengkapi semua kolom bertanda *.');
                    if (specificError) {
                        showNotification(specificError, true);
                        return false;
                    }
                    showNotification(errorMessages[0], true);
                    return false;
                }

                return valid;
            }

            /**
             * Merender semua lingkaran indikator langkah pada progress bar.
             */
            function renderStepIndicators() {
                // ... (Fungsi ini tetap sama, tidak perlu diubah) ...
                stepIndicatorsEl.innerHTML = '';
                STEPS.forEach(step => {
                    const isCompleted = step.id < currentStep;
                    const isActive = step.id === currentStep;

                    let circleClasses = 'w-10 h-10 flex items-center justify-center rounded-full text-white font-bold text-sm z-10 step-circle';
                    let titleClasses = 'text-xs mt-3 w-max text-center absolute left-1/2 transform -translate-x-1/2 transition duration-300 ease-in-out';

                    if (isCompleted) {
                        circleClasses += ' bg-secondary-green scale-110';
                        titleClasses += ' text-secondary-green font-semibold';
                    } else if (isActive) {
                        circleClasses += ' bg-primary-blue ring-4 ring-primary-blue/30 scale-110';
                        titleClasses += ' text-primary-blue font-bold';
                    } else {
                        circleClasses += ' bg-gray-400/70 text-gray-100';
                        titleClasses += ' text-gray-500';
                    }

                    const stepEl = document.createElement('div');
                    stepEl.className = 'relative flex-1 text-center';

                    const circleEl = document.createElement('div');
                    circleEl.className = circleClasses + ' cursor-default mx-auto';
                    circleEl.innerHTML = isCompleted ? `<i data-lucide="check" class="w-5 h-5"></i>` : step.id;

                    const subTitleEl = document.createElement('span');
                    subTitleEl.className = titleClasses;
                    const stepTitleShort = step.title.split(' ')[0] + ' ' + (step.title.split(' ')[1] || '');
                    subTitleEl.textContent = stepTitleShort.trim();
                    subTitleEl.style.top = '56px';

                    stepEl.appendChild(circleEl);
                    stepEl.appendChild(subTitleEl);
                    stepIndicatorsEl.appendChild(stepEl);
                });
                lucide.createIcons();
            }

            /**
             * Memperbarui tampilan bilah progres dan KONTEN FORMULIR (Show/Hide).
             * FUNGSI INI DIMODIFIKASI BESAR.
             */
            function updateProgress() {
                // 1. Update Progress Bar (Logika tetap sama)
                const progressSteps = totalSteps - 1;
                const progressPercentage = (currentStep === totalSteps) ? 100 : ((currentStep - 1) / (progressSteps - 1)) * 100;
                progressBarEl.style.width = `${progressPercentage}%`;

                // 2. Update Lingkaran Indikator (Logika tetap sama)
                renderStepIndicators();

                // 3. MODIFIKASI: Tampilkan div langkah yang benar dan sembunyikan yang lain
                // Sembunyikan semua langkah terlebih dahulu
                document.querySelectorAll('.form-step').forEach(stepEl => {
                    stepEl.classList.add('hidden');
                });

                // Tampilkan langkah yang aktif
                const activeStepEl = document.getElementById('step-' + currentStep);
                if (activeStepEl) {
                    activeStepEl.classList.remove('hidden');
                }
                
                // 4. MODIFIKASI: Jika di langkah terakhir, isi data summary
                if (currentStep === totalSteps) {
                    // Ambil data langsung dari input DOM
                    const nama = document.getElementById('nama-lengkap')?.value || '-';
                    const jurusanVal = document.getElementById('jurusan-minat')?.value;
                    const jurusanText = JURUSAN_MAP[jurusanVal] || '-';
                    const kontak = document.getElementById('kontak-wa')?.value || '-';

                    // Masukkan ke elemen summary
                    document.getElementById('summary-nama').textContent = nama;
                    document.getElementById('summary-jurusan').textContent = jurusanText;
                    document.getElementById('summary-kontak').textContent = kontak;
                }


                // 5. Mengelola status tombol Navigasi (Logika tetap sama)
                prevBtn.disabled = currentStep === 1;
                prevBtn.classList.toggle('opacity-50', currentStep === 1);

                if (currentStep === totalSteps) {
                    nextBtn.classList.add('hidden');
                    submitBtn.classList.remove('hidden');
                } else {
                    nextBtn.classList.remove('hidden');
                    submitBtn.classList.add('hidden');
                }
            }

            /**
             * Pindah ke langkah berikutnya.
             */
            window.nextStep = function () {
                if (currentStep < totalSteps) {
                    // 1. Validasi langkah saat ini
                    if (!validateStep(currentStep)) {
                        return;
                    }

                    // 2. Simpan data (Sudah tidak perlu, data ada di DOM)
                    // collectFormData(); // <-- DIHAPUS

                    // 3. Pindah langkah dan scroll ke atas form
                    currentStep++;
                    updateProgress();
                    document.getElementById('daftar').scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            /**
             * Pindah ke langkah sebelumnya.
             */
            window.prevStep = function () {
                if (currentStep > 1) {
                    // collectFormData(); // <-- DIHAPUS
                    currentStep--;
                    updateProgress();
                    document.getElementById('daftar').scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            // Logika Submit Formulir (pada Langkah Terakhir)
            document.addEventListener('DOMContentLoaded', function () {
                AOS.init({ once: true, duration: 800 });
                lucide.createIcons();

                // Panggil fungsi active link saat pertama kali dimuat
                updateActiveLink();

                // Tambahkan event listener untuk scroll dan resize
                window.addEventListener('scroll', updateActiveLink);
                window.addEventListener('resize', updateActiveLink);

                // Inisialisasi Firebase dan mulai ambil daftar pendaftar
                initFirebaseAndFetchCount();

                // Inisialisasi Tampilan Form
                updateProgress();

                // Cek jika ada error validasi dari Laravel saat halaman dimuat
                // Jika ada, kita harus pindah ke step yang mengandung error tersebut.
                // Ini logika tambahan yang cukup kompleks, tapi bisa dilakukan dengan
                // memeriksa field mana yang memiliki error dan memetakan ke step-nya.
                // Untuk saat ini, kita biarkan mulai dari step 1.
                // Jika server-side validation gagal, Laravel akan me-refresh halaman
                // dan JS akan memulai dari `currentStep = 1`.
                // Anda bisa menyiasati ini dengan melewatkan variabel dari Blade ke JS
                // @if($errors->any())
                //     <script> let startStep = {{ $errors->first('nama-lengkap') ? 1 : ($errors->first('asal-sekolah') ? 2 : 1) }}; </script>
                // @endif
                // Lalu di JS: currentStep = window.startStep || 1;
                

                /**
                 * Event listener submit SEKARANG DIHAPUS.
                 * Form akan disubmit secara normal (non-AJAX) ke Laravel.
                 * Notifikasi sukses/error akan ditangani oleh Laravel
                 * (misalnya, dengan session flash message).
                 * ppdbForm.addEventListener('submit', async function (e) { ... } // <-- DIHAPUS
                 */
            });
        </script>
    </body>

</html>