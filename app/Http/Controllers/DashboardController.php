<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Siswa;
use App\Models\Gtk;
use App\Models\Rombel; // Tetap dipakai untuk hitung total rombel se-wilayah
use App\Models\Sekolah;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. IDENTITAS WILAYAH (KCD)
        // Ambil data sekolah induk (atau KCD jika ada tabel khususnya)
        $instansi = Sekolah::firstOrCreate(['id' => 1]); 
        
        // 2. STATISTIK UTAMA (KCD VIEW)
        
        // A. Total Satuan Pendidikan (Sekolah Binaan)
        // Jika aplikasi ini menampung banyak sekolah, hitung jumlah row di tabel Sekolah.
        // Jika aplikasi ini single-tenant tapi ingin terlihat KCD, kita hitung Rombel sebagai 'Kelompok Belajar'.
        $totalSekolah = Sekolah::count(); 

        // B. Total Peserta Didik (Se-Wilayah)
        $totalSiswa = Siswa::where('status', 'Aktif')->count();
        
        // C. Total GTK (Se-Wilayah)
        $totalGuru = Gtk::count();

        // D. Cakupan Wilayah (Kecamatan)
        // Menghitung ada berapa kecamatan unik dari data domisili siswa
        $totalKecamatan = Siswa::where('status', 'Aktif')
            ->whereNotNull('kecamatan')
            ->distinct('kecamatan')
            ->count('kecamatan');

        // 3. STATISTIK RINCIAN

        // Gender Parity (L/P)
        $siswaLaki = Siswa::where('status', 'Aktif')->whereIn('jenis_kelamin', ['L', 'Laki-laki'])->count();
        $siswaPerempuan = Siswa::where('status', 'Aktif')->whereIn('jenis_kelamin', ['P', 'Perempuan'])->count();

        // Status Kepegawaian (ASN vs Non-ASN)
        $guruASN = Gtk::where('status_kepegawaian_id_str', 'like', '%PNS%')
                    ->orWhere('status_kepegawaian_id_str', 'like', '%PPPK%')
                    ->count();
        $guruNonASN = $totalGuru - $guruASN;

        // 4. CHART: SEBARAN SISWA PER KECAMATAN (Top 5)
        // Ini lebih relevan buat KCD daripada "Siswa per Tahun"
        $sebaranKecamatan = Siswa::select('kecamatan', DB::raw('count(*) as total'))
            ->where('status', 'Aktif')
            ->whereNotNull('kecamatan')
            ->groupBy('kecamatan')
            ->orderByDesc('total')
            ->limit(5) // Ambil 5 kecamatan terbanyak
            ->get();

        $chartCategories = $sebaranKecamatan->pluck('kecamatan')->toArray();
        $chartData = $sebaranKecamatan->pluck('total')->toArray();

        // Tahun Ajaran Dinamis
        $currMonth = date('n');
        $currYear = date('Y');
        $tahunAjaran = ($currMonth > 6) ? "$currYear/" . ($currYear + 1) : ($currYear - 1) . "/$currYear";

        return view('admin.dashboard', compact(
            'instansi',
            'totalSekolah',
            'totalSiswa', 'siswaLaki', 'siswaPerempuan',
            'totalGuru', 'guruASN', 'guruNonASN',
            'totalKecamatan',
            'chartCategories', 'chartData',
            'tahunAjaran'
        ));
    }
}