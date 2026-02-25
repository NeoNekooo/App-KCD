<?php

namespace App\Http\Controllers\Admin\Sekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\Gtk;
use App\Models\Rombel;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SekolahExport;

class SekolahController extends Controller
{
    /**
     * Menampilkan Daftar Sekolah (Monitoring)
     */
    public function index(Request $request)
    {
        // 🔥 AKAL-AKALAN SUBQUERY: Ambil data sekolah + tanggal terakhir sinkron dari tabel sync_logs
        $query = Sekolah::select('sekolahs.*')
            ->addSelect([
                'terakhir_sinkron' => DB::table('sync_logs')
                    ->select('updated_at')
                    ->whereColumn('sync_logs.npsn', 'sekolahs.npsn')
                    ->orderByDesc('updated_at')
                    ->limit(1)
            ]);

        // --- 1. FILTER DATA UTAMA (Query untuk Tabel) ---
        $query->when($request->filled('kabupaten_kota'), fn($q) => $q->where('kabupaten_kota', $request->kabupaten_kota));
        $query->when($request->filled('kecamatan'), fn($q) => $q->where('kecamatan', $request->kecamatan));
        $query->when($request->filled('jenjang'), fn($q) => $q->where('bentuk_pendidikan_id_str', $request->jenjang));
        $query->when($request->filled('status_sekolah'), fn($q) => $q->where('status_sekolah_str', $request->status_sekolah));
        
        // --- 2. SEARCH ---
        $query->when($request->search, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('npsn', 'like', "%{$search}%");
            });
        });

        // --- 3. PAGINATION ---
        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') $perPage = $query->count() > 0 ? $query->count() : 15;

        $sekolahs = $query->orderBy('nama', 'asc')->paginate($perPage)->appends($request->all());

        // ==========================================================
        // LOGIKA DROPDOWN BERANTAI (DEPENDENT DROPDOWN)
        // ==========================================================

        // A. List Kabupaten (Selalu Muncul Semua)
        $listKabupaten = Sekolah::whereNotNull('kabupaten_kota')
            ->distinct()
            ->orderBy('kabupaten_kota')
            ->pluck('kabupaten_kota');
        
        // B. List Kecamatan (Hanya muncul yang ada di Kabupaten terpilih)
        $kecQuery = Sekolah::whereNotNull('kecamatan');
        if($request->filled('kabupaten_kota')) {
            $kecQuery->where('kabupaten_kota', $request->kabupaten_kota);
        }
        $listKecamatan = $kecQuery->distinct()->orderBy('kecamatan')->pluck('kecamatan');

        // C. List Jenjang (Filter by Kabupaten & Kecamatan terpilih)
        $jenjangQuery = Sekolah::whereNotNull('bentuk_pendidikan_id_str');
        if($request->filled('kabupaten_kota')) $jenjangQuery->where('kabupaten_kota', $request->kabupaten_kota);
        if($request->filled('kecamatan')) $jenjangQuery->where('kecamatan', $request->kecamatan);
        $listJenjang = $jenjangQuery->distinct()->orderBy('bentuk_pendidikan_id_str', 'asc')->pluck('bentuk_pendidikan_id_str');

        // D. List Status (Filter by Kab, Kec, & Jenjang terpilih)
        $statusQuery = Sekolah::whereNotNull('status_sekolah_str');
        if($request->filled('kabupaten_kota')) $statusQuery->where('kabupaten_kota', $request->kabupaten_kota);
        if($request->filled('kecamatan')) $statusQuery->where('kecamatan', $request->kecamatan);
        if($request->filled('jenjang')) $statusQuery->where('bentuk_pendidikan_id_str', $request->jenjang);
        $listStatus = $statusQuery->distinct()->orderBy('status_sekolah_str', 'asc')->pluck('status_sekolah_str');

        // --- STATISTIK HEADER ---
        $totalSekolah = Sekolah::count();
        $totalNegeri  = Sekolah::where('status_sekolah_str', 'LIKE', '%Negeri%')->count();
        $totalSwasta  = Sekolah::where('status_sekolah_str', 'LIKE', '%Swasta%')->count();

        return view('admin.sekolah.index', compact(
            'sekolahs', 'listKabupaten', 'listKecamatan', 'listJenjang', 'listStatus',
            'totalSekolah', 'totalNegeri', 'totalSwasta'
        ));
    }

    /**
     * Menampilkan Detail Sekolah
     */
    public function show($id)
    {
        // 1. Ambil Data Sekolah
        $sekolah = Sekolah::findOrFail($id);
        
        // 2. Ambil UUID Sekolah (Kunci Relasi di tabel penggunas)
        $uuidSekolah = $sekolah->sekolah_id;

        // --- HITUNG STATISTIK (using direct sekolah_id) ---
        
        // A. HITUNG SISWA AKTIF
        $totalSiswa = Siswa::where('sekolah_id', $uuidSekolah)
            ->where('status', 'Aktif')
            ->count();

        // B. HITUNG GURU
        $totalGuru = Gtk::where('sekolah_id', $uuidSekolah)
            ->where('jenis_ptk_id_str', 'like', '%Guru%') 
            ->count();

        // C. HITUNG TENDIK
        $totalTendik = Gtk::where('sekolah_id', $uuidSekolah)
            ->where(function($q) {
                $q->where('jenis_ptk_id_str', 'not like', '%Guru%')
                  ->orWhereNull('jenis_ptk_id_str');
            })
            ->count();

        // D. CARI KEPALA SEKOLAH
        $kepalaSekolah = Gtk::where('sekolah_id', $uuidSekolah)
            ->where(function($query) {
                $query->where('jenis_ptk_id_str', 'LIKE', '%Kepala Sekolah%')
                      ->orWhere('jabatan_ptk_id_str', 'LIKE', '%Kepala Sekolah%'); 
            })
            ->select('nama', 'nip', 'nuptk', 'no_hp')
            ->first();

        // E. Total Rombel
        $totalRombel = Rombel::where('sekolah_id', $uuidSekolah)->count(); 

        return view('admin.sekolah.show', compact(
            'sekolah', 'totalSiswa', 'totalGuru', 'totalTendik', 'totalRombel', 'kepalaSekolah'
        ));
    }

    /**
     * Export Excel Data Sekolah
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(new SekolahExport($request), 'Data_Satuan_Pendidikan.xlsx');
    }
}