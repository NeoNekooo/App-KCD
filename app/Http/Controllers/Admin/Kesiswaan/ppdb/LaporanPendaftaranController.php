<?php

namespace App\Http\Controllers\Admin\Kesiswaan\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\TahunPelajaran;
use App\Models\JalurPendaftaran;
use App\Models\Rombel;

class LaporanPendaftaranController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $tp = $p = $l = 0;
        $laporanJalur = collect();
        $laporanJurusan = collect();

        if ($tahunAktif) {
            // === Total umum ===
            $tp = CalonSiswa::where('tahun_id', $tahunAktif->id)->count();
            $p  = CalonSiswa::where('tahun_id', $tahunAktif->id)->where('jenis_kelamin', 'P')->count();
            $l  = CalonSiswa::where('tahun_id', $tahunAktif->id)->where('jenis_kelamin', 'L')->count();

            // === Laporan per jalur ===
            $jalurAktif = JalurPendaftaran::where('is_active', true)->get();
            $laporanJalur = $jalurAktif->map(function ($jalur) use ($tahunAktif) {
                $jumlah = CalonSiswa::where('tahun_id', $tahunAktif->id)
                    ->where('jalur_id', $jalur->id)
                    ->count();
                return [
                    'nama' => $jalur->jalur ?? 'Tanpa Nama',
                    'jumlah' => $jumlah,
                ];
            });

            // === Laporan per jurusan (berdasarkan rombel) ===
            $jurusans = Rombel::select('jurusan_id_str')
                ->distinct()
                ->orderBy('jurusan_id_str')
                ->pluck('jurusan_id_str');

            $laporanJurusan = $jurusans->map(function ($jurusan) use ($tahunAktif) {
                $jumlah = CalonSiswa::where('tahun_id', $tahunAktif->id)
                    ->where('jurusan', $jurusan)
                    ->count();
                return [
                    'nama' => $jurusan,
                    'jumlah' => $jumlah,
                ];
            });

            // === Laporan calon siswa registrasi (status = 3) berdasarkan jurusan ===
            $laporanJurusanRegistrasi = $jurusans->map(function ($jurusan) use ($tahunAktif) {
                $jumlah = \App\Models\CalonSiswa::where('tahun_id', $tahunAktif->id)
                    ->where('jurusan', $jurusan)
                    ->where('status', 3)
                    ->count();
                return [
                    'nama' => $jurusan,
                    'jumlah' => $jumlah,
                ];
            });

        }

        return view('admin.kesiswaan.ppdb.laporan_pendaftaran', compact(
            'tp', 'p', 'l', 'laporanJalur', 'laporanJurusan', 'laporanJurusanRegistrasi', 'tahunAktif'
        ));
    }
}
