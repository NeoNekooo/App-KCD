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

        // ===============================
        // DETEKSI ROLE LOGIN
        // ===============================
        $isSiswa = session()->has('peserta_didik_id');
        $isGtk = session()->has('ptk_id');
        // Anggap admin = akses penuh (bisa refine kalau ada role table)
        $isAdmin = auth()->check() && !$isSiswa && !$isGtk;

        return view('admin.pengaturan.sekolah.index', compact('sekolah','isAdmin'));
    }

    /**
     * Update data sekolah (Logo, Peta, Media Sosial Dynamic, dan Background Kartu).
     */
    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'background_kartu' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kode_sekolah' => 'nullable|string|max:50',
            'peta' => 'nullable|string',

            // --- VALIDASI BARU UNTUK DYNAMIC SOCIAL MEDIA ---
            // Kita menerima input array 'social_media'
            'social_media' => 'nullable|array',
            'social_media.*.platform' => 'nullable|string', // Validasi tiap item di dalam array
            'social_media.*.url' => 'nullable|url',
            'social_media.*.username' => 'nullable|string',
            // ------------------------------------------------
        ]);

        // Cari sekolah dengan ID 1.
        $sekolah = Sekolah::firstOrCreate(['id' => 1]);

        // Update data text sederhana
        $sekolah->peta = $request->peta;
        $sekolah->kode_sekolah = $request->kode_sekolah;

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

        // --- HANDLE SIMPAN MEDIA SOSIAL (JSON) ---
        // Karena di Model sudah di-cast 'array', kita bisa langsung assign array dari request.
        // Jika null (kosong), kita simpan array kosong [].
        $sekolah->social_media = $request->social_media ?? [];

        // Hapus properti lama (legacy) agar tidak error jika tidak sengaja terkirim
        // (Facebook_url dkk sudah tidak ada di DB)
        // -----------------------------------------

        $sekolah->save();

        return redirect()->back()->with('success', 'Profil sekolah berhasil diperbarui.');
    }
}
