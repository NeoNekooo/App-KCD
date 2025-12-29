<?php

namespace App\Http\Controllers\Admin\Pengaturan;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use App\Models\Rombel; // <--- Pastikan Model Rombel di-import
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
    public function index()
    {
        // 1. Ambil Data Hari Libur
        $hariLibur = HariLibur::with('rombels')
                        ->orderBy('tanggal_mulai', 'desc')
                        ->get();

        // 2. Ambil Data Rombel & KELOMPOKKAN (PENTING!)
        // Kita urutkan dulu berdasarkan tingkat, lalu nama
        $rombels = Rombel::orderBy('tingkat_pendidikan_id', 'asc')
                        ->orderBy('nama', 'asc')
                        ->get()
                        // FUNGSI INI YANG MEMBUAT LIST MUNCUL BERTINGKAT
                        ->groupBy(function($item) {
                            // Mengelompokkan berdasarkan kolom 'tingkat_pendidikan_id_str' (Misal: 'Kelas 10')
                            return $item->tingkat_pendidikan_id_str ?? 'Lainnya';
                        });

        // Kirim ke view
        return view('admin.pengaturan.hari-libur.index', compact('hariLibur', 'rombels'));
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'keterangan'      => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tipe'            => 'required|in:global,khusus',
            'rombels'         => 'required_if:tipe,khusus|array',
        ]);

        // Simpan Hari Libur
        $libur = HariLibur::create([
            'keterangan'      => $request->keterangan,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'tipe'            => $request->tipe,
        ]);

        // Simpan Relasi Kelas (Jika tipe khusus)
        if ($request->tipe == 'khusus' && $request->has('rombels')) {
            $libur->rombels()->sync($request->rombels);
        }

        return back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $hariLibur = HariLibur::findOrFail($id);
        $hariLibur->delete();
        return back()->with('success', 'Hari libur berhasil dihapus.');
    }
}