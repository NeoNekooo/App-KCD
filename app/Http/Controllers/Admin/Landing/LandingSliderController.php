<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\LandingSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingSliderController extends Controller
{
    // Menampilkan daftar slider
    public function index()
    {
        $sliders = LandingSlider::orderBy('urutan', 'asc')->get();
        return view('admin.landing.slider.index', compact('sliders'));
    }

    // Menampilkan form tambah
    public function create()
    {
        return view('admin.landing.slider.create');
    }

    // Menyimpan data baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'nullable|string|max:255',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'urutan' => 'integer',
        ]);

        $data = $request->except(['_token']);

        // Proses Upload Gambar
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            
            // PERBAIKAN:
            // 1. Path jadi 'sliders' (tanpa 'public/' di depan)
            // 2. Tambahkan parameter ke-3 'public' agar bisa diakses browser
            $image->storeAs('sliders', $image->hashName(), 'public'); 
            
            // Simpan hanya nama filenya saja ke database
            $data['gambar'] = $image->hashName();
        }

        LandingSlider::create($data);

        return redirect()->route('admin.landing.slider.index')->with('success', 'Slider berhasil ditambahkan!');
    }

    // Menampilkan form edit
    public function edit($id)
    {
        $slider = LandingSlider::findOrFail($id);
        return view('admin.landing.slider.edit', compact('slider'));
    }

    // Mengupdate data
    public function update(Request $request, $id)
    {
        $slider = LandingSlider::findOrFail($id);

        $request->validate([
            'judul' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['_token', '_method']);

        // Cek jika ada upload gambar baru
        if ($request->hasFile('gambar')) {
            
            // Hapus gambar lama dulu
            // PERBAIKAN: Gunakan disk('public') agar target hapusnya benar
            if ($slider->gambar && Storage::disk('public')->exists('sliders/' . $slider->gambar)) {
                Storage::disk('public')->delete('sliders/' . $slider->gambar);
            }

            // Upload gambar baru
            $image = $request->file('gambar');
            
            // PERBAIKAN: Tambahkan parameter 'public'
            $image->storeAs('sliders', $image->hashName(), 'public');
            
            $data['gambar'] = $image->hashName();
        }

        $slider->update($data);

        return redirect()->route('admin.landing.slider.index')->with('success', 'Slider berhasil diperbarui!');
    }

    // Menghapus data
    public function destroy($id)
    {
        $slider = LandingSlider::findOrFail($id);
        
        // Hapus file gambar fisik dari penyimpanan
        // PERBAIKAN: Gunakan disk('public')
        if ($slider->gambar && Storage::disk('public')->exists('sliders/' . $slider->gambar)) {
            Storage::disk('public')->delete('sliders/' . $slider->gambar);
        }
        
        // Hapus data dari database
        $slider->delete();

        return redirect()->route('admin.landing.slider.index')->with('success', 'Slider berhasil dihapus!');
    }
}