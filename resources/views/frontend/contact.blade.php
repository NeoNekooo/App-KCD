@extends('layouts.frontend')

@section('title', 'Kontak - Kantor Cabang Dinas')

@section('content')
<div class="bg-blue-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-4xl font-extrabold mb-4">Hubungi Kami</h1>
        <p class="text-xl text-blue-100 max-w-2xl mx-auto">Kami siap melayani pertanyaan dan bantuan administratif Anda.</p>
    </div>
</div>

<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-8">Informasi Kontak</h2>
                <div class="space-y-8">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-xl text-blue-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.828a1.6 1.6 0 01-2.263 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Alamat Kantor</h3>
                            <p class="mt-1 text-gray-600">Jl. Pendidikan No. 123, Kelurahan Belajar, Kecamatan Ilmu, Kota Wilayah KCD, 12345.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-xl text-blue-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Telepon & Fax</h3>
                            <p class="mt-1 text-gray-600">(021) 1234-5678 (Senin - Jumat, 08:00 - 16:00)</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-xl text-blue-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Email Resmi</h3>
                            <p class="mt-1 text-gray-600">info@kcd-wilayah.go.id</p>
                        </div>
                    </div>
                </div>

                <div class="mt-12">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Lokasi Kami</h3>
                    <div class="w-full h-64 bg-gray-200 rounded-2xl overflow-hidden shadow-inner flex items-center justify-center relative">
                        <!-- Simulated Google Maps -->
                        <div class="text-center p-8">
                             <svg class="w-12 h-12 text-blue-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                             </svg>
                             <p class="text-gray-500 font-medium">Map Kantor Cabang Dinas (Placeholder)</p>
                             <p class="text-gray-400 text-sm mt-2">Untuk implementasi nyata, masukkan embed code Google Maps di sini.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-8 rounded-3xl border border-gray-100 shadow-sm">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Kirim Pesan</h2>
                <form action="#" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" id="name" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition outline-none" placeholder="Masukkan nama Anda">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                            <input type="email" id="email" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition outline-none" placeholder="name@example.com">
                        </div>
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subjek</label>
                        <select id="subject" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition outline-none">
                            <option>Informasi Layanan</option>
                            <option>Pengaduan</option>
                            <option>Kerjasama</option>
                            <option>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Pesan Anda</label>
                        <textarea id="message" rows="5" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition outline-none" placeholder="Tuliskan pesan atau pertanyaan Anda..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-0.5">
                        Kirim Pesan Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
