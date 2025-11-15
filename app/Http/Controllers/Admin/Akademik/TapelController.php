<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TapelController extends Controller
{
    /**
     * Tampilkan daftar tahun pelajaran
     */
    public function index()
    {
        $tapel = Tapel::orderByDesc('kode_tapel')->get();

        return view('admin.akademik.tapel.index', compact('tapel'));
    }

    /**
     * Sinkronkan tahun pelajaran dengan semester_id terbaru dari tabel rombels
     */
    public function sinkron()
    {
        $latest = DB::table('rombels')
            ->select('semester_id')
            ->whereNotNull('semester_id')
            ->distinct()
            ->orderByDesc('semester_id')
            ->first();

        if (!$latest) {
            return back()->with('warning', 'Tidak ada data semester_id di tabel rombels ❌');
        }

        $kode = $latest->semester_id;
        $tahun = substr($kode, 0, 4);
        $semester = substr($kode, -1) == '1' ? 'Ganjil' : 'Genap';
        $tahunAjaran = $tahun . '/' . ($tahun + 1);

        // Nonaktifkan semua tapel dulu
        Tapel::query()->update(['is_active' => false]);

        $existing = Tapel::where('kode_tapel', $kode)->first();

        if ($existing) {
            $existing->update(['is_active' => true]);
        } else {
            Tapel::create([
                'kode_tapel' => $kode,
                'tahun_ajaran' => $tahunAjaran,
                'semester' => $semester,
                'is_active' => true,
            ]);
        }

        return back()->with('success', 'Tapel berhasil disinkron dan dijadikan aktif 🟢');
    }

    /**
     * Jadikan salah satu tapel sebagai aktif
     */
    public function setAktif($id)
    {
        Tapel::query()->update(['is_active' => false]);
        Tapel::where('id', $id)->update(['is_active' => true]);

        return back()->with('success', 'Tapel berhasil dijadikan aktif 🟢');
    }
}
