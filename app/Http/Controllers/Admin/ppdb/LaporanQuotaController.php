<?php

namespace App\Http\Controllers\Admin\Ppdb;

use App\Http\Controllers\Controller;
use App\Models\TahunPelajaran;
use App\Models\KompetensiPendaftaran;
use App\Models\QuotaPendaftaran;
use App\Models\CalonSiswa;
use App\Models\TingkatPendaftaran;

class LaporanQuotaController extends Controller
{
    public function index()
    {
        // Ambil tahun aktif dan tingkat aktif
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tingkatAktif = TingkatPendaftaran::where('is_active', true)->first();

        if (!$tahunAktif || !$tingkatAktif) {
            return view('admin.ppdb.laporan_quota', [
                'laporan' => collect(),
                'tahunAktif' => $tahunAktif,
                'tingkatAktif' => $tingkatAktif,
            ]);
        }

        // Kalau tingkat 10 → tampil per keahlian
        if ($tingkatAktif->tingkat == 10) {
            $kompetensis = KompetensiPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)->get();

            $laporan = $kompetensis->map(function ($kompetensi) use ($tahunAktif) {
                $quotaData = QuotaPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)
                    ->where('keahlian', $kompetensi->kode)
                    ->first();

                $jumlahKelas = $quotaData->jumlah_kelas ?? 0;
                $quota = $quotaData->quota ?? 0;

                $jumlahPendaftar = CalonSiswa::where('tahun_id', $tahunAktif->id)
                    ->where('jurusan', $kompetensi->kompetensi)
                    ->count();

                $jumlahRegistrasi = CalonSiswa::where('tahun_id', $tahunAktif->id)
                    ->where('jurusan', $kompetensi->kompetensi)
                    ->where('status', 3)
                    ->count();

                $sisaQuota = max($quota - $jumlahRegistrasi, 0);

                return (object) [
                    'paket_keahlian' => $kompetensi->kompetensi,
                    'kode' => $kompetensi->kode,
                    'jumlah_kelas' => $jumlahKelas,
                    'quota' => $quota,
                    'jumlah_pendaftar' => $jumlahPendaftar,
                    'jumlah_registrasi' => $jumlahRegistrasi,
                    'sisa_quota' => $sisaQuota,
                ];
            });
        } else {
            // Kalau tingkat 1 atau 7 → tanpa keahlian, cuma total umum tapi berdasarkan tingkat
            $jumlahPendaftar = CalonSiswa::where('tahun_id', $tahunAktif->id)
                ->where('tingkat', $tingkatAktif->tingkat)  // <-- filter tingkat aktif
                ->count();

            $jumlahRegistrasi = CalonSiswa::where('tahun_id', $tahunAktif->id)
                ->where('status', 3)
                ->where('tingkat', $tingkatAktif->tingkat)  // <-- filter tingkat aktif
                ->count();

            // Ambil total quota dari tabel quota_pendaftarans (semua keahlian dijumlahkan)
            $quotaData = QuotaPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)->get();

            $jumlahKelas = $quotaData->sum('jumlah_kelas');
            $quota = $quotaData->sum('quota');
            $sisaQuota = max($quota - $jumlahRegistrasi, 0);

            $laporan = collect([
                (object)[
                    'paket_keahlian' => 'Umum',
                    'jumlah_kelas' => $jumlahKelas,
                    'quota' => $quota,
                    'jumlah_pendaftar' => $jumlahPendaftar,
                    'jumlah_registrasi' => $jumlahRegistrasi,
                    'sisa_quota' => $sisaQuota,
                ]
            ]);
        }

        return view('admin.ppdb.laporan_quota', compact('laporan', 'tahunAktif', 'tingkatAktif'));
    }
}
