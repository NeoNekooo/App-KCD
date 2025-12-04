<?php

namespace App\Http\Controllers\Admin\Rombel;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use App\Models\DaftarEkstrakurikuler;
use App\Models\Gtk; 
use Illuminate\Http\Request;

class RombelEkstrakurikulerController extends Controller
{
    /**
     * Menampilkan halaman utama dengan daftar ekskul.
     */
    public function index()
    {
        // 1. Ambil data ekskul yang sudah ada (Load relasi 'daftar' dan 'pembina')
        $ekskul = Ekstrakurikuler::with(['daftar', 'pembina'])->latest()->paginate(10);

        // 2. AMBIL DATA MASTER UNTUK DROPDOWN (Ini yang sebelumnya kurang)
        // Data ini wajib ada agar dropdown di Modal Create/Edit bisa muncul
        $daftarEkskul = DaftarEkstrakurikuler::orderBy('nama')->get();
        $pembinas = Gtk::orderBy('nama')->get();
        
        // 3. Kirim semua variabel ke view
        return view('admin.rombel.ekstrakurikuler.index', compact('ekskul', 'daftarEkskul', 'pembinas'));
    }

    /**
     * Menyimpan data ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Validasi input ID, pastikan tabel daftar_ekstrakurikuler ada isinya
            'daftar_ekstrakurikuler_id' => 'required|exists:daftar_ekstrakurikuler,id',
            'pembina_id'                => 'nullable|exists:gtks,id',
            'prasarana'                 => 'nullable|string|max:255',
        ]);

        Ekstrakurikuler::create([
            'daftar_ekstrakurikuler_id' => $validated['daftar_ekstrakurikuler_id'],
            'pembina_id'                => $validated['pembina_id'],
            'prasarana'                 => $validated['prasarana'] ?? null,
        ]);

        return redirect()->route('admin.rombel.ekstrakurikuler.index')
                         ->with('success', 'Ekstrakurikuler berhasil ditambahkan.');
    }

    /**
     * Update data di database.
     */
    public function update(Request $request, Ekstrakurikuler $ekstrakurikuler)
    {
        $validated = $request->validate([
            'daftar_ekstrakurikuler_id' => 'required|exists:daftar_ekstrakurikuler,id',
            'pembina_id'                => 'nullable|exists:gtks,id',
            'prasarana'                 => 'nullable|string|max:255',
        ]);

        $ekstrakurikuler->update([
            'daftar_ekstrakurikuler_id' => $validated['daftar_ekstrakurikuler_id'],
            'pembina_id'                => $validated['pembina_id'],
            'prasarana'                 => $validated['prasarana'] ?? null,
        ]);

        return redirect()->route('admin.rombel.ekstrakurikuler.index')
                         ->with('success', 'Ekstrakurikuler berhasil diperbarui.');
    }

    /**
     * Hapus data.
     */
    public function destroy(Ekstrakurikuler $ekstrakurikuler)
    {
        $ekstrakurikuler->delete();

        return redirect()->route('admin.rombel.ekstrakurikuler.index')
                         ->with('success', 'Ekstrakurikuler berhasil dihapus.');
    }
}