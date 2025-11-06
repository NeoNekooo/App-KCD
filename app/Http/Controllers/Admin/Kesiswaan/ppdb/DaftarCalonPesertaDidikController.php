<?php

namespace App\Http\Controllers\Admin\Kesiswaan\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\TahunPelajaran;
use App\Models\ProfilSekolah;

class DaftarCalonPesertaDidikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ambil tahun pelajaran aktif
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        // kalau ada tahun aktif, ambil calon siswa sesuai tahun tsb
        $formulirs = [];
        if ($tahunAktif) {
            $formulirs = CalonSiswa::with(['jalurPendaftaran', 'syarat'])
                ->where('tahun_id', $tahunAktif->id)
                ->get();
        }

        return view('admin.kesiswaan.ppdb.daftar_calon_peserta_didik', compact('formulirs', 'tahunAktif'));
    }

    public function resi($id)
    {
        // Ambil data calon siswa + relasi
        $calon = CalonSiswa::with(['jalurPendaftaran', 'syarat'])
            ->findOrFail($id);

        
        $profilSekolah = ProfilSekolah::first(); // ambil data profil

        // Kirim ke view khusus resi
        return view('admin.kesiswaan.ppdb.resi_calon', compact('calon','profilSekolah'));
    }

    public function destroy($id)
{
    $calon = CalonSiswa::findOrFail($id);
    $calon->delete();

    return redirect()->back()->with('success', 'Data berhasil dihapus');
}

}
