@extends('layouts.ppdb')

@section('content-ppdb')
    
 
<div id="notification" 
     class="fixed top-5 right-5 hidden z-50 px-4 py-3 rounded-lg shadow-lg text-white transition-all duration-300">
</div>

    <!-- Formulir Pendaftaran Section (Daftar) - DENGAN PROGRESS BAR DINAMIS -->
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

                    <form id="ppdb-form" class="space-y-6" method="POST" action="{{ route('ppdb.formulir.store') }}" enctype="multipart/form-data">
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
                                    <input type="hidden" name="tahun_id" value="{{ $tahunAktif->id ?? '' }}">
                                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap Calon Siswa <span class="text-red-500">*</span></label>
                                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                                        class="{{ $inputClasses }} @error('nama_lengkap') {{ $errorClasses }} @enderror" placeholder="Masukkan nama lengkap siswa">
                                    @error('nama_lengkap') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                                        <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required
                                            class="{{ $inputClasses }} @error('tempat_lahir') {{ $errorClasses }} @enderror" placeholder="Contoh: Jakarta">
                                        @error('tempat_lahir') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="tgl_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                                        <input type="date" id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir') }}" required
                                            class="{{ $inputClasses }} @error('tgl_lahir') {{ $errorClasses }} @enderror">
                                        @error('tgl_lahir') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label for="alamat_lengkap" class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap (Domisili) <span class="text-red-500">*</span></label>
                                    <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" required
                                        class="{{ $inputClasses }} @error('alamat_lengkap') {{ $errorClasses }} @enderror" placeholder="Masukkan alamat lengkap siswa saat ini">{{ old('alamat_lengkap') }}</textarea>
                                    @error('alamat_lengkap') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                                        <select id="jenis_kelamin" name="jenis_kelamin" required
                                                class="{{ $inputClasses }} appearance-none bg-white @error('jenis_kelamin') {{ $errorClasses }} @enderror">
                                            <option value="">-- Pilih Jenis Kelamin --</option>
                                            <option value="L" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('jenis_kelamin') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="nisn" class="block text-sm font-medium text-gray-700 mb-2">NISN <span class="text-red-500">*</span></label>
                                        <input type="number" id="nisn" name="nisn" value="{{ old('nisn') }}" required
                                            class="{{ $inputClasses }} @error('nisn') {{ $errorClasses }} @enderror" placeholder="Nomor Induk Siswa Nasional (10 Digit)">
                                        @error('nisn') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label for="kontak" class="block text-sm font-medium text-gray-700 mb-2">Nomor HP/WhatsApp (Aktif) <span class="text-red-500">*</span></label>
                                    <input type="tel" id="kontak" name="kontak" value="{{ old('kontak') }}" required
                                        class="{{ $inputClasses }} @error('kontak') {{ $errorClasses }} @enderror" placeholder="Contoh: 081234567890">
                                    @error('kontak') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <p class="text-sm text-gray-500 pt-2 border-t border-gray-200">Data Orang Tua (Opsional, disarankan):</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="nama_ayah" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap Ayah</label>
                                        <input type="text" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah') }}"
                                            class="{{ $inputClasses }} @error('nama_ayah') {{ $errorClasses }} @enderror" placeholder="Nama lengkap ayah/wali">
                                        @error('nama_ayah') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="nama_ibu" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap Ibu</label>
                                        <input type="text" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu') }}"
                                            class="{{ $inputClasses }} @error('nama_ibu') {{ $errorClasses }} @enderror" placeholder="Nama lengkap ibu/wali">
                                        @error('nama_ibu') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div id="step-2" class="form-step space-y-6 hidden">
                                <h2 class="text-2xl font-bold text-primary-blue mb-4">Langkah 2: Data Sekolah</h2>
                                <p class="text-gray-700 mb-6">Masukkan informasi asal sekolah dan detail pendaftaran awal Anda.</p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="asal_sekolah" class="block text-sm font-medium text-gray-700 mb-2">Asal Sekolah <span class="text-red-500">*</span></label>
                                        <input type="text" id="asal_sekolah" name="asal_sekolah" value="{{ old('asal_sekolah') }}" required
                                            class="{{ $inputClasses }} @error('asal_sekolah') {{ $errorClasses }} @enderror" placeholder="Contoh: SMP Negeri 1">
                                        @error('asal_sekolah') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
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
                                        <label for="ukuran_pakaian" class="block text-sm font-medium text-gray-700 mb-2">Ukuran Baju Seragam <span class="text-red-500">*</span></label>
                                        <select id="ukuran_pakaian" name="ukuran_pakaian" required
                                                class="{{ $inputClasses }} appearance-none bg-white @error('ukuran_pakaian') {{ $errorClasses }} @enderror">
                                            <option value="">-- Pilih Ukuran Baju --</option>
                                            @foreach (['S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $size)
                                                <option value="{{ $size }}" {{ old('ukuran_pakaian') == $size ? 'selected' : '' }}>{{ $size }}</option>
                                            @endforeach
                                        </select>
                                        @error('ukuran_pakaian') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="jalur_id" class="block text-sm font-medium text-gray-700 mb-2">Jalur Pendaftaran <span class="text-red-500">*</span></label>
                                        <select id="jalur_id" name="jalur_id" required
                                                class="{{ $inputClasses }} appearance-none bg-white @error('jalur_id') {{ $errorClasses }} @enderror">
                                            <option value="">-- Pilih Jalur Pendaftaran --</option> 
                                            @foreach($jalurs as $jalur)
                                                <option value="{{ $jalur->id }}" 
                                                    {{ old('jalur_id', $formulir->jalur_id ?? '') == $jalur->id ? 'selected' : '' }}>
                                                    {{ $jalur->kode }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('jalur_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div id="step-3" class="form-step space-y-6 hidden">
                                <h2 class="text-2xl font-bold text-primary-blue mb-4">Langkah 3: Pilih Jurusan</h2>
                                <p class="text-gray-700 mb-6">Pilih satu kompetensi keahlian yang paling Anda minati di SMAKNIS.</p>

                                <div>
                                    <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-2">Pilihan Utama Jurusan <span class="text-red-500">*</span></label>
                                    <select id="jurusan" name="jurusan" required
                                            class="{{ $inputClasses }} appearance-none bg-white @error('jurusan') {{ $errorClasses }} @enderror">
                                        <option value="">-- Pilih Jurusan Pilihan Utama --</option>
                                        
                                        @foreach( $jurusans as $jurusan)
                                            <option value="{{ $jurusan  }}" 
                                                {{ old('jurusan', $formulir->jurusan ?? '') == $jurusan ? 'selected' : '' }}>
                                                {{ $jurusan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jurusan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
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
                                    {{-- NAH DI SINI TAMPILKAN INPUTANNYA SESUAI JUMLAH SYARAT AKTIF, NANTI SEBELUM KESINI KAN ADA PILIH JALUR NAH JADI SYARAT YANG MUNCUL SESUAI JALUR --}}
                                    <div id="syarat-container" class="space-y-4">
                                        <p class="text-gray-500 text-sm">Pilih jalur pendaftaran terlebih dahulu untuk menampilkan syarat.</p>
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

        
@endsection