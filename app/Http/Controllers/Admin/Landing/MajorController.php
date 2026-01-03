<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MajorController extends Controller
{
    public function index(Request $request)
    {
        $query = Major::latest();

        if ($request->has('q') && $request->q != '') {
            $query->where('nama_jurusan', 'like', '%' . $request->q . '%')
                  ->orWhere('singkatan', 'like', '%' . $request->q . '%');
        }

        $jurusans = $query->paginate(8);

        return view('admin.landing.jurusan.index', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jurusan'   => 'required|string|max:255',
            'singkatan'      => 'required|string|max:10',
            'kepala_jurusan' => 'nullable|string|max:255',
            'deskripsi'      => 'nullable|string', // Tambahkan validasi
            'gambar'         => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['_token']);

        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $image->storeAs('jurusans', $image->hashName(), 'public'); 
            $data['gambar'] = $image->hashName();
        }

        Major::create($data);

        return redirect()->back()->with('success', 'Jurusan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $jurusan = Major::findOrFail($id);

        $request->validate([
            'nama_jurusan'   => 'required|string|max:255',
            'singkatan'      => 'required|string|max:10',
            'kepala_jurusan' => 'nullable|string|max:255',
            'deskripsi'      => 'nullable|string', // Tambahkan validasi
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('gambar')) {
            if ($jurusan->gambar && Storage::disk('public')->exists('jurusans/' . $jurusan->gambar)) {
                Storage::disk('public')->delete('jurusans/' . $jurusan->gambar);
            }

            $image = $request->file('gambar');
            $image->storeAs('jurusans', $image->hashName(), 'public');
            $data['gambar'] = $image->hashName();
        }

        $jurusan->update($data);

        return redirect()->back()->with('success', 'Jurusan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jurusan = Major::findOrFail($id);
        
        if ($jurusan->gambar && Storage::disk('public')->exists('jurusans/' . $jurusan->gambar)) {
            Storage::disk('public')->delete('jurusans/' . $jurusan->gambar);
        }

        $jurusan->delete();

        return redirect()->back()->with('success', 'Jurusan berhasil dihapus!');
    }
}