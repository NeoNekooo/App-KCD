<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instansi;
use Illuminate\Support\Facades\Storage;

class InstansiController extends Controller
{
    public function index()
    {
        // Ambil data pertama, kalau kosong buat baru (default)
        $instansi = Instansi::first();
        if (!$instansi) {
            $instansi = Instansi::create(['nama_instansi' => 'KCD Wilayah Baru']);
        }

        return view('admin.instansi.index', compact('instansi'));
    }

    public function update(Request $request)
    {
        $instansi = Instansi::first();

        // 1. Validasi Input (Termasuk field baru)
        $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'nama_brand'    => 'nullable|string|max:255', // <-- Baru
            'peta'          => 'nullable|string',         // <-- Baru (Embed HTML)
            'social_media'  => 'nullable|array',          // <-- Baru (Array dari form)
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Ambil semua data input kecuali logo
        // Note: 'social_media' akan otomatis masuk sebagai array.
        // Karena di Model sudah ada protected $casts = ['social_media' => 'array'],
        // Laravel otomatis mengubahnya jadi JSON saat disimpan ke DB.
        $data = $request->except(['logo']);

        // 3. Handle Upload Logo
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada & file benar-benar ada di storage
            if ($instansi->logo && Storage::disk('public')->exists($instansi->logo)) {
                Storage::disk('public')->delete($instansi->logo);
            }
            
            // Simpan logo baru ke folder 'public/logos'
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $path;
        }

        // 4. Simpan Perubahan
        $instansi->update($data);

        return redirect()->back()->with('success', 'Profil Instansi berhasil diperbarui!');
    }
}