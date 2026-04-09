<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::orderBy('created_at', 'desc')->get();
        return view('admin.website.pengumuman.index', compact('pengumuman'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'lampiran' => 'nullable|file|max:5120',
            'prioritas' => 'required|in:biasa,penting,urgent',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'status' => 'required|in:draft,publish',
        ]);

        $data = $request->except('lampiran');
        $data['slug'] = Str::slug($request->judul) . '-' . Str::random(5);

        if ($request->hasFile('lampiran')) {
            $data['lampiran'] = $request->file('lampiran')->store('pengumuman', 'public');
        }

        Pengumuman::create($data);
        return redirect()->back()->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = Pengumuman::findOrFail($id);
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'lampiran' => 'nullable|file|max:5120',
            'prioritas' => 'required|in:biasa,penting,urgent',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'status' => 'required|in:draft,publish',
        ]);

        $data = $request->except('lampiran');
        if ($request->hasFile('lampiran')) {
            if ($item->lampiran && Storage::disk('public')->exists($item->lampiran)) {
                Storage::disk('public')->delete($item->lampiran);
            }
            $data['lampiran'] = $request->file('lampiran')->store('pengumuman', 'public');
        }

        $item->update($data);
        return redirect()->back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = Pengumuman::findOrFail($id);
        if ($item->lampiran && Storage::disk('public')->exists($item->lampiran)) {
            Storage::disk('public')->delete($item->lampiran);
        }
        $item->delete();
        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
