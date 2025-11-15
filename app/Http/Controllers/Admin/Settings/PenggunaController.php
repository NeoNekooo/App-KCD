<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class PenggunaController extends Controller
{
    /**
     * Menampilkan daftar pengguna.
     */
    public function index()
    {
        // === BAGIAN YANG DIPERBAIKI ===
        // Mengganti latest() dengan orderBy() untuk memastikan kompatibilitas
        $penggunas = Pengguna::orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.settings.pengguna.index', compact('penggunas'));
    }

    /**
     * Menampilkan form untuk membuat pengguna baru.
     */
    public function create()
    {
        return view('admin.settings.pengguna.create');
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:penggunas'],
            'peran_id_str' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        Pengguna::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'peran_id_str' => $request->peran_id_str,
            'password' => $request->password, // Model akan otomatis hash
        ]);

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit pengguna.
     */
    public function edit(Pengguna $pengguna)
    {
        return view('admin.settings.pengguna.edit', compact('pengguna'));
    }

    /**
     * Mengupdate data pengguna di database.
     */
    public function update(Request $request, Pengguna $pengguna)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:penggunas,username,' . $pengguna->id],
            'peran_id_str' => ['required', 'string'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $pengguna->update([
            'nama' => $request->nama,
            'username' => $request->username,
            'peran_id_str' => $request->peran_id_str,
        ]);

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $pengguna->update([
                'password' => $request->password,
            ]);
        }

        return redirect()->route('admin.pengguna.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna dari database.
     */
    public function destroy(Pengguna $pengguna)
    {
        // Hati-hati: pastikan ada konfirmasi sebelum menghapus
        $pengguna->delete();
        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}