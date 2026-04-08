<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Instansi;
use Illuminate\Support\Facades\Storage;

class ProfilWebsiteController extends Controller
{
    public function index()
    {
        $instansi = Instansi::first();
        if (!$instansi) {
            $instansi = Instansi::create(['nama_instansi' => 'KCD Wilayah Baru']);
        }
        return view('admin.website.profil.index', compact('instansi'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'visi'          => 'nullable|string',
            'misi'          => 'nullable|string',
            'sejarah_singkat' => 'nullable|string',
            'foto_profil'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $instansi = Instansi::first();
        $data = $request->only(['visi', 'misi', 'sejarah_singkat']);

        if ($request->hasFile('foto_profil')) {
            if ($instansi->foto_profil && Storage::disk('public')->exists($instansi->foto_profil)) {
                Storage::disk('public')->delete($instansi->foto_profil);
            }
            $data['foto_profil'] = $request->file('foto_profil')->store('profil', 'public');
        }

        $instansi->update($data);

        return redirect()->back()->with('success', 'Profil Website beserta Foto berhasil diperbarui!');
    }
}
