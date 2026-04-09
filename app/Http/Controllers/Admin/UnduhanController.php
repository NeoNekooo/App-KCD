<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unduhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UnduhanController extends Controller
{
    public function index()
    {
        $unduhan = Unduhan::orderBy('created_at', 'desc')->get();
        return view('admin.website.unduhan.index', compact('unduhan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'required|file|max:10240',
            'kategori' => 'required|string',
        ]);

        $data = $request->except('file');
        $data['file'] = $request->file('file')->store('unduhan', 'public');

        Unduhan::create($data);
        return redirect()->back()->with('success', 'File unduhan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = Unduhan::findOrFail($id);
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
            'kategori' => 'required|string',
        ]);

        $data = $request->except('file');
        if ($request->hasFile('file')) {
            if ($item->file && Storage::disk('public')->exists($item->file)) {
                Storage::disk('public')->delete($item->file);
            }
            $data['file'] = $request->file('file')->store('unduhan', 'public');
        }

        $item->update($data);
        return redirect()->back()->with('success', 'File unduhan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = Unduhan::findOrFail($id);
        if ($item->file && Storage::disk('public')->exists($item->file)) {
            Storage::disk('public')->delete($item->file);
        }
        $item->delete();
        return redirect()->back()->with('success', 'File unduhan berhasil dihapus.');
    }
}
