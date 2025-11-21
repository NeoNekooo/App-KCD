<?php

namespace App\Http\Controllers\Admin\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\TahunPelajaran;
use App\Models\ProfilSekolah;
use App\Models\TingkatPendaftaran;

class DaftarCalonPesertaDidikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil tahun pelajaran & tingkat yang aktif
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tingkatAktif = TingkatPendaftaran::where('is_active', true)->first();

        // Jika ada tahun aktif, ambil calon siswa sesuai tahun & tingkat aktif
        $formulirs = collect(); // default kosong
        if ($tahunAktif && $tingkatAktif) {
            $formulirs = CalonSiswa::with(['jalurPendaftaran', 'syarat'])
                ->where('tahun_id', $tahunAktif->id)
                ->where('tingkat', $tingkatAktif->tingkat)
                ->get();
        }

        return view('admin.ppdb.daftar_calon_peserta_didik', compact('formulirs', 'tahunAktif', 'tingkatAktif'));
    }

    /**
     * Tampilkan resi calon siswa
     */
    public function resi($id)
    {
        // Ambil data calon siswa + relasi
        $calon = CalonSiswa::with(['jalurPendaftaran', 'syarat'])
            ->findOrFail($id);

        $profilSekolah = ProfilSekolah::first(); // ambil data profil

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
