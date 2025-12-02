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
     * Update data sekolah (Logo, Peta, Media Sosial, dan Background Kartu).
     */
    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'background_kartu' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
            // --- VALIDASI BARU ---
            'kode_sekolah' => 'nullable|string|max:50', // Tambahkan ini
            // ---------------------
            'peta' => 'nullable|string',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
        ]);

        // Cari sekolah dengan ID 1.
        $sekolah = Sekolah::firstOrCreate(['id' => 1]);
        
        // Update data text sederhana
        $sekolah->peta = $request->peta;
        $sekolah->kode_sekolah = $request->kode_sekolah; // <--- SIMPAN DATA DISINI

        // 1. Handle upload LOGO
        if ($request->hasFile('logo')) {
            if ($sekolah->logo && Storage::disk('public')->exists($sekolah->logo)) {
                Storage::disk('public')->delete($sekolah->logo);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $sekolah->logo = $path;
        }

        // 2. Handle upload BACKGROUND KARTU
        if ($request->hasFile('background_kartu')) {
            if ($sekolah->background_kartu && Storage::disk('public')->exists($sekolah->background_kartu)) {
                Storage::disk('public')->delete($sekolah->background_kartu);
            }
            $path = $request->file('background_kartu')->store('sekolah_media/background', 'public');
            $sekolah->background_kartu = $path;
        }

        // Handle update media sosial
        $sekolah->facebook_url = $request->facebook_url;
        $sekolah->instagram_url = $request->instagram_url;
        $sekolah->youtube_url = $request->youtube_url;
        $sekolah->tiktok_url = $request->tiktok_url;

        $sekolah->save();

        return redirect()->back()->with('success', 'Profil sekolah berhasil diperbarui.');
    }
}