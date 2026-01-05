<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\Gtk;
use App\Models\Sekolah;
use App\Models\Instansi;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. DATA INSTANSI
        $instansi = Instansi::first(); 
        if (!$instansi) {
            $instansi = new Instansi();
            $instansi->nama_instansi = 'KCD Wilayah';
        }

        // 2. DATA WILAYAH (Untuk ditaruh di Header)
        $totalKabupaten = Sekolah::distinct('kabupaten_kota')->whereNotNull('kabupaten_kota')->count('kabupaten_kota');
        $totalKecamatan = Sekolah::distinct('kecamatan')->whereNotNull('kecamatan')->count('kecamatan');

        // 3. STATISTIK UTAMA
        
        // A. Satuan Pendidikan (Total, Negeri, Swasta)
        $totalSekolah = Sekolah::count();
        $totalNegeri  = Sekolah::where('status_sekolah_str', 'LIKE', '%Negeri%')->count();
        $totalSwasta  = Sekolah::where('status_sekolah_str', 'LIKE', '%Swasta%')->count();

        // B. Guru
        $totalGuru = Gtk::where('jenis_ptk_id_str', 'like', '%Guru%')->count();

        // C. Tendik
        $totalTendik = Gtk::where(function($q) {
            $q->where('jenis_ptk_id_str', 'not like', '%Guru%')
              ->orWhereNull('jenis_ptk_id_str');
        })->count();

        // D. Siswa
        $totalSiswa = Siswa::where('status', 'Aktif')->count();

        // 4. DATA PENDUKUNG
        $siswaLaki = Siswa::where('status', 'Aktif')->whereIn('jenis_kelamin', ['L', 'Laki-laki'])->count();
        $siswaPerempuan = Siswa::where('status', 'Aktif')->whereIn('jenis_kelamin', ['P', 'Perempuan'])->count();

        $guruASN = Gtk::where('jenis_ptk_id_str', 'like', '%Guru%')
                      ->where(function($q) {
                          $q->where('status_kepegawaian_id_str', 'like', '%PNS%')
                            ->orWhere('status_kepegawaian_id_str', 'like', '%PPPK%')
                            ->orWhere('status_kepegawaian_id_str', 'like', '%CPNS%');
                      })->count();
        $guruNonASN = $totalGuru - $guruASN;

        // 5. CHART DATA
        $dataChart = Sekolah::select('kecamatan', DB::raw('count(*) as total'))
            ->whereNotNull('kecamatan')
            ->groupBy('kecamatan')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $chartCategories = $dataChart->pluck('kecamatan')->toArray(); 
        $chartData = $dataChart->pluck('total')->toArray();           

        // 6. Tahun Ajaran
        $currMonth = date('n');
        $currYear = date('Y');
        $tahunAjaran = ($currMonth > 6) ? "$currYear/" . ($currYear + 1) : ($currYear - 1) . "/$currYear";

        return view('admin.dashboard', compact(
            'instansi',
            'totalSekolah', 'totalNegeri', 'totalSwasta',       // Card 1
            'totalKabupaten', 'totalKecamatan',                 // Header
            'totalGuru', 'guruASN', 'guruNonASN',               // Card 2
            'totalTendik',                                      // Card 3
            'totalSiswa', 'siswaLaki', 'siswaPerempuan',        // Card 4
            'chartCategories', 'chartData',
            'tahunAjaran'
        ));
    }
}