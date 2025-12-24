<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    public function index()
    {
        $galeris = Galeri::latest()->paginate(9);
        return view('admin.landing.galeri.index', compact('galeris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'foto'      => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tanggal'   => 'nullable|date',
        ]);

        $data = $request->except(['_token']);

        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            
            // PERBAIKAN: 
            // 1. Path jadi 'galeris' (tanpa 'public/')
            // 2. Tambah parameter 'public' di belakang
            $image->storeAs('galeris', $image->hashName(), 'public');
            
            $data['foto'] = $image->hashName();
        }

        Galeri::create($data);

        return redirect()->back()->with('success', 'Album kegiatan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $galeri = Galeri::findOrFail($id);

        $request->validate([
            'judul'     => 'required|string|max:255',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('foto')) {
            // PERBAIKAN: Hapus foto lama pakai disk public
            if ($galeri->foto && Storage::disk('public')->exists('galeris/' . $galeri->foto)) {
                Storage::disk('public')->delete('galeris/' . $galeri->foto);
            }

            // Upload foto baru
            $image = $request->file('foto');
            $image->storeAs('galeris', $image->hashName(), 'public');
            $data['foto'] = $image->hashName();
        }

        $galeri->update($data);

        return redirect()->back()->with('success', 'Album kegiatan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $galeri = Galeri::findOrFail($id);
        
        // PERBAIKAN: Hapus foto fisik pakai disk public
        if ($galeri->foto && Storage::disk('public')->exists('galeris/' . $galeri->foto)) {
            Storage::disk('public')->delete('galeris/' . $galeri->foto);
        }

        $galeri->delete();

        return redirect()->back()->with('success', 'Album kegiatan berhasil dihapus!');
    }
}