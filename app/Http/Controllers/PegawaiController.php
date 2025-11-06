<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\ProfilSekolah;
use App\Models\TugasPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawais = Pegawai::with(['tugasTerbaru'])->latest()->get();
        return view('admin.kepegawaian.pegawai.index', compact('pegawais'));
    }

    public function create()
    {
        return view('admin.kepegawaian.pegawai.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nip' => 'nullable|string|unique:pegawais,nip',
            'nik' => 'nullable|string|unique:pegawais,nik',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tanda_tangan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['foto', 'tanda_tangan']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('pegawai/foto', 'public');
        }

        if ($request->hasFile('tanda_tangan')) {
            $data['tanda_tangan'] = $request->file('tanda_tangan')->store('pegawai/ttd', 'public');
        }

        Pegawai::create($data);

        return redirect()->route('admin.kepegawaian.pegawai.index')->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function show(Pegawai $pegawai)
    {
        $profilSekolah = ProfilSekolah::first();
        $tugas = TugasPegawai::where('pegawai_id', $pegawai->id)->latest()->first();

        return view('admin.kepegawaian.pegawai.show', compact('pegawai', 'profilSekolah', 'tugas'));
    }

    public function edit(Pegawai $pegawai)
    {
        return view('admin.kepegawaian.pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nip' => 'nullable|string|unique:pegawais,nip,' . $pegawai->id,
            'nik' => 'nullable|string|unique:pegawais,nik,' . $pegawai->id,
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tanda_tangan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['foto', 'tanda_tangan']);

        if ($request->hasFile('foto')) {
            if ($pegawai->foto) {
                Storage::disk('public')->delete($pegawai->foto);
            }
            $data['foto'] = $request->file('foto')->store('pegawai/foto', 'public');
        }

        if ($request->hasFile('tanda_tangan')) {
            if ($pegawai->tanda_tangan) {
                Storage::disk('public')->delete($pegawai->tanda_tangan);
            }
            $data['tanda_tangan'] = $request->file('tanda_tangan')->store('pegawai/ttd', 'public');
        }

        $pegawai->fill($data);

        if ($pegawai->isDirty()) {
            $pegawai->save();
            return redirect()->route('admin.kepegawaian.pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
        } else {
            return redirect()->route('admin.kepegawaian.pegawai.index')->with('info', 'Anda tidak mengubah data apapun.');
        }
    }

    public function destroy(Pegawai $pegawai)
    {
        if ($pegawai->foto) {
            Storage::disk('public')->delete($pegawai->foto);
        }
        if ($pegawai->tanda_tangan) {
            Storage::disk('public')->delete($pegawai->tanda_tangan);
        }

        $pegawai->delete();

        return redirect()->route('admin.kepegawaian.pegawai.index')->with('success', 'Data pegawai berhasil dihapus.');
    }
}
