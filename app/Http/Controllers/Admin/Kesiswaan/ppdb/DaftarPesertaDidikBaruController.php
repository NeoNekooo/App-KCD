<?php

namespace App\Http\Controllers\Admin\Kesiswaan\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\TahunPelajaran;
use App\Models\TingkatPendaftaran;

class DaftarPesertaDidikBaruController extends Controller
{
    public function index()
    {
        // Ambil tahun pelajaran aktif
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        // Ambil tingkat aktif
        $tingkatAktif = TingkatPendaftaran::where('is_active', true)->first();

        $pesertaDidik = collect();

        if ($tahunAktif && $tingkatAktif) {
            // Filter berdasarkan tahun aktif + tingkat aktif + sudah punya NIS + belum punya kelas
            $pesertaDidik = CalonSiswa::with(['jalurPendaftaran'])
                ->where('tahun_id', $tahunAktif->id)
                ->where('tingkat', $tingkatAktif->tingkat) // pastikan kolom tingkat_daftar ada di tabel calon_siswas
                ->whereNotNull('nis')  // sudah punya NIS
                ->whereNull('kelas_tujuan') // belum punya kelas
                ->get();
        }

        return view('admin.kesiswaan.ppdb.daftar_peserta_didik_baru', compact('pesertaDidik', 'tahunAktif', 'tingkatAktif'));
    }
}
