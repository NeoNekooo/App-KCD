<?php

namespace App\Http\Controllers\Admin\Kesiswaan\Ppdb;

use App\Http\Controllers\Controller;
use App\Models\TahunPelajaran;
use App\Models\Rombel;
use App\Models\CalonSiswa;

class LaporanQuotaController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $rombels = Rombel::select('jurusan_id_str')
            ->distinct()
            ->orderBy('jurusan_id_str')
            ->get();

        $laporan = $rombels->map(function ($rombel) use ($tahunAktif) {
            $paket = $rombel->jurusan_id_str;
            $jumlahKelas = Rombel::where('jurusan_id_str', $paket)->count();
            $quota = $jumlahKelas * 48; // contoh quota per kelas 48

            $jumlahPendaftar = CalonSiswa::where('tahun_id', $tahunAktif->id)
                ->where('jurusan', $paket)
                ->count();

            $jumlahRegistrasi = CalonSiswa::where('tahun_id', $tahunAktif->id)
                ->where('jurusan', $paket)
                ->where('status', 3)
                ->count();

            return (object)[
                'paket_keahlian' => $paket,
                'jumlah_kelas' => $jumlahKelas,
                'quota' => $quota,
                'jumlah_pendaftar' => $jumlahPendaftar,
                'jumlah_registrasi' => $jumlahRegistrasi,
            ];
        });

        return view('admin.kesiswaan.ppdb.laporan_quota', compact('laporan', 'tahunAktif'));
    }
}
