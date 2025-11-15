<?php

namespace App\Http\Controllers\Admin\Kesiswaan\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\TahunPelajaran;
use App\Models\TingkatPendaftaran;

class PemberianNisController extends Controller
{
    public function index()
    {
        // Ambil tingkat aktif
        $tingkatAktif = TingkatPendaftaran::where('is_active', true)->first();

        // Tampilkan hanya calon dengan status = 2 dan sesuai tingkat aktif
        $calons = collect();
        if ($tingkatAktif) {
            $calons = CalonSiswa::where('status', 2)
                ->where('tingkat', $tingkatAktif->tingkat)
                ->get();
        }

        return view('admin.kesiswaan.ppdb.pemberian_nis', compact('calons', 'tingkatAktif'));
    }

    public function generate()
    {
        $tingkatAktif = TingkatPendaftaran::where('is_active', true)->first();

        if (!$tingkatAktif) {
            return redirect()->back()->with('danger', 'Tidak ada tingkat pendaftaran yang aktif!');
        }

        // Ambil semua calon siswa dari tingkat aktif yang siap (status = 2)
        $calons = CalonSiswa::where('status', 2)
            ->where('tingkat', $tingkatAktif->tingkat)
            ->get();

        if ($calons->isEmpty()) {
            return redirect()->back()->with('warning', 'Tidak ada calon dengan status Registered untuk tingkat aktif.');
        }

        $count = 0; // hitung NIS yang berhasil digenerate  

        foreach ($calons as $calon) {
            if (!$calon->nis) {
                $nisBaru = $this->generateNis($calon->tahun_id, $tingkatAktif->tingkat);
                $calon->update([
                    'nis' => $nisBaru,
                    'status' => 3, // Registered with NIS
                ]);
                $count++;
            }
        }

        return redirect()->back()->with('success', "Berhasil meng-generate {$count} NIS untuk tingkat {$tingkatAktif->tingkat}.");
    }

    /**
     * Helper Generate NIS
     */
    private function generateNis($tahunPelajaranId, $tingkat)
    {
        $tahun = TahunPelajaran::findOrFail($tahunPelajaranId);
        $tp = $tahun->tahun_pelajaran;

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

        // Gunakan tingkat aktif (misalnya 07, 10, dll)
        $base = $awal2 . $akhir2 . str_pad($tingkat, 2, '0', STR_PAD_LEFT);
        // contoh: 252607 (tingkat 7), 252610 (tingkat 10)

        // Cari NIS terakhir berdasarkan tahun & tingkat
        $last = CalonSiswa::whereNotNull('nis')
            ->where('tahun_id', $tahunPelajaranId)
            ->where('tingkat', $tingkat)
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
