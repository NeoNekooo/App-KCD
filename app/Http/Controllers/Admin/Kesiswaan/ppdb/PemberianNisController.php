<?php

namespace App\Http\Controllers\Admin\Kesiswaan\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\TahunPelajaran;

class PemberianNisController extends Controller
{
    public function index()
    {
        // Tampilkan hanya calon dengan status = 2 (Registered)
        $calons = CalonSiswa::where('status', 2)->get();

        return view('admin.kesiswaan.ppdb.pemberian_nis', compact('calons'));
    }

    public function generate()
    {
        // Ambil semua calon siswa yang siap (status = 2)
        $calons = CalonSiswa::where('status', 2)->get();

        if ($calons->isEmpty()) {
            return redirect()->back()->with('warning', 'Tidak ada calon dengan status Registered.');
        }

        foreach ($calons as $calon) {
            if (!$calon->nis) {
                $nisBaru = $this->generateNis($calon->tahun_id);
            
                if (!$nisBaru) {
                    return redirect()->back()->with('danger', 'Tidak ada tingkat pendaftaran yang aktif!');
                }
            
                $calon->update([
                    'nis' => $nisBaru,
                    'status' => 3, // Registered with NIS
                ]);
            }
        }


        return redirect()->back()->with('success', 'NIS berhasil digenerate dan status diubah menjadi 3.');
    }

    // ===== Helper Generate NIS =====
    private function generateNis($tahunPelajaranId)
    {
        $tahun = TahunPelajaran::findOrFail($tahunPelajaranId);
        $tp = $tahun->tahun_pelajaran;

        // Ambil tingkat aktif dari tabel tingkat_pendaftarans
        $tingkatAktif = \App\Models\TingkatPendaftaran::where('is_active', true)->first();

        if (!$tingkatAktif) {
            // throw new \Exception("Tidak ada tingkat pendaftaran yang aktif!");

            
        return null;
        }

        $tingkat = $tingkatAktif->tingkat;

        // Pisahkan tahun ajaran (misal: 2025-2026)
        if (strpos($tp, '-') !== false) {
            [$awal, $akhir] = array_map('trim', explode('-', $tp));
            $awal  = (int) $awal;
            $akhir = (int) $akhir;
        } else {
            $awal  = (int) trim($tp);
            $akhir = $awal + 1;
        }

        // Format ke 4 digit
        $awal  = str_pad($awal, 4, '0', STR_PAD_LEFT);
        $akhir = str_pad($akhir, 4, '0', STR_PAD_LEFT);

        $awal2  = substr($awal, -2);   // contoh: 25
        $akhir2 = substr($akhir, -2); // contoh: 26

        // Gunakan tingkat aktif (misalnya 07, 08, 09, dst)
        $base = $awal2 . $akhir2 . str_pad($tingkat, 2, '0', STR_PAD_LEFT);
        // contoh: 252607 (tingkat 07), 252608 (tingkat 08)

        // Cari NIS terakhir berdasarkan tahun saja (tanpa tingkat)
        $last = \App\Models\CalonSiswa::whereNotNull('nis')
            ->where('tahun_id', $tahunPelajaranId)
            ->orderByDesc('nis')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->nis, -3);
            $urutan = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $urutan = "001";
        }

        return $base . $urutan;
    }

}
