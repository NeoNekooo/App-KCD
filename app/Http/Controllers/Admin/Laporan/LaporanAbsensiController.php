<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\Siswa;
use App\Models\Rombel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanAbsensiController extends Controller
{
    public function index(Request $request)
    {
        // --- PERBAIKAN DI SINI ---
        // Ambil semua rombel, lalu saring agar namanya unik.
        $rombels = Rombel::orderBy('nama', 'asc')->get()->unique('nama');
        // --- AKHIR PERBAIKAN ---

        // Tentukan rentang tanggal default (bulan ini)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Mulai query absensi siswa dengan data relasinya
        $query = AbsensiSiswa::with(['siswa.rombel'])
                    ->whereBetween('tanggal', [$startDate, $endDate]);

        // Terapkan filter jika ada
        if ($request->filled('rombel_id')) {
            // Perbaikan kecil: Filter berdasarkan rombongan_belajar_id yang unik
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('rombongan_belajar_id', $request->rombel_id);
            });
        }
        if ($request->filled('status')) {
            if ($request->status == 'Terlambat') {
                $query->where('status_kehadiran', 'Terlambat');
            } else {
                $query->where('status', $request->status);
            }
        }
        
        // Ambil hasil query dengan paginasi
        $laporanAbsensi = $query->orderBy('tanggal', 'desc')->paginate(25);

        // Buat data rekapitulasi (summary)
        $rekap = [
            'hadir' => (clone $query)->where('status', 'Hadir')->count(),
            'sakit' => (clone $query)->where('status', 'Sakit')->count(),
            'izin' => (clone $query)->where('status', 'Izin')->count(),
            'alfa' => (clone $query)->where('status', 'Alfa')->count(),
            'terlambat' => (clone $query)->where('status_kehadiran', 'Terlambat')->count(),
        ];

        return view('admin.laporan.absensi.index', compact('laporanAbsensi', 'rombels', 'startDate', 'endDate', 'rekap'));
    }

    public function export(Request $request)
    {
        // Logika untuk ekspor ke Excel akan ditambahkan di sini.
        // Ini memerlukan package tambahan seperti Maatwebsite/Excel.
        // Untuk saat ini, kita bisa redirect kembali dengan pesan.
        return back()->with('info', 'Fitur ekspor sedang dalam pengembangan.');
    }

    public function laporanTanpaPulang()
    {
        // Set judul halaman
        $title = 'Laporan Siswa Tanpa Absen Pulang';

        // Ambil data siswa yang punya jam masuk tapi tidak punya jam pulang
        // untuk semua tanggal SEBELUM hari ini.
        $laporan = AbsensiSiswa::whereNotNull('jam_masuk')
                                ->whereNull('jam_pulang')
                                ->where('tanggal', '<', Carbon::today()->toDateString())
                                ->with('siswa:id,nama') // Ambil data siswa yang relevan saja
                                ->orderBy('tanggal', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->paginate(20);

        // Arahkan ke file view baru yang akan kita buat
        return view('admin.laporan.absensi.tanpa_pulang', compact('laporan', 'title'));
    }

    public function dashboard()
    {
        $today = Carbon::today();
        
        // --- DATA UNTUK GAUGE/KARTU STATISTIK ---
        $totalSiswa = Siswa::count();
        $hadirHariIni = AbsensiSiswa::whereDate('tanggal', $today)
                                    ->whereIn('status', ['Hadir', 'Terlambat']) // Anggap Terlambat juga Hadir
                                    ->distinct('siswa_id')
                                    ->count();
        
        // Hindari pembagian dengan nol jika tidak ada siswa
        $persentaseKehadiran = $totalSiswa > 0 ? round(($hadirHariIni / $totalSiswa) * 100) : 0;

        // --- DATA UNTUK PIE CHART ---
        $rekapStatusHariIni = AbsensiSiswa::whereDate('tanggal', $today)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status'); // Hasilnya: ['Hadir' => 150, 'Sakit' => 5, ...]

        // --- DATA UNTUK BAR CHART (TREN 7 HARI TERAKHIR) ---
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        $trenAbsensi = AbsensiSiswa::whereBetween('tanggal', [$startDate, $endDate])
            ->whereIn('status', ['Sakit', 'Izin', 'Alfa']) // Fokus pada absensi negatif
            ->select(DB::raw('DATE(tanggal) as tanggal'), DB::raw('count(*) as total'))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();
            
        // Proses data untuk Chart.js: pisahkan label (tanggal) dan data (total)
        $labelsTren = [];
        $dataTren = [];
        // Buat rentang tanggal 7 hari agar hari yang tidak ada absensi tetap muncul (nilai 0)
        $period = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate);
        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $labelsTren[] = Carbon::parse($formattedDate)->isoFormat('dd, D MMM'); // Format: Rab, 7 Okt
            
            // Cari data absensi untuk tanggal ini
            $found = $trenAbsensi->firstWhere('tanggal', $formattedDate);
            $dataTren[] = $found ? $found->total : 0;
        }

        // Kirim semua data ke view
        return view('admin.laporan.absensi.dashboard', compact(
            'totalSiswa',
            'hadirHariIni',
            'persentaseKehadiran',
            'rekapStatusHariIni',
            'labelsTren',
            'dataTren'
        ));
    }
}
