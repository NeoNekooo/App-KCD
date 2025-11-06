<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Models\Sekolah;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SekolahController extends Controller
{
    /**
     * Menampilkan data detail sekolah.
     */
    public function index()
    {
        // Mengambil data sekolah pertama, atau membuat data kosong jika belum ada.
        $sekolah = Sekolah::firstOrCreate(['id' => 1]);
        return view('admin.pengaturan.sekolah.index', compact('sekolah'));
    }

    /**
     * Update data sekolah (hanya logo dan peta).
     */
    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi file logo
            'peta' => 'nullable|string', // Validasi untuk iframe peta
        ]);

        // Cari sekolah dengan ID 1. Diasumsikan data sudah ada dari method index.
        $sekolah = Sekolah::firstOrCreate(['id' => 1]);
        
        // Update Peta. 'peta' bisa jadi null jika user menghapus isinya.
        $sekolah->peta = $request->peta;

        // Handle upload logo
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($sekolah->logo && Storage::disk('public')->exists($sekolah->logo)) {
                Storage::disk('public')->delete($sekolah->logo);
            }
            // Simpan logo baru
            $path = $request->file('logo')->store('logos', 'public');
            $sekolah->logo = $path;
        }

        $sekolah->save();

        return redirect()->back()->with('success', 'Logo & Peta sekolah berhasil diperbarui.');
    }
}

