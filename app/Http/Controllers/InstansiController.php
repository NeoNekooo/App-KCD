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

        // 1. Validasi Input
        $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'nama_brand'    => 'nullable|string|max:255',
            'peta'          => 'nullable|string',
            'social_media'  => 'nullable|array', // Validasi array
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Ambil data input KECUALI logo & social_media (kita olah manual)
        $data = $request->except(['logo', 'social_media']);

        // 3. FIX: Rapikan Array Social Media biar jadi JSON Array [{}, {}]
        // Kalau gak diginiin, nanti jadinya Object JSON {"1": {}, "2": {}} dan error di JS
        if ($request->has('social_media')) {
            $data['social_media'] = array_values($request->input('social_media'));
        } else {
            $data['social_media'] = []; // Kalau kosong, simpan array kosong
        }

        // 4. Handle Upload Logo
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada & file benar-benar ada
            if ($instansi->logo && Storage::disk('public')->exists($instansi->logo)) {
                Storage::disk('public')->delete($instansi->logo);
            }
            
            // Simpan logo baru
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $path;
        }

        // 5. Simpan Perubahan
        $instansi->update($data);

        return redirect()->back()->with('success', 'Profil Instansi berhasil diperbarui!');
    }
}