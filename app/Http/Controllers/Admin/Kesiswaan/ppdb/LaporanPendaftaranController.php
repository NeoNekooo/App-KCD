<?php

namespace App\Http\Controllers\Admin\Kesiswaan\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\TahunPelajaran;
use App\Models\JalurPendaftaran;
use App\Models\KompetensiPendaftaran;
use App\Models\TingkatPendaftaran;

class LaporanPendaftaranController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tingkatAktif = TingkatPendaftaran::where('is_active', true)->first();

        $tp = $p = $l = 0;
        $laporanJalur = collect();
        $laporanJurusan = collect();
        $laporanJurusanRegistrasi = collect();

        if ($tahunAktif) {
            // === Base query calon siswa berdasarkan tahun & tingkat aktif ===
            $baseQuery = CalonSiswa::where('tahun_id', $tahunAktif->id);

            if ($tingkatAktif) {
                $baseQuery->where('tingkat', $tingkatAktif->tingkat);
            }

            // === Total umum ===
            $tp = (clone $baseQuery)->count();
            $p  = (clone $baseQuery)->where('jenis_kelamin', 'P')->count();
            $l  = (clone $baseQuery)->where('jenis_kelamin', 'L')->count();

            // === Laporan per jalur ===
            $jalurAktif = JalurPendaftaran::where('is_active', true)->get();

            $laporanJalur = $jalurAktif->map(function ($jalur) use ($baseQuery) {
                $jumlah = (clone $baseQuery)
                    ->where('jalur_id', $jalur->id)
                    ->count();
                return [
                    'nama' => $jalur->jalur ?? 'Tanpa Nama',
                    'jumlah' => $jumlah,
                ];
            });

            // === Laporan per jurusan (berdasarkan rombel) ===
            $jurusans = KompetensiPendaftaran::all();

            $laporanJurusan = $jurusans->map(function ($jurusan) use ($baseQuery) {
                $jumlah = (clone $baseQuery)
                    ->where('jurusan', $jurusan->kompetensi)
                    ->count();
                return [
                    'nama' => $jurusan->kompetensi,
                    'jumlah' => $jumlah,
                ];
            });

            // === Laporan calon siswa registrasi (status = 3) berdasarkan jurusan ===
            $laporanJurusanRegistrasi = $jurusans->map(function ($jurusan) use ($baseQuery) {
                $jumlah = (clone $baseQuery)
                    ->where('jurusan', $jurusan->kompetensi)
                    ->where('status', 3)
                    ->count();
                return [
                    'nama' => $jurusan->kompetensi,
                    'jumlah' => $jumlah,
                ];
            });
        }

        return view('admin.kesiswaan.ppdb.laporan_pendaftaran', compact(
            'tp', 'p', 'l',
            'laporanJalur', 'laporanJurusan', 'laporanJurusanRegistrasi',
            'tahunAktif', 'tingkatAktif'
        ));
    }
}
