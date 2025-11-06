<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Rombel; // Tambahkan ini untuk mengambil data rombel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{

public function index(Request $request)
{
    $query = Siswa::query()->with('rombel');

    // 1. Logika untuk Pencarian (TETAP SAMA)
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->where('nama', 'like', "%{$searchTerm}%")
              ->orWhere('nis', 'like', "%{$searchTerm}%")
              ->orWhere('nisn', 'like', "%{$searchTerm}%");
        });
    }

    if ($request->filled('rombel_id')) {
        $query->where('nama_rombel', $request->rombel_id);
    }

    $siswas = $query->latest()->paginate(10);

    // Rombels tetap digunakan untuk mengisi dropdown filter di view
    $rombels = Rombel::orderBy('nama')->get();

    return view('admin.kesiswaan.siswa.index', compact('siswas', 'rombels'));
}
    public function edit(Siswa $siswa)
    {
        $rombels = Rombel::orderBy('nama')->get();
        return view('admin.kesiswaan.siswa.edit', compact('siswa', 'rombels'));
    }

public function update(Request $request, Siswa $siswa)
{
  $request->validate([
        'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'status' => 'required|string|in:Aktif,Lulus,Pindah', // Harus di-update
        'nama' => 'nullable|string|max:255',         // Untuk menelan data hidden
        'jenis_kelamin' => 'nullable|in:L,P', // Untuk menelan data hidden
    ]);

    $data = [
        'status' => $request->status,
    ];

    // 3. Logika untuk kolom 'foto'
    if ($request->hasFile('foto')) {
        // Hapus foto lama jika ada
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }
        // Simpan foto baru dan tambahkan ke array data
        $data['foto'] = $request->file('foto')->store('siswa/foto', 'public');
        $message = 'Foto dan Status siswa berhasil diperbarui.';
    } else {
        $message = 'Status siswa berhasil diperbarui.';
    }

    // 4. Lakukan update data. (Update akan selalu berjalan karena $data minimal berisi 'status').
    $siswa->update($data);

    // 5. Redirect ke index dengan pesan sukses yang sesuai
    return redirect()->route('admin.kesiswaan.siswa.index')->with('success', $message);
}

}
