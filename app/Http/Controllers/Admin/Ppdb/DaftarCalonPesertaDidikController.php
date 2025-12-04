<?php

namespace App\Http\Controllers\Admin\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\TahunPelajaran;
use App\Models\Sekolah;
use App\Models\TingkatPendaftaran;

class DaftarCalonPesertaDidikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $tahunAktif = TahunPelajaran::where('is_active', true)->first();
    $tingkatAktif = TingkatPendaftaran::where('is_active', true)->first();

    $perPage = $request->input('per_page', 10); // default 10
    $search = $request->input('search');

    $query = CalonSiswa::with(['jalurPendaftaran', 'syarat'])
        ->when($tahunAktif, fn($q) => $q->where('tahun_id', $tahunAktif->id))
        ->when($tingkatAktif, fn($q) => $q->where('tingkat', $tingkatAktif->tingkat))
        ->when($search, function($q) use ($search) {
            $q->where(function($qq) use ($search){
                $qq->where('nama_lengkap', 'like', "%$search%")
                   ->orWhere('nomor_resi', 'like', "%$search%")
                   ->orWhere('asal_sekolah', 'like', "%$search%");
            });
        });

    $formulirs = $query->paginate($perPage)->appends($request->all());

    return view('admin.ppdb.daftar_calon_peserta_didik', compact(
        'formulirs','tahunAktif','tingkatAktif','search','perPage'
    ));
}


    /**
     * Tampilkan resi calon siswa
     */
    public function resi($id)
    {
        // Ambil data calon siswa + relasi
        $calon = CalonSiswa::with(['jalurPendaftaran', 'syarat'])
            ->findOrFail($id);

        $profilSekolah = Sekolah::first(); // ambil data profil

        // Kirim ke view khusus resi
        return view('admin.ppdb.resi_calon', compact('calon', 'profilSekolah'));
    }

    /**
     * Hapus calon siswa
     */
    public function destroy($id)
    {
        $calon = CalonSiswa::findOrFail($id);
        $calon->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}
