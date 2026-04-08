@extends('layouts.frontend')

@section('title', 'Layanan - Kantor Cabang Dinas')

@section('content')
<div class="bg-blue-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-4xl font-extrabold mb-4">Layanan Publik</h1>
        <p class="text-xl text-blue-100 max-w-2xl mx-auto">Informasi lengkap mengenai jenis layanan yang tersedia di Kantor Cabang Dinas.</p>
    </div>
</div>

<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Service Item 1 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-md transition duration-300">
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Administrasi Guru (PTK)</h3>
                <p class="text-gray-600 mb-6">Layanan pengurusan NUPTK, mutasi guru, sertifikasi, dan administrasi kepegawaian pendidik lainnya.</p>
                <ul class="text-sm text-gray-500 space-y-2 mb-8">
                    <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293l-9.414 9.414-4.707-4.707-1.414 1.414 6.121 6.121 10.828-10.828-1.414-1.414z"/></svg> Pengajuan NUPTK</li>
                    <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293l-9.414 9.414-4.707-4.707-1.414 1.414 6.121 6.121 10.828-10.828-1.414-1.414z"/></svg> Mutasi Antar Satuan</li>
                </ul>
                <button class="text-blue-600 font-semibold hover:text-blue-800 transition">Detail Prosedur &rarr;</button>
            </div>

            <!-- Service Item 2 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-md transition duration-300">
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Pembinaan Sekolah</h3>
                <p class="text-gray-600 mb-6">Monitoring dan evaluasi standar nasional pendidikan pada sekolah negeri maupun swasta.</p>
                <ul class="text-sm text-gray-500 space-y-2 mb-8">
                    <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293l-9.414 9.414-4.707-4.707-1.414 1.414 6.121 6.121 10.828-10.828-1.414-1.414z"/></svg> Akreditasi Sekolah</li>
                    <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293l-9.414 9.414-4.707-4.707-1.414 1.414 6.121 6.121 10.828-10.828-1.414-1.414z"/></svg> Izin Operasional</li>
                </ul>
                <button class="text-blue-600 font-semibold hover:text-blue-800 transition">Detail Prosedur &rarr;</button>
            </div>

            <!-- Service Item 3 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-md transition duration-300">
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Dana BOS & DAK</h3>
                <p class="text-gray-600 mb-6">Fasilitasi pelaporan dan verifikasi penggunaan dana Bantuan Operasional Sekolah.</p>
                <ul class="text-sm text-gray-500 space-y-2 mb-8">
                    <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293l-9.414 9.414-4.707-4.707-1.414 1.414 6.121 6.121 10.828-10.828-1.414-1.414z"/></svg> Verifikasi RKAS</li>
                    <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293l-9.414 9.414-4.707-4.707-1.414 1.414 6.121 6.121 10.828-10.828-1.414-1.414z"/></svg> Laporan Realisasi</li>
                </ul>
                <button class="text-blue-600 font-semibold hover:text-blue-800 transition">Detail Prosedur &rarr;</button>
            </div>

            <!-- Service Item 4 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-md transition duration-300">
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Layanan Kesiswaan</h3>
                <p class="text-gray-600 mb-6">Pengelolaan data siswa, beasiswa PIP, dan mutasi siswa antar wilayah.</p>
                <ul class="text-sm text-gray-500 space-y-2 mb-8">
                    <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293l-9.414 9.414-4.707-4.707-1.414 1.414 6.121 6.121 10.828-10.828-1.414-1.414z"/></svg> Rekomendasi Beasiswa</li>
                    <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293l-9.414 9.414-4.707-4.707-1.414 1.414 6.121 6.121 10.828-10.828-1.414-1.414z"/></svg> Validasi Ijazah</li>
                </ul>
                <button class="text-blue-600 font-semibold hover:text-blue-800 transition">Detail Prosedur &rarr;</button>
            </div>

            <!-- Service Item 5 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-md transition duration-300">
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293L17 5.414a1 1 0 01.293.707V7M8 7h9" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Legalitas Dokumen</h3>
                <p class="text-gray-600 mb-6">Pengesahan (legalisir) dokumen pendidikan untuk keperluan studi lanjut atau pekerjaan.</p>
                <ul class="text-sm text-gray-500 space-y-2 mb-8">
                    <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293l-9.414 9.414-4.707-4.707-1.414 1.414 6.121 6.121 10.828-10.828-1.414-1.414z"/></svg> Legalisir Ijazah</li>
                    <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293l-9.414 9.414-4.707-4.707-1.414 1.414 6.121 6.121 10.828-10.828-1.414-1.414z"/></svg> Surat Keterangan Pengganti</li>
                </ul>
                <button class="text-blue-600 font-semibold hover:text-blue-800 transition">Detail Prosedur &rarr;</button>
            </div>

            <!-- Service Item 6 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-md transition duration-300 text-center flex flex-col items-center justify-center border-dashed border-2">
                <p class="text-gray-500 italic">Layanan lainnya akan segera hadir secara digital...</p>
            </div>
        </div>
    </div>
</div>
@endsection
