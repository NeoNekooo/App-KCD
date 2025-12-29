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
        $sambutan = SambutanKepalaSekolah::first();
        return view('admin.landing.sambutan.index', compact('sambutan'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_kepala_sekolah' => 'required|string|max:255',
            'judul_sambutan'      => 'required|string|max:255',
            'isi_sambutan'        => 'required|string',
            'foto'                => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Foto Kepsek
            
            // Validasi Tambahan
            'sejarah'             => 'nullable|string',
            'foto_gedung'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Foto Gedung
            'visi'                => 'nullable|string',
            'misi'                => 'nullable|string',
            'program_kerja'       => 'nullable|string',
        ]);

        // Cek apakah data sudah ada
        $sambutan = SambutanKepalaSekolah::first();

        // Ambil semua data input kecuali token dan file
        $data = $request->except(['_token', 'foto', 'foto_gedung']);

        // 1. Logic Upload Foto Kepala Sekolah
        if ($request->hasFile('foto')) {
            if ($sambutan && $sambutan->foto) {
                if (Storage::disk('public')->exists('sambutan/' . $sambutan->foto)) {
                    Storage::disk('public')->delete('sambutan/' . $sambutan->foto);
                }
            }
            $image = $request->file('foto');
            $image->storeAs('sambutan', $image->hashName(), 'public');
            $data['foto'] = $image->hashName();
        }

        // 2. Logic Upload Foto Gedung / Sejarah (BARU)
        if ($request->hasFile('foto_gedung')) {
            // Hapus foto gedung lama jika ada
            if ($sambutan && $sambutan->foto_gedung) {
                if (Storage::disk('public')->exists('sambutan/' . $sambutan->foto_gedung)) {
                    Storage::disk('public')->delete('sambutan/' . $sambutan->foto_gedung);
                }
            }
            // Simpan foto gedung baru
            $imageGedung = $request->file('foto_gedung');
            // Kita simpan di folder yang sama 'sambutan' atau bisa buat folder baru 'sekolah'
            $imageGedung->storeAs('sambutan', $imageGedung->hashName(), 'public');
            
            $data['foto_gedung'] = $imageGedung->hashName();
        }

        // Update atau Buat Baru (ID selalu 1)
        SambutanKepalaSekolah::updateOrCreate(
            ['id' => 1], 
            $data
        );

        return redirect()->back()->with('success', 'Data Profil & Sejarah Sekolah berhasil diperbarui!');
    }
}