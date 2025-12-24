<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimoniController extends Controller
{
    public function index()
    {
        // Urutkan: Yang belum diapprove (pending) muncul paling atas
        $testimonis = Testimoni::orderBy('is_published', 'asc')
                               ->latest()
                               ->paginate(9);

        // Pisahkan data agar mudah di-looping di view
    $pending = \App\Models\Testimoni::where('is_published', false)->latest()->get();
    $published = \App\Models\Testimoni::where('is_published', true)->latest()->get();
                               
        return view('admin.landing.testimoni.index', compact('testimonis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:255',
            'status' => 'required|string|max:255', // Misal: Alumni, Ortu Siswa
            'isi'    => 'required|string',
            'foto'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except(['_token']);
        
        // Kalau admin yang input manual, kita anggap langsung published (true)
        $data['is_published'] = true; 

        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            
            // PERBAIKAN: 
            // 1. Path jadi 'testimonis' (tanpa 'public/')
            // 2. Tambah parameter 'public' di belakang
            $image->storeAs('testimonis', $image->hashName(), 'public');
            
            $data['foto'] = $image->hashName();
        }

        Testimoni::create($data);

        return redirect()->back()->with('success', 'Testimoni berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $testimoni = Testimoni::findOrFail($id);

        $request->validate([
            'nama'   => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'isi'    => 'required|string',
            'foto'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('foto')) {
            // PERBAIKAN: Hapus foto lama pakai disk public
            if ($testimoni->foto && Storage::disk('public')->exists('testimonis/' . $testimoni->foto)) {
                Storage::disk('public')->delete('testimonis/' . $testimoni->foto);
            }

            // Upload foto baru
            $image = $request->file('foto');
            $image->storeAs('testimonis', $image->hashName(), 'public');
            $data['foto'] = $image->hashName();
        }

        $testimoni->update($data);

        return redirect()->back()->with('success', 'Testimoni berhasil diperbarui!');
    }

    // FITUR BARU: Toggle Status Publish
    public function toggleStatus($id)
    {
        $testimoni = Testimoni::findOrFail($id);
        
        // Ubah status kebalikan (true jadi false, false jadi true)
        $testimoni->is_published = !$testimoni->is_published;
        $testimoni->save();

        $statusMsg = $testimoni->is_published ? 'ditayangkan (Published)' : 'disembunyikan (Pending)';
        return redirect()->back()->with('success', 'Testimoni berhasil ' . $statusMsg);
    }

    public function destroy($id)
    {
        $testimoni = Testimoni::findOrFail($id);
        
        // PERBAIKAN: Hapus foto fisik pakai disk public
        if ($testimoni->foto && Storage::disk('public')->exists('testimonis/' . $testimoni->foto)) {
            Storage::disk('public')->delete('testimonis/' . $testimoni->foto);
        }

        $testimoni->delete();

        return redirect()->back()->with('success', 'Testimoni berhasil dihapus!');
    }
}