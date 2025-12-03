<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\DaftarEkstrakurikuler;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class DaftarEkstrakurikulerController extends Controller
{
    /**
     * Menampilkan daftar semua daftar ekstrakurikuler.
     */
    public function index()
    {
        $ekskul = DaftarEkstrakurikuler::all();
        return view('admin.akademik.daftar_ekstrakurikuler.index', compact('ekskul'));
    }

    /**
     * Menampilkan form untuk membuat daftar ekstrakurikuler baru.
     */
    public function create()
    {
        return view('admin.akademik.daftar_ekstrakurikuler.create');
    }

    /**
     * Menyimpan data daftar ekstrakurikuler baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Pastikan nama unik di tabel 'daftar_ekstrakurikuler'
            'nama' => 'required|string|max:100|unique:daftar_ekstrakurikuler,nama',
            'alias' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:500',
        ]);

        DaftarEkstrakurikuler::create($request->all());

        return redirect()->route('admin.akademik.daftar-ekstrakurikuler.index')->with('success', 'Ekstrakurikuler berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit daftar ekstrakurikuler tertentu (menggunakan Route Model Binding).
     */
    public function edit(DaftarEkstrakurikuler $daftar_ekstrakurikuler)
    {
        return view('admin.akademik.daftar_ekstrakurikuler.edit', ['ekstrakurikuler' => $daftar_ekstrakurikuler]);
    }

    /**
     * Memperbarui data daftar ekstrakurikuler yang ada (menggunakan Route Model Binding).
     */
    public function update(Request $request, DaftarEkstrakurikuler $daftar_ekstrakurikuler)
    {
        Log::debug('DaftarEkstrakurikulerController@update called', [
            'id' => $daftar_ekstrakurikuler->id ?? null,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'input' => $request->all(),
        ]);
        $request->validate([
            // Validasi nama unik, kecuali untuk data daftar ekstrakurikuler yang sedang diedit
            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('daftar_ekstrakurikuler', 'nama')->ignore($daftar_ekstrakurikuler->id),
            ],
            'alias' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $daftar_ekstrakurikuler->update($request->all());

        Log::debug('DaftarEkstrakurikuler updated', ['id' => $daftar_ekstrakurikuler->id]);

        return redirect()->route('admin.akademik.daftar-ekstrakurikuler.index')->with('success', 'Ekstrakurikuler berhasil diperbarui.');
    }

    /**
     * Menghapus daftar ekstrakurikuler tertentu (menggunakan Route Model Binding).
     */
    public function destroy(DaftarEkstrakurikuler $daftar_ekstrakurikuler)
    {
        Log::debug('DaftarEkstrakurikulerController@destroy called', ['id' => $daftar_ekstrakurikuler->id]);

        $daftar_ekstrakurikuler->delete();
        Log::debug('DaftarEkstrakurikuler deleted', ['id' => $daftar_ekstrakurikuler->id]);
        return redirect()->route('admin.akademik.daftar-ekstrakurikuler.index')->with('success', 'Ekstrakurikuler berhasil dihapus.');
    }
}
