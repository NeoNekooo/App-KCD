<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Pastikan Model di-import dengan benar
use App\Models\Siswa;
use App\Models\Gtk;
use App\Models\Sekolah;
use App\Models\Instansi;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Cek Role (Sama seperti logic di View)
        // Admin & Operator butuh data statistik lengkap
        $isAdmin = ($user->role === 'Admin' || $user->role === 'Operator KCD');

        // ------------------------------------------------------------------
        // 1. DATA UMUM (Wajib ada untuk SEMUA user: Admin & Pegawai)
        // ------------------------------------------------------------------
        
        // Ambil Profil Instansi (untuk Logo & Judul Dashboard)
        $instansi = Instansi::first(); 
        if (!$instansi) {
            $instansi = new Instansi();
            $instansi->nama_instansi = 'KCD Wilayah';
        }

        // Hitung Tahun Ajaran (Untuk ditampilkan di view jika perlu)
        $currMonth = date('n');
        $currYear = date('Y');
        $tahunAjaran = ($currMonth > 6) ? "$currYear/" . ($currYear + 1) : ($currYear - 1) . "/$currYear";

        // Siapkan variabel dasar untuk dikirim ke View
        $data = [
            'instansi'    => $instansi,
            'tahunAjaran' => $tahunAjaran,
            'user'        => $user,
        ];

        // ------------------------------------------------------------------
        // 2. LOGIC KHUSUS ADMIN / OPERATOR (Heavy Queries)
        // ------------------------------------------------------------------
        // Kalau Pegawai login, blok kode ini DILONCATI (Skipped) biar ringan.
        
        if ($isAdmin) {
            
            // A. DATA WILAYAH (Header)
            $data['totalKabupaten'] = Sekolah::distinct('kabupaten_kota')->whereNotNull('kabupaten_kota')->count('kabupaten_kota');
            $data['totalKecamatan'] = Sekolah::distinct('kecamatan')->whereNotNull('kecamatan')->count('kecamatan');

            // B. STATISTIK SEKOLAH
            $data['totalSekolah'] = Sekolah::count();
            $data['totalNegeri']  = Sekolah::where('status_sekolah_str', 'LIKE', '%Negeri%')->count();
            $data['totalSwasta']  = Sekolah::where('status_sekolah_str', 'LIKE', '%Swasta%')->count();

            // C. STATISTIK GURU (ASN vs Non-ASN)
            $data['totalGuru'] = Gtk::where('jenis_ptk_id_str', 'like', '%Guru%')->count();
            
            $data['guruASN'] = Gtk::where('jenis_ptk_id_str', 'like', '%Guru%')
                ->where(function($q) {
                    $q->where('status_kepegawaian_id_str', 'like', '%PNS%')
                      ->orWhere('status_kepegawaian_id_str', 'like', '%PPPK%')
                      ->orWhere('status_kepegawaian_id_str', 'like', '%CPNS%');
                })->count();
            
            $data['guruNonASN'] = $data['totalGuru'] - $data['guruASN'];

            // D. STATISTIK TENDIK
            // Mengambil GTK yang BUKAN Guru
            $data['totalTendik'] = Gtk::where(function($q) {
                $q->where('jenis_ptk_id_str', 'not like', '%Guru%')
                  ->orWhereNull('jenis_ptk_id_str');
            })->count();

            // E. STATISTIK SISWA
            $data['totalSiswa'] = Siswa::where('status', 'Aktif')->count();
            $data['siswaLaki']  = Siswa::where('status', 'Aktif')->whereIn('jenis_kelamin', ['L', 'Laki-laki'])->count();
            $data['siswaPerempuan'] = Siswa::where('status', 'Aktif')->whereIn('jenis_kelamin', ['P', 'Perempuan'])->count();

            // F. CHART DATA (Query Grouping)
            // Mengambil Top 10 Kecamatan dengan sekolah terbanyak untuk grafik
            $dataChart = Sekolah::select('kecamatan', DB::raw('count(*) as total'))
                ->whereNotNull('kecamatan')
                ->groupBy('kecamatan')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            $data['chartCategories'] = $dataChart->pluck('kecamatan')->toArray(); 
            $data['chartData']       = $dataChart->pluck('total')->toArray();
        }

        // 3. Return View dengan Data yang sudah disiapkan
        // Sesuaikan nama view dengan struktur folder kamu (admin.dashboard)
        return view('admin.dashboard', $data);
    }
}
