<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EkstrakurikulerController extends Controller
{
    /**
     * Menampilkan daftar semua ekstrakurikuler.
     */
    public function index()
    {
        // Menggunakan paginate atau get() sesuai kebutuhan, di sini menggunakan all()
        $ekskul = Ekstrakurikuler::all();
        return view('admin.akademik.ekstrakurikuler.index', compact('ekskul'));
    }

    /**
     * Menampilkan form untuk membuat ekstrakurikuler baru.
     */
    public function create()
    {
        return view('admin.akademik.ekstrakurikuler.create');
    }

    /**
     * Menyimpan data ekstrakurikuler baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Pastikan nama unik di tabel 'ekstrakurikuler'
            'nama' => 'required|string|max:100|unique:ekstrakurikuler,nama',
            'alias' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:500',
        ]);

        Ekstrakurikuler::create($request->all());

        // Menggunakan route name yang benar dari resource route: admin.akademik.ekstrakurikuler.index
        return redirect()->route('admin.akademik.ekstrakurikuler.index')->with('success', 'Ekstrakurikuler berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit ekstrakurikuler tertentu (menggunakan Route Model Binding).
     */
    public function edit(Ekstrakurikuler $ekstrakurikuler)
    {
        return view('admin.akademik.ekstrakurikuler.edit', compact('ekstrakurikuler'));
    }

    /**
     * Memperbarui data ekstrakurikuler yang ada (menggunakan Route Model Binding).
     */
    public function update(Request $request, Ekstrakurikuler $ekstrakurikuler)
    {
        $request->validate([
            // Validasi nama unik, kecuali untuk data ekstrakurikuler yang sedang diedit
            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('ekstrakurikuler', 'nama')->ignore($ekstrakurikuler->id),
            ],
            'alias' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $ekstrakurikuler->update($request->all());

        // Menggunakan route name yang benar
        return redirect()->route('admin.akademik.ekstrakurikuler.index')->with('success', 'Ekstrakurikuler berhasil diperbarui.');
    }

    /**
     * Menghapus ekstrakurikuler tertentu (menggunakan Route Model Binding).
     */
    public function destroy(Ekstrakurikuler $ekstrakurikuler)
    {
        $ekstrakurikuler->delete();
        
        // Menggunakan route name yang benar
        return redirect()->route('admin.akademik.ekstrakurikuler.index')->with('success', 'Ekstrakurikuler berhasil dihapus.');
    }
}