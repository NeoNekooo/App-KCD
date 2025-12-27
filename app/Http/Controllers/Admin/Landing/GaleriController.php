<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use App\Models\GaleriItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    // Menampilkan daftar Album
    public function index()
    {
        $galeris = Galeri::withCount('items')->latest()->paginate(9);
        return view('admin.landing.galeri.index', compact('galeris'));
    }

    // Menyimpan Album Baru
    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'foto'      => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Cover Album
            'tanggal'   => 'nullable|date',
        ]);

        $data = $request->except(['_token']);

        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            $image->storeAs('galeris/covers', $image->hashName(), 'public');
            $data['foto'] = $image->hashName();
        }

        Galeri::create($data);

        return redirect()->back()->with('success', 'Album berhasil dibuat!');
    }

    // Halaman Detail Album (Untuk upload banyak foto/video)
    public function show($id)
    {
        $galeri = Galeri::with('items')->findOrFail($id);
        return view('admin.landing.galeri.show', compact('galeri'));
    }

    // Update Info Album (Judul, Deskripsi, Cover)
    public function update(Request $request, $id)
    {
        $galeri = Galeri::findOrFail($id);

        $request->validate([
            'judul'     => 'required|string|max:255',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('foto')) {
            if ($galeri->foto && Storage::disk('public')->exists('galeris/covers/' . $galeri->foto)) {
                Storage::disk('public')->delete('galeris/covers/' . $galeri->foto);
            }
            $image = $request->file('foto');
            $image->storeAs('galeris/covers', $image->hashName(), 'public');
            $data['foto'] = $image->hashName();
        }

        $galeri->update($data);

        return redirect()->back()->with('success', 'Info album diperbarui!');
    }

    // Logic Upload Item (Foto/Video) ke dalam Album
    public function storeItem(Request $request, $id)
    {
        $galeri = Galeri::findOrFail($id);

        $request->validate([
            'files.*' => 'required|mimes:jpeg,png,jpg,webp,mp4,mov,avi|max:20480', // Max 20MB untuk video
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Deteksi tipe file
                $mime = $file->getMimeType();
                $jenis = str_contains($mime, 'video') ? 'video' : 'foto';

                // Simpan file
                $file->storeAs('galeris/items', $file->hashName(), 'public');

                // Simpan ke database
                GaleriItem::create([
                    'galeri_id' => $galeri->id,
                    'file'      => $file->hashName(),
                    'jenis'     => $jenis,
                    'caption'   => $file->getClientOriginalName(), // Default caption nama file
                ]);
            }
        }

        return redirect()->back()->with('success', 'File berhasil ditambahkan ke album!');
    }

    // Hapus Item Spesifik (Satu Foto/Video)
    public function destroyItem($id)
    {
        $item = GaleriItem::findOrFail($id);

        if (Storage::disk('public')->exists('galeris/items/' . $item->file)) {
            Storage::disk('public')->delete('galeris/items/' . $item->file);
        }

        $item->delete();

        return redirect()->back()->with('success', 'Item berhasil dihapus!');
    }

    // Hapus Album Beserta Isinya
    public function destroy($id)
    {
        $galeri = Galeri::with('items')->findOrFail($id);
        
        // 1. Hapus Cover
        if ($galeri->foto && Storage::disk('public')->exists('galeris/covers/' . $galeri->foto)) {
            Storage::disk('public')->delete('galeris/covers/' . $galeri->foto);
        }

        // 2. Hapus Semua File Item Fisik
        foreach ($galeri->items as $item) {
            if (Storage::disk('public')->exists('galeris/items/' . $item->file)) {
                Storage::disk('public')->delete('galeris/items/' . $item->file);
            }
        }

        // 3. Database akan otomatis hapus items karena onDelete('cascade') di migration
        $galeri->delete();

        return redirect()->route('admin.landing.galeri.index')->with('success', 'Album dan seluruh isinya berhasil dihapus!');
    }
}