<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data terbaru & paginate
        $beritas = Berita::latest()->paginate(9);
        return view('admin.landing.berita.index', compact('beritas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'isi'       => 'required',
            'gambar'    => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status'    => 'required|in:published,draft',
        ]);

        $data = $request->except(['_token']);
        
        // 1. Buat Slug Otomatis
        $data['slug'] = Str::slug($request->judul) . '-' . time();
        
        // 2. Buat Ringkasan Otomatis
        $data['ringkasan'] = Str::limit(strip_tags($request->isi), 100);
        
        // 3. Set Penulis
        $data['penulis'] = 'Administrator';

        // 4. Upload Gambar (PERBAIKAN DISINI)
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            // Simpan ke folder 'beritas' dengan disk 'public'
            $image->storeAs('beritas', $image->hashName(), 'public');
            $data['gambar'] = $image->hashName();
        }

        Berita::create($data);

        return redirect()->back()->with('success', 'Berita berhasil dipublish!');
    }

    public function update(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);

        $request->validate([
            'judul'     => 'required|string|max:255',
            'isi'       => 'required',
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status'    => 'required|in:published,draft',
        ]);

        $data = $request->except(['_token', '_method']);

        // Update Slug jika judul berubah
        if ($request->judul != $berita->judul) {
            $data['slug'] = Str::slug($request->judul) . '-' . time();
        }

        // Update Ringkasan
        $data['ringkasan'] = Str::limit(strip_tags($request->isi), 100);

        // Update Gambar (PERBAIKAN DISINI)
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama menggunakan disk public
            if ($berita->gambar && Storage::disk('public')->exists('beritas/' . $berita->gambar)) {
                Storage::disk('public')->delete('beritas/' . $berita->gambar);
            }
            
            // Upload gambar baru
            $image = $request->file('gambar');
            $image->storeAs('beritas', $image->hashName(), 'public');
            $data['gambar'] = $image->hashName();
        }

        $berita->update($data);

        return redirect()->back()->with('success', 'Berita berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $berita = Berita::findOrFail($id);
        
        // Hapus gambar fisik (PERBAIKAN DISINI)
        if ($berita->gambar && Storage::disk('public')->exists('beritas/' . $berita->gambar)) {
            Storage::disk('public')->delete('beritas/' . $berita->gambar);
        }

        $berita->delete();

        return redirect()->back()->with('success', 'Berita berhasil dihapus!');
    }
}