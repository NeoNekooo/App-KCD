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
            // Terima nama ekskul sebagai teks. Kami akan mencari atau membuat record master jika belum ada.
            'nama'       => 'required|string|max:255',
            'pembina_id'  => 'nullable|exists:gtks,id',
            'prasarana'   => 'nullable|string|max:255',
        ]);

        // Cari atau buat master daftar ekstrakurikuler berdasarkan nama (hindari duplikat)
        $daftar = DaftarEkstrakurikuler::firstOrCreate(
            ['nama' => trim($validated['nama'])]
        );

        Ekstrakurikuler::create([
            'daftar_ekstrakurikuler_id' => $daftar->id,
            'pembina_id'                => $validated['pembina_id'] ?? null,
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
            'nama'        => 'required|string|max:255',
            'pembina_id'  => 'nullable|exists:gtks,id',
            'prasarana'   => 'nullable|string|max:255',
        ]);

        $daftar = DaftarEkstrakurikuler::firstOrCreate(
            ['nama' => trim($validated['nama'])]
        );

        $ekstrakurikuler->update([
            'daftar_ekstrakurikuler_id' => $daftar->id,
            'pembina_id'                => $validated['pembina_id'] ?? null,
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

    /**
     * Menyimpan data ke database.
     */

}
