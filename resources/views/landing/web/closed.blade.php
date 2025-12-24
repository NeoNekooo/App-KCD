@extends('landing.web.layouts.master_web')

@section('title', 'Pendaftaran Belum Dibuka')

@section('content')

{{-- Main Container --}}
<section class="min-h-screen flex flex-col justify-center items-center bg-white relative overflow-hidden pt-28 pb-16">
    
    {{-- Background Decoration (Abstract Shapes) --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-[10%] -right-[5%] w-[600px] h-[600px] bg-blue-50/50 rounded-full blur-[100px] opacity-60"></div>
        <div class="absolute top-[20%] -left-[10%] w-[500px] h-[500px] bg-indigo-50/50 rounded-full blur-[100px] opacity-60"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            
            {{-- 1. Icon / Ilustrasi Besar --}}
            <div class="mb-10 animate-bounce-slow">
                <div class="inline-flex items-center justify-center w-28 h-28 bg-blue-50 text-blue-600 rounded-3xl shadow-xl shadow-blue-100 transform rotate-3 hover:rotate-6 transition-transform duration-500">
                    <i class='bx bx-calendar-event text-6xl'></i>
                </div>
            </div>

            {{-- 2. Judul & Pesan Utama --}}
            <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 mb-6 tracking-tight leading-tight">
                Pendaftaran <span class="text-blue-600">Belum Dibuka</span>
            </h1>
            
            <p class="text-lg md:text-xl text-slate-500 max-w-2xl mx-auto leading-relaxed mb-12">
                Mohon maaf, saat ini periode Penerimaan Peserta Didik Baru (PPDB) sedang tidak aktif. Pantau terus halaman ini untuk informasi jadwal terbaru.
            </p>

            {{-- 3. Widget Jadwal (Jika Ada) --}}
            @if(isset($agendaAkanDatang) && $agendaAkanDatang)
                <div class="bg-white border border-slate-100 shadow-2xl shadow-blue-900/5 rounded-3xl p-8 md:p-10 mb-12 max-w-3xl mx-auto relative overflow-hidden group hover:border-blue-200 transition-all duration-300">
                    
                    {{-- Dekorasi Garis --}}
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                    
                    <h3 class="text-sm font-bold text-blue-600 uppercase tracking-widest mb-8 flex items-center justify-center gap-2">
                        <i class='bx bxs-bell-ring animate-swing'></i> Jadwal Berikutnya
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                        
                        {{-- Tanggal Mulai --}}
                        <div class="text-center">
                            <span class="block text-xs text-slate-400 font-bold uppercase mb-2">Dibuka Pada</span>
                            <div class="inline-block bg-blue-50 text-blue-700 px-6 py-3 rounded-2xl">
                                <span class="block text-3xl font-extrabold">{{ \Carbon\Carbon::parse($agendaAkanDatang->tanggal_mulai)->format('d') }}</span>
                                <span class="block text-sm font-bold uppercase">{{ \Carbon\Carbon::parse($agendaAkanDatang->tanggal_mulai)->isoFormat('MMMM Y') }}</span>
                            </div>
                        </div>

                        {{-- Divider / Arrow --}}
                        <div class="hidden md:flex justify-center text-slate-300">
                            <i class='bx bx-right-arrow-alt text-4xl'></i>
                        </div>
                        <div class="md:hidden flex justify-center text-slate-300 my-[-10px]">
                            <i class='bx bx-down-arrow-alt text-3xl'></i>
                        </div>

                        {{-- Tanggal Selesai --}}
                        <div class="text-center">
                            <span class="block text-xs text-slate-400 font-bold uppercase mb-2">Ditutup Pada</span>
                            <div class="inline-block bg-slate-50 text-slate-600 px-6 py-3 rounded-2xl border border-slate-100">
                                <span class="block text-3xl font-extrabold">{{ \Carbon\Carbon::parse($agendaAkanDatang->tanggal_selesai)->format('d') }}</span>
                                <span class="block text-sm font-bold uppercase">{{ \Carbon\Carbon::parse($agendaAkanDatang->tanggal_selesai)->isoFormat('MMMM Y') }}</span>
                            </div>
                        </div>

                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-50">
                        <p class="text-slate-500 font-medium italic">"{{ $agendaAkanDatang->judul }}"</p>
                    </div>
                </div>
            @else
                {{-- Fallback jika belum ada jadwal --}}
                <div class="inline-flex items-center gap-2 px-6 py-3 bg-slate-50 text-slate-500 rounded-full text-sm font-medium border border-slate-100 mb-12">
                    <i class='bx bx-info-circle text-xl'></i>
                    Belum ada jadwal pendaftaran yang dirilis.
                </div>
            @endif

            {{-- 4. Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                
                <a href="{{ route('home') }}" class="w-full sm:w-auto px-8 py-4 bg-white text-slate-700 font-bold rounded-xl border border-slate-200 hover:bg-slate-50 hover:border-slate-300 hover:text-blue-600 transition-all duration-300 flex items-center justify-center gap-2 shadow-sm">
                    <i class='bx bx-home-alt text-xl'></i> 
                    Kembali ke Beranda
                </a>

                <a href="{{ route('ppdb.cek_status') }}" class="w-full sm:w-auto px-8 py-4 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-600/30 hover:shadow-blue-600/40 transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class='bx bx-refresh text-2xl'></i>
                    Cek Status Lagi
                </a>

            </div>

        </div>
    </div>
</section>

@push('styles')
<style>
    /* Animasi Lambat untuk Ilustrasi */
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0) rotate(3deg); }
        50% { transform: translateY(-10px) rotate(6deg); }
    }
    .animate-bounce-slow {
        animation: bounce-slow 4s infinite ease-in-out;
    }

    /* Animasi Lonceng */
    @keyframes swing {
        0% { transform: rotate(0deg); }
        20% { transform: rotate(15deg); }
        40% { transform: rotate(-10deg); }
        60% { transform: rotate(5deg); }
        80% { transform: rotate(-5deg); }
        100% { transform: rotate(0deg); }
    }
    .animate-swing {
        animation: swing 2s infinite ease-in-out;
        transform-origin: top center;
    }
</style>
@endpush

@endsection