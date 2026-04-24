<?php

namespace App\Http\Controllers\Admin\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\SiswaExport; 
use App\Exports\RekapitulasiSiswaExport;

class SiswaController extends Controller
{
    /**
     * Menampilkan Daftar Siswa (Index & Filter)
     */
    public function index(Request $request)
    {
        // Langsung load relasi 'sekolah' tanpa lewat pengguna
        $query = Siswa::with(['rombel', 'sekolah'])->where('status', 'Aktif');
        $user = Auth::user();

        // --- LOGIKA FILTER WILAYAH (LANGSUNG KE sekolah_id) ---
        if ($user && !empty($user->sekolah_id)) {
            $query->where('sekolah_id', $user->sekolah_id);
        } else {
            if ($request->filled('sekolah_id')) {
                $query->where('sekolah_id', $request->sekolah_id);
            } 
            elseif ($request->filled('kabupaten_kota') || $request->filled('kecamatan')) {
                $sekolahIds = Sekolah::query();
                
                if ($request->filled('kabupaten_kota')) {
                    $sekolahIds->where('kabupaten_kota', 'LIKE', '%' . $request->kabupaten_kota . '%');
                }
                if ($request->filled('kecamatan')) {
                    $sekolahIds->where('kecamatan', 'LIKE', '%' . $request->kecamatan . '%');
                }

                $query->whereIn('sekolah_id', $sekolahIds->pluck('sekolah_id'));
            }
        }

        // --- PENCARIAN (NAMA, NISN, NIK, NAMA SEKOLAH) ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    // Cari nama sekolah langsung dari relasi sekolah
                    ->orWhereHas('sekolah', function($qSekolah) use ($search) {
                        $qSekolah->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        // --- PAGINATION ---
        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') $perPage = $query->count() > 0 ? $query->count() : 15;
        $siswas = $query->orderBy('nama', 'asc')->paginate($perPage)->withQueryString();

        // 🔥 FORCED DECRYPTION DI LEVEL CONTROLLER 🔥
        $siswas->through(function ($siswa) {
            $cols = \App\Services\EncryptionService::getEncryptedColumns()['siswas'] ?? [];
            foreach ($cols as $col) {
                if (isset($siswa->$col)) {
                    $siswa->$col = \App\Services\EncryptionService::decrypt($siswa->$col);
                }
            }
            return $siswa;
        });

        // --- LOGIKA DROPDOWN BERJENJANG ---
        $listKabupaten = Sekolah::select('kabupaten_kota')->whereNotNull('kabupaten_kota')->distinct()->orderBy('kabupaten_kota')->pluck('kabupaten_kota');
        
        $listKecamatan = [];
        if ($request->filled('kabupaten_kota')) {
            $listKecamatan = Sekolah::where('kabupaten_kota', 'LIKE', '%' . $request->kabupaten_kota . '%')
                ->select('kecamatan')->whereNotNull('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        }

        $listSekolah = [];
        if ($request->filled('kecamatan')) {
            $sekolahQuery = Sekolah::where('kecamatan', 'LIKE', '%' . $request->kecamatan . '%');
            if ($request->filled('kabupaten_kota')) {
                $sekolahQuery->where('kabupaten_kota', 'LIKE', '%' . $request->kabupaten_kota . '%');
            }
            $listSekolah = $sekolahQuery->orderBy('nama')->pluck('nama', 'sekolah_id');
        }

        return view('admin.kesiswaan.siswa.index', compact('siswas', 'listKabupaten', 'listKecamatan', 'listSekolah'));
    }

    /**
     * Menampilkan Daftar Siswa Non-Aktif
     */
    public function indexNonaktif(Request $request)
    {
        $query = Siswa::with(['rombel', 'sekolah'])->where('status', '!=', 'Aktif');
        $user = Auth::user();

        // Filter Wilayah
        if ($user && !empty($user->sekolah_id)) {
            $query->where('sekolah_id', $user->sekolah_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $siswas = $query->orderBy('nama', 'asc')->paginate($perPage)->withQueryString();

        // Decryption
        $siswas->through(function ($siswa) {
            $cols = \App\Services\EncryptionService::getEncryptedColumns()['siswas'] ?? [];
            foreach ($cols as $col) {
                if (isset($siswa->$col)) {
                    $siswa->$col = \App\Services\EncryptionService::decrypt($siswa->$col);
                }
            }
            return $siswa;
        });

        // Dropdown Berjenjang
        $listKabupaten = Sekolah::select('kabupaten_kota')->whereNotNull('kabupaten_kota')->distinct()->orderBy('kabupaten_kota')->pluck('kabupaten_kota');
        
        $listKecamatan = [];
        if ($request->filled('kabupaten_kota')) {
            $listKecamatan = Sekolah::where('kabupaten_kota', 'LIKE', '%' . $request->kabupaten_kota . '%')
                ->select('kecamatan')->whereNotNull('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        }

        $listSekolah = [];
        if ($request->filled('kecamatan')) {
            $sekolahQuery = Sekolah::where('kecamatan', 'LIKE', '%' . $request->kecamatan . '%');
            if ($request->filled('kabupaten_kota')) {
                $sekolahQuery->where('kabupaten_kota', 'LIKE', '%' . $request->kabupaten_kota . '%');
            }
            $listSekolah = $sekolahQuery->orderBy('nama')->pluck('nama', 'sekolah_id');
        }

        return view('admin.kesiswaan.siswa.index_nonaktif', compact('siswas', 'listKabupaten', 'listKecamatan', 'listSekolah'));
    }

    /**
     * Menampilkan Detail Profil Siswa (Single)
     */
    public function show($id)
    {
        $siswa = Siswa::with(['rombel', 'sekolah'])->findOrFail($id);
        return view('admin.kesiswaan.siswa.show', compact('siswa'));
    }

    /**
     * Menampilkan Detail Banyak Siswa (Show Multiple)
     */
    public function showMultiple(Request $request)
    {
        $idsStr = $request->query('ids', '');
        
        if (empty($idsStr)) {
            return redirect()->route('admin.kesiswaan.siswa.index')->with('error', 'Tidak ada siswa yang dipilih.');
        }
        
        $idsArray = explode(',', $idsStr);
        $siswas = Siswa::with(['rombel', 'sekolah'])->whereIn('id', $idsArray)->orderBy('nama', 'asc')->get();
        
        return view('admin.kesiswaan.siswa.show_multiple', compact('siswas'));
    }

    /**
     * Update Data Siswa
     */
    public function update(Request $request, $id) 
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->update($request->except(['_token', '_method', 'foto']));
        return back()->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Export Excel
     */
    public function exportExcel(Request $request)
    {
        $ids = $request->query('ids') ? explode(',', $request->query('ids')) : null;
        return Excel::download(new SiswaExport($ids), 'Data_Siswa.xlsx');
    }

    /**
     * Menampilkan Halaman Rekapitulasi Siswa (Berdasarkan Wilayah dan Jenjang)
     */
    public function rekapitulasi(Request $request)
    {
        $jenjangTerpilih = $request->input('jenjang', '');

        // 1. Query Agregasi: Join 'siswas' dengan 'sekolahs' (Hanya Siswa Aktif)
        // Menggunakan Model Siswa agar FilterRegional Aktif
        $query = Siswa::query()
            ->join('sekolahs', 'siswas.sekolah_id', '=', 'sekolahs.sekolah_id')
            ->where('siswas.status', 'Aktif')
            ->select(
                'sekolahs.kabupaten_kota',
                DB::raw("SUM(CASE WHEN sekolahs.status_sekolah_str LIKE '%Negeri%' THEN 1 ELSE 0 END) as total_negeri"),
                DB::raw("SUM(CASE WHEN sekolahs.status_sekolah_str LIKE '%Swasta%' THEN 1 ELSE 0 END) as total_swasta"),
                DB::raw("COUNT(siswas.id) as total_keseluruhan")
            )
            ->whereNotNull('sekolahs.kabupaten_kota');

        // B. Filter Jenjang
        if (!empty($jenjangTerpilih)) {
            $query->where('sekolahs.bentuk_pendidikan_id_str', $jenjangTerpilih);
        }

        // Ambil Data
        $rekapData = $query->groupBy('sekolahs.kabupaten_kota')
                           ->orderBy('sekolahs.kabupaten_kota', 'asc')
                           ->get();

        // 2. Kalkulasi Grand Total ke Bawah
        $grandTotalNegeri = $rekapData->sum('total_negeri');
        $grandTotalSwasta = $rekapData->sum('total_swasta');
        $grandTotalAkhir = $rekapData->sum('total_keseluruhan');

        // 3. Ambil List Dropdown Filter Jenjang (Dari Tabel Sekolahs)
        $listJenjang = Sekolah::whereNotNull('bentuk_pendidikan_id_str')
                              ->distinct()
                              ->pluck('bentuk_pendidikan_id_str')
                              ->filter(function($value) { return !empty(trim($value)); })
                              ->sort()
                              ->values();

        // 4. Return ke View
        return view('admin.kesiswaan.siswa.rekapitulasi', compact(
            'rekapData', 
            'grandTotalNegeri', 
            'grandTotalSwasta', 
            'grandTotalAkhir',
            'listJenjang',
            'jenjangTerpilih'
        ));
    }

    /**
     * Download Excel untuk Hasil Rekapitulasi Siswa
     */
    public function exportRekapitulasi(Request $request)
    {
        $jenjangTerpilih = $request->input('jenjang', '');
        
        $namaFile = 'Rekapitulasi_Siswa_Aktif';
        if (!empty($jenjangTerpilih)) {
            $namaFile .= '_' . preg_replace('/[^A-Za-z0-9\-]/', '', $jenjangTerpilih);
        }
        $namaFile .= '.xlsx';

        return Excel::download(new RekapitulasiSiswaExport($jenjangTerpilih), $namaFile);
    }
}