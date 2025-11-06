<?php

namespace App\Http\Controllers\Admin\Kesiswaan\ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\TahunPelajaran;

class DaftarPesertaDidikBaruController extends Controller
{
    public function index()
    {
        // Ambil tahun aktif
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $pesertaDidik = collect();

        if ($tahunAktif) {
            $pesertaDidik = CalonSiswa::with(['jalurPendaftaran'])
                ->where('tahun_id', $tahunAktif->id)
                ->whereNotNull('nis')   // ✅ sudah punya NIS
                ->whereNull('kelas_tujuan')    // ✅ belum punya kelas
                ->get();
        }

        return view('admin.kesiswaan.ppdb.daftar_peserta_didik_baru', compact('pesertaDidik', 'tahunAktif'));
    }
}
