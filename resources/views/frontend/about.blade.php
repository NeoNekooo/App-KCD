@extends('layouts.frontend')

@section('title', 'Tentang Kami - Kantor Cabang Dinas')

@section('content')
<div class="bg-blue-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-4xl font-extrabold mb-4">Profil Kantor Cabang Dinas</h1>
        <p class="text-xl text-blue-100 max-w-2xl mx-auto">Mengenal visi, misi, dan struktur organisasi kami dalam melayani dunia pendidikan.</p>
    </div>
</div>

<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Sejarah Singkat</h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    Kantor Cabang Dinas (KCD) dibentuk sebagai unit pelaksana teknis untuk mendekatkan pelayanan pendidikan kepada satuan pendidikan di daerah. Dengan luasnya wilayah kerja Dinas Pendidikan Provinsi, KCD berperan vital dalam melakukan pengawasan, pembinaan, dan koordinasi yang lebih intensif.
                </p>
                <p class="text-gray-600 leading-relaxed">
                    Sejak berdirinya, kami terus bertransformasi menjadi lembaga yang mengedepankan teknologi dalam pelayanan administrasi guna menciptakan ekosistem pendidikan yang lebih efisien dan transparan.
                </p>
            </div>
            <div class="bg-gray-100 rounded-2xl h-80 flex items-center justify-center overflow-hidden shadow-inner">
                 <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Education" class="w-full h-full object-cover">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-20">
            <div class="bg-blue-50 p-8 rounded-2xl border border-blue-100 shadow-sm">
                <h3 class="text-2xl font-bold text-blue-900 mb-4 flex items-center">
                    <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Visi
                </h3>
                <p class="text-gray-700 italic leading-relaxed">
                    "Terwujudnya layanan pendidikan yang berkualitas, berkeadilan, dan berdaya saing global di seluruh wilayah kerja Kantor Cabang Dinas."
                </p>
            </div>
            <div class="bg-blue-50 p-8 rounded-2xl border border-blue-100 shadow-sm">
                <h3 class="text-2xl font-bold text-blue-900 mb-4 flex items-center">
                    <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Misi
                </h3>
                <ul class="list-disc list-inside text-gray-700 space-y-2">
                    <li>Meningkatkan kualitas sumber daya manusia pendidik.</li>
                    <li>Optimalisasi tata kelola administrasi pendidikan berbasis digital.</li>
                    <li>Pemerataan sarana dan prasarana pendidikan.</li>
                    <li>Memperkuat koordinasi antara sekolah dan stakeholder.</li>
                </ul>
            </div>
        </div>

        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-10 text-center">Struktur Organisasi</h2>
            <div class="flex flex-col items-center">
                <!-- Simple Org Chart Visualization -->
                <div class="bg-blue-600 text-white px-8 py-4 rounded-lg font-bold shadow-md mb-8">Kepala Kantor Cabang Dinas</div>
                <div class="w-px h-8 bg-gray-300"></div>
                <div class="w-4/5 h-px bg-gray-300"></div>
                <div class="flex justify-between w-4/5 mt-0">
                    <div class="flex flex-col items-center">
                         <div class="w-px h-8 bg-gray-300"></div>
                         <div class="bg-blue-500 text-white px-4 py-3 rounded shadow-sm text-sm font-semibold">Subbag Tata Usaha</div>
                    </div>
                    <div class="flex flex-col items-center">
                         <div class="w-px h-8 bg-gray-300"></div>
                         <div class="bg-blue-500 text-white px-4 py-3 rounded shadow-sm text-sm font-semibold">Seksi Pelayanan</div>
                    </div>
                    <div class="flex flex-col items-center">
                         <div class="w-px h-8 bg-gray-300"></div>
                         <div class="bg-blue-500 text-white px-4 py-3 rounded shadow-sm text-sm font-semibold">Seksi Pembinaan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
