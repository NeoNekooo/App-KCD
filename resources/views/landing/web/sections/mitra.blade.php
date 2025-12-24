{{-- FILE: resources/views/landing/web/sections/mitra.blade.php --}}

@if(isset($mitras) && $mitras->count() > 0)
<section id="mitra" class="py-20 bg-gradient-to-b from-white to-blue-50 relative overflow-hidden">
    
    <div class="container mx-auto px-6 md:px-12">
        {{-- Header --}}
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-3">Mitra Industri & Kerjasama</h2>
            <p class="text-gray-500">Bekerja sama dengan perusahaan terkemuka untuk penyaluran lulusan.</p>
        </div>

        {{-- SLIDING CONTAINER --}}
        <div id="mitra-slider" class="flex gap-6 overflow-x-auto pb-8 snap-x snap-mandatory hide-scrollbar cursor-grab active:cursor-grabbing">
            
            @foreach($mitras as $mitra)
            
            {{-- LOGIKA GAMBAR (Disamakan dengan Backend: folder 'mitras') --}}
            @php
                $logoUrl = asset('storage/mitras/'.$mitra->logo);
            @endphp

            <div class="snap-center shrink-0 w-[220px] md:w-[260px]">
                <div class="bg-white rounded-2xl p-6 h-full border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 flex flex-col items-center text-center group">
                    
                    {{-- Logo Container --}}
                    <div class="h-24 w-full flex items-center justify-center mb-4 grayscale group-hover:grayscale-0 transition-all duration-500">
                        <img src="{{ $logoUrl }}" 
                             alt="{{ $mitra->nama_mitra }}" 
                             class="max-h-full max-w-full object-contain drop-shadow-sm group-hover:drop-shadow-md transform group-hover:scale-110 transition-transform"
                             onerror="this.src='https://placehold.co/200x100/f1f5f9/94a3b8?text=MITRA'">
                    </div>
                    
                    {{-- Nama Mitra --}}
                    <h4 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors mb-1 line-clamp-1" title="{{ $mitra->nama_mitra }}">
                        {{ $mitra->nama_mitra }}
                    </h4>
                    
                    {{-- Bidang Kerjasama (Jika ada) --}}
                    @if($mitra->bidang_kerjasama)
                        <span class="text-xs text-gray-400 font-medium mb-3 line-clamp-1">{{ $mitra->bidang_kerjasama }}</span>
                    @endif
                    
                    {{-- Label Kecil --}}
                    <span class="px-2 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-wide rounded-md mt-auto">
                        Official Partner
                    </span>

                </div>
            </div>
            @endforeach

        </div>

        {{-- Navigasi Manual (Kiri/Kanan) --}}
        <div class="flex justify-center gap-4 mt-2">
            <button onclick="scrollMitra('left')" class="w-10 h-10 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-blue-600 hover:text-white shadow-sm flex items-center justify-center transition-all">
                <i class='bx bx-chevron-left text-2xl'></i>
            </button>
            <button onclick="scrollMitra('right')" class="w-10 h-10 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-blue-600 hover:text-white shadow-sm flex items-center justify-center transition-all">
                <i class='bx bx-chevron-right text-2xl'></i>
            </button>
        </div>

    </div>
</section>

{{-- CSS Khusus --}}
<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

{{-- Script Geser --}}
<script>
    function scrollMitra(direction) {
        const container = document.getElementById('mitra-slider');
        const scrollAmount = 300; 
        if (direction === 'left') {
            container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        } else {
            container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    }
</script>
@endif