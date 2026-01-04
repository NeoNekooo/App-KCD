<?php

namespace App\Http\Controllers\Admin\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SiswaExport;
use Barryvdh\DomPDF\Facade\Pdf;

class SiswaController extends Controller
{
    /**
     * Menampilkan Daftar Siswa
     */
    public function index(Request $request)
    {
        $query = Siswa::with('rombel')->where('status', 'Aktif');
        $user = Auth::user();

        $listKabupaten = [];
        $listKecamatan = [];
        $listSekolah   = [];

        // --- FILTER WILAYAH ---
        if ($user && !empty($user->sekolah_id)) {
            $query->whereHas('pengguna', function($q) use ($user) {
                $q->where('sekolah_id', $user->sekolah_id);
            });
        } else {
            $listKabupaten = Sekolah::select('kabupaten_kota')->whereNotNull('kabupaten_kota')->distinct()->orderBy('kabupaten_kota')->pluck('kabupaten_kota');

            if ($request->filled('kabupaten_kota')) {
                $listKecamatan = Sekolah::where('kabupaten_kota', $request->kabupaten_kota)->select('kecamatan')->distinct()->whereNotNull('kecamatan')->orderBy('kecamatan')->pluck('kecamatan');
            }
            if ($request->filled('kabupaten_kota') && $request->filled('kecamatan')) {
                $listSekolah = Sekolah::where('kabupaten_kota', $request->kabupaten_kota)->where('kecamatan', $request->kecamatan)->orderBy('nama')->pluck('nama', 'sekolah_id');
            }

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

        // --- PENCARIAN ---
        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        });

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
     * Menampilkan Detail Banyak Siswa (List/Table View)
     */
    public function showMultiple(Request $request)
    {
        $idsStr = $request->query('ids', '');
        if (empty($idsStr)) return redirect()->route('admin.kesiswaan.siswa.index');
        
        $idsArray = explode(',', $idsStr);
        $siswas = Siswa::with('rombel')->whereIn('id', $idsArray)->orderBy('nama', 'asc')->get();
        
        // Anda bisa membuat view khusus 'show_multiple' jika ingin tampilan tabel perbandingan
        // Untuk sementara kita pakai view show yang sama atau view khusus
        return view('admin.kesiswaan.siswa.show_multiple', compact('siswas'));
    }

    public function exportExcel(Request $request)
    {
        $ids = $request->query('ids') ? explode(',', $request->query('ids')) : null;
        return Excel::download(new SiswaExport($ids), 'Data_Siswa.xlsx');
    }

    public function cetakPdf($id)
    {
        $siswa = Siswa::with(['rombel', 'mutasiKeluar'])->findOrFail($id);
        $sekolah = $siswa->pengguna && $siswa->pengguna->sekolah ? $siswa->pengguna->sekolah : null;
        $siswas = collect([$siswa]); // Bungkus jadi collection biar kompatibel sama view pdf lama
        $pdf = Pdf::loadView('admin.kesiswaan.siswa.pdf_biodata', compact('siswas', 'sekolah'));
        return $pdf->stream('Biodata_'.$siswa->nama.'.pdf');
    }

    public function update(Request $request, $id) {
        $siswa = Siswa::findOrFail($id);
        $siswa->update($request->except(['_token', '_method', 'foto']));
        return back()->with('success', 'Data berhasil diperbarui');
    }
}