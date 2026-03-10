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
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Cek Role (Sama seperti logic di View)
        // Admin & Operator butuh data statistik lengkap
        $isAdmin = in_array(strtolower($user->role ?? ''), ['admin', 'administrator', 'operator kcd']);

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
        // Blokir akses jika pegawai nyasar ke URL admin
        if (!$isAdmin) {
            return redirect()->route('aasdmin.dhboard.pegawai');
        }

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
        $data['totalTendik'] = Gtk::where(function($q) {
            $q->where('jenis_ptk_id_str', 'not like', '%Guru%')
              ->orWhereNull('jenis_ptk_id_str');
        })->count();

        // E. STATISTIK SISWA
        $data['totalSiswa'] = Siswa::where('status', 'Aktif')->count();
        $data['siswaLaki']  = Siswa::where('status', 'Aktif')->whereIn('jenis_kelamin', ['L', 'Laki-laki'])->count();
        $data['siswaPerempuan'] = Siswa::where('status', 'Aktif')->whereIn('jenis_kelamin', ['P', 'Perempuan'])->count();

        // F. DATA DROPDOWN FILTER CHART
        $data['listKabupaten'] = Sekolah::select('kabupaten_kota')
            ->whereNotNull('kabupaten_kota')
            ->distinct()
            ->orderBy('kabupaten_kota')
            ->pluck('kabupaten_kota');

        // G. CHART DATA: Sebaran Sekolah per Kecamatan
        $chartQuery = Sekolah::query();
        
        if ($request->filled('filter_kabupaten')) {
            $chartQuery->where('kabupaten_kota', $request->filter_kabupaten);
        }

        $dataChart = $chartQuery->select('kecamatan', DB::raw('count(*) as total'))
            ->whereNotNull('kecamatan')
            ->groupBy('kecamatan')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $data['chartCategories'] = $dataChart->pluck('kecamatan')->toArray(); 
        $data['chartData']       = $dataChart->pluck('total')->toArray();

        // H. INFO SEKOLAH TERBARU (SINKRONISASI DARI SYNC_LOGS)
        $sekolahTerbaruQuery = Sekolah::select('sekolahs.nama', 'sekolahs.kecamatan', 'sekolahs.kabupaten_kota')
            ->addSelect([
                'terakhir_sinkron' => DB::table('sync_logs')
                    ->select('updated_at')
                    ->whereColumn('sync_logs.npsn', 'sekolahs.npsn')
                    ->orderByDesc('updated_at')
                    ->limit(1)
            ])
            ->havingRaw('terakhir_sinkron IS NOT NULL'); 

        if ($request->filled('filter_kabupaten')) {
            $sekolahTerbaruQuery->where('kabupaten_kota', $request->filter_kabupaten);
        }

        $data['sekolahTerbaru'] = $sekolahTerbaruQuery->orderByDesc('terakhir_sinkron')->limit(5)->get();

        return view('admin.dashboard', $data);
    }

    public function indexPegawai(Request $request)
    {
        $user = Auth::user();
        
        // Cek Role
        $isAdmin = in_array(strtolower($user->role ?? ''), ['admin', 'administrator', 'operator kcd']);
        if ($isAdmin) {
            return redirect()->route('admin.dashboard');
        }

        $instansi = Instansi::first(); 
        if (!$instansi) {
            $instansi = new Instansi();
            $instansi->nama_instansi = 'KCD Wilayah';
        }

        $data = [
            'instansi'    => $instansi,
            'user'        => $user,
        ];

        // ------------------------------------------------------------------
        // LOGIC KHUSUS PEGAWAI (Verifikator)
        // ------------------------------------------------------------------
        $data['verifikasiLink'] = route('admin.verifikasi.index'); 
        $data['kategoriTugasUser'] = []; 

        if ($user->pegawai_kcd_id) {
            $tugas = \App\Models\TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)
                                                ->where('is_active', 1)
                                                ->first();

            if ($tugas) {
                $kategoriUser = $tugas->kategori_layanan; 
                $data['kategoriTugasUser'] = $kategoriUser;

                $hasGeneralAccess = collect($kategoriUser)->contains(fn($k) => in_array(strtolower($k), ['umum', 'all']));

                if ($hasGeneralAccess) {
                    $data['verifikasiLink'] = route('admin.verifikasi.index'); 
                } elseif (!empty($kategoriUser)) {
                    $firstKategori = $kategoriUser[0];
                    $data['verifikasiLink'] = route('admin.verifikasi.index', ['kategori' => $firstKategori]);
                }
            }
        }

        return view('admin.dashboard_pegawai', $data);
    }
}