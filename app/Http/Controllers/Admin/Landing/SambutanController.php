<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\SambutanKepalaSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SambutanController extends Controller
{
    public function index()
    {
        // Ambil data pertama, atau kosong jika belum ada
        $sambutan = SambutanKepalaSekolah::first();
        return view('admin.landing.sambutan.index', compact('sambutan'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_kepala_sekolah' => 'required|string|max:255',
            'judul_sambutan'      => 'required|string|max:255',
            'isi_sambutan'        => 'required|string',
            'foto'                => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'visi'                => 'nullable|string', // Baru
            'misi'                => 'nullable|string', // Baru
            'program_kerja'       => 'nullable|string', // Baru
        ]);

        // Cek apakah data sudah ada
        $sambutan = SambutanKepalaSekolah::first();

        $data = $request->except(['_token', 'foto']);

        // Logic Upload Foto
        if ($request->hasFile('foto')) {
            // 1. Hapus foto lama jika ada
            if ($sambutan && $sambutan->foto) {
                if (Storage::disk('public')->exists('sambutan/' . $sambutan->foto)) {
                    Storage::disk('public')->delete('sambutan/' . $sambutan->foto);
                }
            }

            // 2. Simpan Foto Baru
            $image = $request->file('foto');
            $image->storeAs('sambutan', $image->hashName(), 'public');
            
            $data['foto'] = $image->hashName();
        }

        // Update atau Buat Baru (ID selalu 1 karena single data)
        SambutanKepalaSekolah::updateOrCreate(
            ['id' => 1], // Kunci pencarian
            $data        // Data yang disimpan
        );

        return redirect()->back()->with('success', 'Data Profil Sekolah berhasil diperbarui!');
    }
}