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
            'nama_kepala'   => 'nullable|string|max:255',
            'nip_kepala'    => 'nullable|string|max:50',
            'peta'          => 'nullable|string',
            'social_media'  => 'nullable|array',
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // UPDATE: Sekarang support JPG/JPEG selain PNG
            'tanda_tangan'  => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'lintang'       => 'nullable|numeric',
            'bujur'         => 'nullable|numeric',
        ]);

        // 2. Ambil data input KECUALI logo, ttd, & social_media
        $data = $request->except(['logo', 'tanda_tangan', 'social_media']);

        // 3. Rapikan Array Social Media
        if ($request->has('social_media')) {
            $data['social_media'] = array_values($request->input('social_media'));
        } else {
            $data['social_media'] = []; 
        }

        // 4. Handle Upload Logo
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($instansi->logo && Storage::disk('public')->exists($instansi->logo)) {
                Storage::disk('public')->delete($instansi->logo);
            }
            // Simpan logo baru
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        // 5. Handle Upload Tanda Tangan (TTD)
        if ($request->hasFile('tanda_tangan')) {
            // Hapus TTD lama jika ada
            if ($instansi->tanda_tangan && Storage::disk('public')->exists($instansi->tanda_tangan)) {
                Storage::disk('public')->delete($instansi->tanda_tangan);
            }
            // Simpan TTD baru di folder 'signatures'
            $data['tanda_tangan'] = $request->file('tanda_tangan')->store('signatures', 'public');
        }

        // 6. Simpan Perubahan ke Database
        $instansi->update($data);

        return redirect()->back()->with('success', 'Profil Instansi dan Aset Surat berhasil diperbarui!');
    }
}