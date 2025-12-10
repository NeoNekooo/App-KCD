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
        // Urutkan dari yang terbaru
        $tapel = Tapel::orderByDesc('kode_tapel')->get();

        return view('admin.akademik.tapel.index', compact('tapel'));
    }

    /**
     * Sinkronkan tahun pelajaran dengan semester_id terbaru dari tabel rombels
     * Dan otomatis set AKTIF hanya untuk data terbaru tersebut.
     */
    public function sinkron()
    {
        // 1. Cari semester_id paling besar (terbaru) dari tabel rombels
        $latest = DB::table('rombels')
            ->select('semester_id')
            ->whereNotNull('semester_id')
            ->orderByDesc('semester_id') // Pastikan mengambil yang paling besar angkanya
            ->first();

        if (!$latest) {
            return back()->with('warning', 'Tidak ada data semester_id di tabel rombels ❌');
        }

        $kodeTerbaru = $latest->semester_id;
        
        // 2. Siapkan data untuk Tapel baru/update
        $tahun = substr($kodeTerbaru, 0, 4);
        $semester = substr($kodeTerbaru, -1) == '1' ? 'Ganjil' : 'Genap';
        $tahunAjaran = $tahun . '/' . ($tahun + 1);

        // 3. Reset semua Tapel menjadi TIDAK AKTIF terlebih dahulu
        Tapel::query()->update(['is_active' => false]);

        // 4. Cek apakah Tapel dengan kode tersebut sudah ada
        $existing = Tapel::where('kode_tapel', $kodeTerbaru)->first();

        if ($existing) {
            // Jika ada, cukup update statusnya jadi AKTIF
            $existing->update(['is_active' => true]);
        } else {
            // Jika belum ada, buat baru dan langsung set AKTIF
            Tapel::create([
                'kode_tapel' => $kodeTerbaru,
                'tahun_ajaran' => $tahunAjaran,
                'semester' => $semester,
                'is_active' => true,
            ]);
        }

        return back()->with('success', 'Sinkronisasi berhasil! Tapel terbaru (' . $tahunAjaran . ' - ' . $semester . ') kini AKTIF 🟢');
    }
}