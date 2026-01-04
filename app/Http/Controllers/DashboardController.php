<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\Gtk;
use App\Models\Sekolah;
use App\Models\Instansi; // Pastikan model ini sudah dibuat
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. DATA INSTANSI (Mengambil profil KCD dari tabel instansis)
        $instansi = Instansi::first(); 
        
        // Proteksi jika data instansi masih kosong di database
        if (!$instansi) {
            $instansi = new Instansi();
            $instansi->nama_instansi = 'KCD Wilayah';
        }

        // 2. STATISTIK UTAMA (4 KOTAK)
        $totalSekolah = Sekolah::count();
        $totalSiswa = Siswa::where('status', 'Aktif')->count();
        $totalGuru = Gtk::count();

        // Hitung Wilayah berdasarkan data sekolah yang ada
        $totalKabupaten = Sekolah::distinct('kabupaten_kota')
                                 ->whereNotNull('kabupaten_kota')
                                 ->count('kabupaten_kota');

        $totalKecamatan = Sekolah::distinct('kecamatan')
                                 ->whereNotNull('kecamatan')
                                 ->count('kecamatan');

        // 3. DATA PENDUKUNG (Gender & Status Guru)
        $siswaLaki = Siswa::where('status', 'Aktif')->whereIn('jenis_kelamin', ['L', 'Laki-laki'])->count();
        $siswaPerempuan = Siswa::where('status', 'Aktif')->whereIn('jenis_kelamin', ['P', 'Perempuan'])->count();

        $guruASN = Gtk::where('status_kepegawaian_id_str', 'like', '%PNS%')
                      ->orWhere('status_kepegawaian_id_str', 'like', '%PPPK%')
                      ->orWhere('status_kepegawaian_id_str', 'like', '%CPNS%')
                      ->count();
        $guruNonASN = $totalGuru - $guruASN;

        // 4. CHART DATA (Sebaran Sekolah per Kecamatan)
        $dataChart = Sekolah::select('kecamatan', DB::raw('count(*) as total'))
            ->whereNotNull('kecamatan')
            ->groupBy('kecamatan')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $chartCategories = $dataChart->pluck('kecamatan')->toArray(); 
        $chartData = $dataChart->pluck('total')->toArray();           

        // 5. Tahun Ajaran Dinamis
        $currMonth = date('n');
        $currYear = date('Y');
        $tahunAjaran = ($currMonth > 6) ? "$currYear/" . ($currYear + 1) : ($currYear - 1) . "/$currYear";

        return view('admin.dashboard', compact(
            'instansi',
            'totalSekolah',
            'totalSiswa', 'siswaLaki', 'siswaPerempuan',
            'totalGuru', 'guruASN', 'guruNonASN',
            'totalKabupaten', 'totalKecamatan', 
            'chartCategories', 'chartData',
            'tahunAjaran'
        ));
    }
}