<?php

namespace App\Http\Controllers\Admin\Pengaturan;

use App\Http\Controllers\Controller;
use App\Models\HariLibur; // Pastikan Anda membuat model ini
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
    public function index()
    {
        $hariLibur = HariLibur::orderBy('tanggal', 'desc')->get();
        return view('admin.pengaturan.hari-libur.index', compact('hariLibur'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:hari_libur,tanggal',
            'keterangan' => 'required|string|max:255',
        ]);

        HariLibur::create($request->all());
        return back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function destroy(HariLibur $hariLibur)
    {
        $hariLibur->delete();
        return back()->with('success', 'Hari libur berhasil dihapus.');
    }
}
