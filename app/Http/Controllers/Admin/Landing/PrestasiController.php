<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\Prestasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrestasiController extends Controller
{
    public function index(Request $request)
    {
        $prestasis = Prestasi::latest()->paginate(9);
        return view('admin.landing.prestasi.index', compact('prestasis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'         => 'required|string|max:255',
            'nama_pemenang' => 'required|string|max:255',
            'tingkat'       => 'required', // Contoh: Kecamatan, Kabupaten, Provinsi, Nasional
            'foto'          => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tanggal'       => 'nullable|date',
        ]);

        $data = $request->except(['_token']);

        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            
            // PERBAIKAN:
            // 1. Hapus 'public/' di depan path.
            // 2. Tambahkan parameter ke-3 'public'.
            $image->storeAs('prestasis', $image->hashName(), 'public');
            
            $data['foto'] = $image->hashName();
        }

        Prestasi::create($data);

        return redirect()->back()->with('success', 'Prestasi berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $prestasi = Prestasi::findOrFail($id);

        $request->validate([
            'judul'         => 'required|string|max:255',
            'nama_pemenang' => 'required|string|max:255',
            'tingkat'       => 'required',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('foto')) {
            // PERBAIKAN: Hapus foto lama menggunakan disk 'public'
            if ($prestasi->foto && Storage::disk('public')->exists('prestasis/' . $prestasi->foto)) {
                Storage::disk('public')->delete('prestasis/' . $prestasi->foto);
            }

            // Upload foto baru
            $image = $request->file('foto');
            $image->storeAs('prestasis', $image->hashName(), 'public');
            $data['foto'] = $image->hashName();
        }

        $prestasi->update($data);

        return redirect()->back()->with('success', 'Prestasi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $prestasi = Prestasi::findOrFail($id);
        
        // PERBAIKAN: Hapus foto fisik menggunakan disk 'public'
        if ($prestasi->foto && Storage::disk('public')->exists('prestasis/' . $prestasi->foto)) {
            Storage::disk('public')->delete('prestasis/' . $prestasi->foto);
        }

        $prestasi->delete();

        return redirect()->back()->with('success', 'Prestasi berhasil dihapus!');
    }
}