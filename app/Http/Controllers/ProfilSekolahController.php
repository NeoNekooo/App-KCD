<?php

namespace App\Http\Controllers;

use App\Models\ProfilSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilSekolahController extends Controller
{
    public function edit()
    {
        $profil = ProfilSekolah::firstOrCreate(['id' => 1]);
        return view('admin.pengaturan.profil_sekolah.edit', compact('profil'));
    }

    public function update(Request $request)
    {
        $profil = ProfilSekolah::firstOrCreate(['id' => 1]);
        $data = $request->except(['logo', 'icon']);

        if ($request->hasFile('logo')) {
            if ($profil->logo) {
                Storage::disk('public')->delete($profil->logo);
            }
            $data['logo'] = $request->file('logo')->store('profil', 'public');
        }

        if ($request->hasFile('icon')) {
            if ($profil->icon) {
                Storage::disk('public')->delete($profil->icon);
            }
            $data['icon'] = $request->file('icon')->store('profil', 'public');
        }

        // PERUBAHAN UTAMA DI SINI
        // Isi model dengan data baru, tapi jangan simpan dulu
        $profil->fill($data);

        // Cek apakah ada perubahan data
        if ($profil->isDirty()) {
            // Jika ada perubahan, simpan dan kirim pesan sukses
            $profil->save();
            return redirect()->route('admin.pengaturan.profil_sekolah.edit')->with('success', 'Profil sekolah berhasil diperbarui!');
        } else {
            // Jika tidak ada perubahan, kirim pesan info
            return redirect()->route('admin.pengaturan.profil_sekolah.edit')->with('info', 'Anda tidak mengubah data apapun.');
        }
    }
}
