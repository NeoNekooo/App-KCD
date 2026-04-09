@extends('layouts.frontend')

@section('title', 'Profil & Tentang Kami - ' . ($instansi->nama_instansi ?? 'Kantor Cabang Dinas'))

@push('styles')
<style>
    /* Styling khusus dari Editor Teks (CKEditor) */
    .cms-content p { margin-bottom: 1rem; line-height: 1.8; color: #475569; }
    .cms-content strong { color: #1e293b; font-weight: 700; }
    .cms-content ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1rem; color: #475569; }
    .cms-content ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 1rem; color: #475569; }
    .cms-content blockquote { 
        border-left: 4px solid #3b82f6; 
        padding-left: 1rem; 
        margin-left: 0; 
        font-style: italic; 
        color: #64748b; 
        background: rgba(59, 130, 246, 0.05);
        padding: 1rem;
        border-radius: 0.25rem;
    }
    
    /* Utility Animations & Glassmorphism */
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
    }
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(59, 130, 246, 0.1);
        border-color: rgba(255, 255, 255, 0.8);
    }
    .transition-all { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
    
    /* Fade In Up animation */
    .animate-in { opacity: 0; transform: translateY(30px); animation: fadeIn 0.8s ease-out forwards; }
    .delay-100 { animation-delay: 100ms; }
    .delay-200 { animation-delay: 200ms; }
    .delay-300 { animation-delay: 300ms; }
    @keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@section('content')
<!-- Hero Section (Deep Blue with Abstract Gradient) -->
<div class="relative bg-slate-900 py-24 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-[30%] -right-[10%] w-[70%] h-[100%] rounded-full bg-blue-600/20 blur-[120px]"></div>
        <div class="absolute -bottom-[20%] -left-[10%] w-[50%] h-[80%] rounded-full bg-indigo-500/20 blur-[100px]"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-in z-10">
        <span class="inline-block px-4 py-1.5 rounded-full bg-blue-500/20 text-blue-300 font-semibold text-sm tracking-widest uppercase mb-6 backdrop-blur-sm border border-blue-400/30">
            Profil Instansi
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6 leading-tight">
            {{ $instansi->nama_instansi ?? 'Kantor Cabang Dinas' }}
        </h1>
        <p class="text-xl text-blue-100/80 max-w-3xl mx-auto font-light leading-relaxed">
            Menempa Generasi Tangguh, Mencerdaskan Bangsa melalui Pelayanan Pendidikan yang Transparan, Cerdas, dan Profesional.
        </p>
    </div>
</div>

<!-- Main Content Area with Bento Layout -->
<div class="py-24 bg-slate-50 relative">
    <!-- Dekorasi background atas -->
    <div class="absolute top-0 inset-x-0 h-40 bg-gradient-to-b from-slate-900 to-transparent opacity-5 z-0"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Bento Grid 1: Sejarah & Foto -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-20">
            <!-- Left: Sejarah Singkat -->
            <div class="lg:col-span-7 glass-card rounded-[2rem] p-8 md:p-12 transition-all animate-in delay-100">
                <div class="inline-flex items-center justify-center p-3 bg-blue-50 text-blue-600 rounded-2xl mb-6 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-800 mb-6 font-sans">Sejarah Singkat</h2>
                <div class="cms-content">
                    @if($instansi && $instansi->sejarah_singkat)
                        {!! $instansi->sejarah_singkat !!}
                    @else
                        <p>Belum ada rincian sejarah yang dicantumkan untuk instansi ini.</p>
                    @endif
                </div>
            </div>

            <!-- Right: Foto Premium Profil -->
            <div class="lg:col-span-5 rounded-[2rem] overflow-hidden shadow-2xl relative group animate-in delay-200 min-h-[400px]">
                <!-- Layer Overlay untuk estetika -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent z-10"></div>
                
                @if($instansi && $instansi->foto_profil)
                    <img src="{{ Storage::url($instansi->foto_profil) }}" alt="Foto Instansi" class="w-full h-full object-cover relative z-0 transition-transform duration-700 group-hover:scale-110">
                @else
                    <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Placeholder Gedung Edukasi" class="w-full h-full object-cover relative z-0 transition-transform duration-700 group-hover:scale-110">
                @endif
                
                <!-- Badge di pojok gambar -->
                <div class="absolute bottom-6 left-6 right-6 z-20">
                    <div class="glass-card rounded-xl p-4">
                        <p class="text-slate-800 font-semibold">{{ $instansi->nama_instansi ?? 'Gedung KCD Wilayah' }}</p>
                        <p class="text-slate-500 text-sm flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Pusat Administratif & Pelayanan
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bento Grid 2: Visi & Misi (Overlapping & Asimetris) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-24">
            <!-- Visi Card -->
            <div class="glass-card rounded-[2rem] p-8 md:p-10 transition-all animate-in delay-200 border-t-4 border-t-emerald-400">
                <div class="flex items-center mb-6">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center bg-emerald-50 text-emerald-500 shadow-sm mr-4">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <h3 class="text-3xl font-bold text-slate-800">Visi Kami</h3>
                </div>
                <div class="cms-content text-lg italic bg-slate-50/50 p-6 rounded-2xl border border-slate-100">
                    @if($instansi && $instansi->visi)
                        {!! $instansi->visi !!}
                    @else
                        "Terwujudnya layanan pendidikan yang berkualitas, berkeadilan, dan berdaya saing global."
                    @endif
                </div>
            </div>

            <!-- Misi Card -->
            <div class="glass-card rounded-[2rem] p-8 md:p-10 transition-all animate-in delay-300 border-t-4 border-t-indigo-400 md:translate-y-8">
                <div class="flex items-center mb-6">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center bg-indigo-50 text-indigo-500 shadow-sm mr-4">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-3xl font-bold text-slate-800">Misi Kami</h3>
                </div>
                <div class="cms-content">
                    @if($instansi && $instansi->misi)
                        {!! $instansi->misi !!}
                    @else
                        <ul>
                            <li>Meningkatkan kualitas sumber daya manusia pendidik.</li>
                            <li>Optimalisasi tata kelola administrasi pendidikan berbasis digital.</li>
                            <li>Pemerataan sarana dan prasarana pendidikan.</li>
                        </ul>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
