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
     * Update data sekolah (Logo, Peta, dan Media Sosial).
     */
    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi file logo
            'peta' => 'nullable|string', // Validasi untuk iframe peta
            // Validasi untuk URL media sosial
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
        ]);

        // Cari sekolah dengan ID 1.
        $sekolah = Sekolah::firstOrCreate(['id' => 1]);
        
        // Update Peta.
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

        // Handle update media sosial
        // Karena Model Sekolah pakai $guarded = [], kita bisa langsung isi
        $sekolah->facebook_url = $request->facebook_url;
        $sekolah->instagram_url = $request->instagram_url;
        $sekolah->youtube_url = $request->youtube_url;
        $sekolah->tiktok_url = $request->tiktok_url;

        $sekolah->save();

        return redirect()->back()->with('success', 'Profil sekolah berhasil diperbarui.');
    }
}