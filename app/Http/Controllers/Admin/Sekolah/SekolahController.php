<?php

namespace App\Http\Controllers\Admin\Sekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Wajib ada untuk Query Builder
use App\Models\Sekolah;

class SekolahController extends Controller
{
    /**
     * Menampilkan Daftar Sekolah
     */
    public function index(Request $request)
    {
        $query = Sekolah::query();

        // --- FILTER ---
        $query->when($request->filled('kabupaten_kota'), fn($q) => $q->where('kabupaten_kota', $request->kabupaten_kota));
        $query->when($request->filled('kecamatan'), fn($q) => $q->where('kecamatan', $request->kecamatan));
        $query->when($request->filled('jenjang'), fn($q) => $q->where('bentuk_pendidikan_id_str', $request->jenjang));
        $query->when($request->filled('status_sekolah'), fn($q) => $q->where('status_sekolah_str', $request->status_sekolah));
        
        $query->when($request->search, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('npsn', 'like', "%{$search}%");
            });
        });

        // --- PAGINASI ---
        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') $perPage = $query->count() > 0 ? $query->count() : 15;

        $sekolahs = $query->orderBy('nama', 'asc')->paginate($perPage)->appends($request->all());

        // --- DATA PENDUKUNG ---
        $listKabupaten = Sekolah::whereNotNull('kabupaten_kota')->distinct()->pluck('kabupaten_kota');
        
        $kecQuery = Sekolah::whereNotNull('kecamatan');
        if($request->filled('kabupaten_kota')) $kecQuery->where('kabupaten_kota', $request->kabupaten_kota);
        $listKecamatan = $kecQuery->distinct()->pluck('kecamatan');

        $listJenjang = Sekolah::whereNotNull('bentuk_pendidikan_id_str')->distinct()->orderBy('bentuk_pendidikan_id_str', 'asc')->pluck('bentuk_pendidikan_id_str');
        $listStatus = Sekolah::whereNotNull('status_sekolah_str')->distinct()->orderBy('status_sekolah_str', 'asc')->pluck('status_sekolah_str');

        // Statistik Header
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

        // --- A. HITUNG GURU (Via Relasi Pengguna) ---
        // Join tabel 'penggunas' dengan 'gtks' berdasarkan ptk_id
        $totalGuru = DB::table('penggunas')
            ->join('gtks', 'penggunas.ptk_id', '=', 'gtks.ptk_id')
            ->where('penggunas.sekolah_id', $uuidSekolah)
            ->where('gtks.jenis_ptk_id_str', 'like', '%Guru%') // Filter Guru
            ->count();

        // --- B. HITUNG SISWA (Via Relasi Pengguna) ---
        // Join tabel 'penggunas' dengan 'siswas' berdasarkan peserta_didik_id
        $totalSiswa = DB::table('penggunas')
            ->join('siswas', 'penggunas.peserta_didik_id', '=', 'siswas.peserta_didik_id')
            ->where('penggunas.sekolah_id', $uuidSekolah)
            ->where('siswas.status', 'Aktif') // Hanya siswa aktif
            ->count();

        // --- C. HITUNG TENDIK (Opsional) ---
        $totalTendik = DB::table('penggunas')
            ->join('gtks', 'penggunas.ptk_id', '=', 'gtks.ptk_id')
            ->where('penggunas.sekolah_id', $uuidSekolah)
            ->where(function($q) {
                $q->where('gtks.jenis_ptk_id_str', 'not like', '%Guru%')
                  ->orWhereNull('gtks.jenis_ptk_id_str');
            })
            ->count();

        // --- D. HITUNG ROMBEL (Diksonongkan sesuai request) ---
        $totalRombel = 0;

        return view('admin.sekolah.show', compact(
            'sekolah', 'totalSiswa', 'totalGuru', 'totalTendik', 'totalRombel'
        ));
    }
}