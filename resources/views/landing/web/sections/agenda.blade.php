<section id="agenda" class="py-20 bg-slate-50 relative overflow-hidden">
    {{-- Hiasan Background --}}
    <div class="absolute top-0 left-0 w-64 h-64 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-64 h-64 bg-orange-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 translate-x-1/2 translate-y-1/2"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

            {{-- KOLOM KIRI: INFO & LEGENDA --}}
            <div class="lg:col-span-4 bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                <div class="mb-6">
                    <span class="inline-block py-1 px-3 rounded-full bg-indigo-50 text-indigo-600 text-xs font-bold uppercase tracking-wider mb-3">
                        Kalender Akademik
                    </span>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4 leading-tight">
                        Jangan Lewatkan <br> <span class="text-indigo-600">Agenda Penting</span>
                    </h2>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                        Pantau jadwal kegiatan akademik, libur sekolah, dan acara penting lainnya melalui kalender interaktif ini. Klik tanggal untuk melihat detail.
                    </p>
                </div>

                {{-- Legenda Kategori --}}
                <div class="space-y-3 border-t border-gray-100 pt-6">
                    <h4 class="text-sm font-bold text-gray-700 mb-2">Keterangan Warna:</h4>
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="w-3 h-3 rounded-full bg-blue-500 mr-3 shadow-sm shadow-blue-200"></span>
                        Akademik (Ujian/Rapor)
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="w-3 h-3 rounded-full bg-red-500 mr-3 shadow-sm shadow-red-200"></span>
                        Libur Nasional / Cuti
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="w-3 h-3 rounded-full bg-green-500 mr-3 shadow-sm shadow-green-200"></span>
                        Kegiatan Sekolah
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="w-3 h-3 rounded-full bg-yellow-400 mr-3 shadow-sm shadow-yellow-200"></span>
                        Rapat Guru / Wali Murid
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="w-3 h-3 rounded-full bg-[#03c3ec] mr-3 shadow-sm shadow-[#03c3ec]/40"></span>
                        Informasi SPMB
                    </div>
                </div>

                {{-- Button Aksi --}}
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <a href="/spmb" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Lihat Jadwal PPDB
                    </a>
                </div>
            </div>

            {{-- KOLOM KANAN: KALENDER --}}
            <div class="lg:col-span-8">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden p-4 md:p-6">
                    <div id="calendar" class="font-sans text-gray-700 text-sm"></div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- LOAD LIBRARY DARI CDN --}}
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- BLOK PHP UNTUK MEMPROSES DATA SEBELUM DI-RENDER KE JS --}}
@php
    // Kita proses data di sini agar bersih dan tidak merusak sintaks Blade/JS
    $agendaEvents = $agendas->map(function($agenda) {
        $color = '#eab308'; // Default kuning
        if ($agenda->kategori == 'Libur') $color = '#ef4444';
        elseif ($agenda->kategori == 'Akademik') $color = '#3b82f6';
        elseif ($agenda->kategori == 'Kegiatan') $color = '#22c55e';
        elseif ($agenda->kategori == 'PPDB') $color = '#03c3ec';

        return [
            'title' => $agenda->judul,
            // Pastikan format tanggal Y-m-d
            'start' => $agenda->tanggal_mulai instanceof \DateTime ? $agenda->tanggal_mulai->format('Y-m-d') : $agenda->tanggal_mulai,
            'end' => $agenda->tanggal_selesai 
                ? \Carbon\Carbon::parse($agenda->tanggal_selesai)->addDay()->format('Y-m-d') 
                : ($agenda->tanggal_mulai instanceof \DateTime ? $agenda->tanggal_mulai->format('Y-m-d') : $agenda->tanggal_mulai),
            'backgroundColor' => $color,
            'borderColor' => 'transparent',
            'textColor' => '#ffffff',
            'classNames' => ['cursor-pointer', 'hover:opacity-80', 'transition'],
            'extendedProps' => [
                'kategori' => $agenda->kategori,
                'deskripsi' => $agenda->deskripsi ?? 'Tidak ada deskripsi detail.'
            ]
        ];
    });
@endphp

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    // Ambil data dari variabel PHP yang sudah diproses di atas
    var agendaEvents = @json($agendaEvents);

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: window.innerWidth < 768 ? 'listMonth' : 'dayGridMonth',
        locale: 'id',
        themeSystem: 'standard',
        height: 'auto',
        contentHeight: 'auto',
        aspectRatio: 1.8,

        headerToolbar: {
            left: 'title',
            center: '',
            right: 'prev,next today'
        },

        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            list: 'List'
        },

        events: agendaEvents,

        eventClick: function(info) {
            const dateOptions = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
            const dateString = info.event.start.toLocaleDateString('id-ID', dateOptions);

            Swal.fire({
                title: info.event.title,
                html: `
                    <div class="text-left mt-2 text-sm">
                        <div class="mb-3 p-2 bg-gray-50 rounded border border-gray-100">
                            <span class="font-bold block text-gray-500 text-xs uppercase">Waktu Pelaksanaan</span>
                            <span class="text-gray-800 font-medium">${dateString}</span>
                        </div>
                        <div class="mb-3">
                            <span class="font-bold block text-gray-500 text-xs uppercase mb-1">Kategori</span>
                            <span class="inline-block px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs border border-gray-200">
                                ${info.event.extendedProps.kategori}
                            </span>
                        </div>
                        <div>
                            <span class="font-bold block text-gray-500 text-xs uppercase mb-1">Deskripsi</span>
                            <p class="text-gray-600 leading-relaxed">${info.event.extendedProps.deskripsi}</p>
                        </div>
                    </div>
                `,
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-xl'
                }
            });
        },

        windowResize: function(view) {
            if (window.innerWidth < 768) {
                calendar.changeView('listMonth');
            } else {
                calendar.changeView('dayGridMonth');
            }
        }
    });

    calendar.render();
});
</script>

<style>
    /* Styling Custom FullCalendar */
    .fc-toolbar-title {
        font-size: 1.25rem !important;
        font-weight: 800;
        color: #1f2937;
        text-transform: capitalize;
    }
    .fc-button-primary {
        background-color: white !important;
        border: 1px solid #e5e7eb !important;
        color: #4b5563 !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.35rem 0.75rem;
        border-radius: 0.5rem !important;
        margin-left: 4px !important;
    }
    .fc-button-primary:hover {
        background-color: #f9fafb !important;
        color: #4f46e5 !important;
    }
    .fc-button-active {
        background-color: #4f46e5 !important;
        color: white !important;
        border-color: #4f46e5 !important;
    }
    .fc th {
        padding: 10px 0;
        background-color: #f9fafb;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        border-bottom: 0 !important;
    }
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #f3f4f6;
    }
    .fc-daygrid-day-number {
        color: #374151;
        font-weight: 500;
        padding: 8px;
    }
    .fc-day-today {
        background-color: #f5f3ff !important;
    }
    .fc-event {
        border-radius: 4px;
        padding: 1px 4px;
        font-size: 0.75rem;
        margin-bottom: 2px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        
        border: none;
    }
</style>