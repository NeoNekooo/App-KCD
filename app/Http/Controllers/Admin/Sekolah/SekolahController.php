<?php

namespace App\Http\Controllers\Admin\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\Gtk;
use App\Models\Rombel;
use Illuminate\Http\Request;

class SekolahController extends Controller
{
    /**
     * Menampilkan Daftar Sekolah (Monitoring KCD)
     */
    public function index(Request $request)
    {
        $query = Sekolah::query();

        // --- 1. FILTER LOGIC ---
        
        // Filter Wilayah
        $query->when($request->filled('kabupaten_kota'), function ($q) use ($request) {
            $q->where('kabupaten_kota', $request->kabupaten_kota);
        });

        $query->when($request->filled('kecamatan'), function ($q) use ($request) {
            $q->where('kecamatan', $request->kecamatan);
        });

        // Filter Jenjang (Menggunakan kolom _str agar user bisa baca 'SMK', 'SMA')
        $query->when($request->filled('jenjang'), function ($q) use ($request) {
            $q->where('bentuk_pendidikan_id_str', $request->jenjang);
        });

        // Filter Status (Menggunakan kolom _str 'Negeri'/'Swasta')
        $query->when($request->filled('status_sekolah'), function ($q) use ($request) {
            $q->where('status_sekolah_str', $request->status_sekolah);
        });

        // Filter Pencarian
        $query->when($request->search, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('npsn', 'like', "%{$search}%");
            });
        });

        // --- 2. PAGINASI ---
        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') {
            $perPage = $query->count() > 0 ? $query->count() : 15;
        }

        $sekolahs = $query->orderBy('nama', 'asc')
                          ->paginate($perPage)
                          ->appends($request->all());

        // --- 3. DATA DROPDOWN (Dinamis) ---
        $listKabupaten = Sekolah::whereNotNull('kabupaten_kota')->where('kabupaten_kota', '!=', '')
            ->distinct()->pluck('kabupaten_kota');

        // Filter kecamatan menyesuaikan kabupaten yang dipilih (opsional)
        $kecQuery = Sekolah::whereNotNull('kecamatan')->where('kecamatan', '!=', '');
        if($request->filled('kabupaten_kota')) {
            $kecQuery->where('kabupaten_kota', $request->kabupaten_kota);
        }
        $listKecamatan = $kecQuery->distinct()->pluck('kecamatan');

        $listJenjang = Sekolah::whereNotNull('bentuk_pendidikan_id_str')->where('bentuk_pendidikan_id_str', '!=', '')
            ->distinct()->orderBy('bentuk_pendidikan_id_str', 'asc')->pluck('bentuk_pendidikan_id_str');

        $listStatus = Sekolah::whereNotNull('status_sekolah_str')->where('status_sekolah_str', '!=', '')
            ->distinct()->orderBy('status_sekolah_str', 'asc')->pluck('status_sekolah_str');

        // --- 4. STATISTIK HEADER ---
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
        // 1. Cari Sekolah berdasarkan ID (Angka) dari URL
        $sekolah = Sekolah::findOrFail($id);

        // 2. Ambil UUID Sekolah untuk Query Relasi
        // Karena tabel siswas, gtks, rombels menggunakan UUID 'sekolah_id'
        $uuidSekolah = $sekolah->sekolah_id;

        try {
            // Hitung Siswa (Gunakan UUID)
            $totalSiswa = Siswa::where('sekolah_id', $uuidSekolah)
                                ->where('status', 'Aktif')
                                ->count();

            // Hitung Guru (Gunakan UUID)
            $totalGuru = Gtk::where('sekolah_id', $uuidSekolah)
                             ->where('jenis_ptk_id_str', 'Guru')
                             ->count();

            // Hitung Tendik (Selain Guru - Kode 91, 93, 94, dll)
            $totalTendik = Gtk::where('sekolah_id', $uuidSekolah)
                              ->whereIn('jenis_ptk_id', ['91', '93', '94', '96'])
                              ->count();

            // Hitung Rombel (Gunakan UUID)
            $totalRombel = Rombel::where('sekolah_id', $uuidSekolah)->count();

        } catch (\Exception $e) {
            // Fallback jika tabel belum ada atau error query
            $totalSiswa = 0; $totalGuru = 0; $totalTendik = 0; $totalRombel = 0;
        }

        return view('admin.sekolah.show', compact(
            'sekolah', 'totalSiswa', 'totalGuru', 'totalTendik', 'totalRombel'
        ));
    }
}