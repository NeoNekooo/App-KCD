<?php

namespace App\Http\Controllers\Admin\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel; // Import Excel
use App\Exports\SiswaExport; // Import Export Class

class SiswaController extends Controller
{
    /**
     * Menampilkan Daftar Siswa (Index & Filter)
     */
    public function index(Request $request)
    {
        $query = Siswa::with('rombel')->where('status', 'Aktif');
        $user = Auth::user();

        $listKabupaten = [];
        $listKecamatan = [];
        $listSekolah   = [];

        // --- FILTER WILAYAH ---
        // Jika user admin sekolah, kunci ke sekolahnya
        if ($user && !empty($user->sekolah_id)) {
            $query->whereHas('pengguna', function($q) use ($user) {
                $q->where('sekolah_id', $user->sekolah_id);
            });
        } else {
            // Jika admin dinas/super, load opsi filter
            $listKabupaten = Sekolah::select('kabupaten_kota')->whereNotNull('kabupaten_kota')->distinct()->orderBy('kabupaten_kota')->pluck('kabupaten_kota');

            if ($request->filled('kabupaten_kota')) {
                $listKecamatan = Sekolah::where('kabupaten_kota', $request->kabupaten_kota)->select('kecamatan')->distinct()->whereNotNull('kecamatan')->orderBy('kecamatan')->pluck('kecamatan');
            }
            if ($request->filled('kabupaten_kota') && $request->filled('kecamatan')) {
                $listSekolah = Sekolah::where('kabupaten_kota', $request->kabupaten_kota)->where('kecamatan', $request->kecamatan)->orderBy('nama')->pluck('nama', 'sekolah_id');
            }

            // Terapkan filter query
            if ($request->filled('sekolah_id')) {
                $query->whereHas('pengguna', function($q) use ($request) {
                    $q->where('sekolah_id', $request->sekolah_id);
                });
            } elseif ($request->filled('kecamatan')) {
                $query->whereHas('pengguna.sekolah', function($q) use ($request) {
                    $q->where('kecamatan', $request->kecamatan)->where('kabupaten_kota', $request->kabupaten_kota);
                });
            } elseif ($request->filled('kabupaten_kota')) {
                $query->whereHas('pengguna.sekolah', function($q) use ($request) {
                    $q->where('kabupaten_kota', $request->kabupaten_kota);
                });
            }
        }

        // --- PENCARIAN (UPDATED) ---
        // Sekarang bisa cari Nama, NISN, NIK, DAN Nama Sekolah
        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    // Tambahan: Cari berdasarkan Nama Sekolah (relasi via pengguna -> sekolah)
                    ->orWhereHas('pengguna.sekolah', function($qSekolah) use ($search) {
                        $qSekolah->where('nama', 'like', "%{$search}%");
                    });
            });
        });

        // --- PAGINATION ---
        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') $perPage = $query->count() > 0 ? $query->count() : 15;
        
        $siswas = $query->orderBy('nama', 'asc')->paginate($perPage)->appends($request->all());

        return view('admin.kesiswaan.siswa.index', compact('siswas', 'listKabupaten', 'listKecamatan', 'listSekolah'));
    }

    /**
     * Menampilkan Detail Profil Siswa (Single)
     */
    public function show($id)
    {
        // Ambil data siswa dengan relasi rombel dan sekolah (via pengguna)
        $siswa = Siswa::with(['rombel', 'pengguna.sekolah'])->findOrFail($id);
        return view('admin.kesiswaan.siswa.show', compact('siswa'));
    }

    /**
     * Menampilkan Detail Banyak Siswa (Show Multiple)
     * Diakses dari Checkbox di Index
     */
    public function showMultiple(Request $request)
    {
        $idsStr = $request->query('ids', '');
        
        // Jika tidak ada ID, kembalikan ke index
        if (empty($idsStr)) {
            return redirect()->route('admin.kesiswaan.siswa.index')->with('error', 'Tidak ada siswa yang dipilih.');
        }
        
        $idsArray = explode(',', $idsStr);
        // Load data siswa berdasarkan ID yang dipilih
        $siswas = Siswa::with(['rombel', 'pengguna.sekolah'])->whereIn('id', $idsArray)->orderBy('nama', 'asc')->get();
        
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
     * Bisa export semua atau yang dipilih saja
     */
    public function exportExcel(Request $request)
    {
        // Ambil ID dari parameter ?ids=... (jika ada)
        $ids = $request->query('ids') ? explode(',', $request->query('ids')) : null;
        
        return Excel::download(new SiswaExport($ids), 'Data_Siswa.xlsx');
    }
}