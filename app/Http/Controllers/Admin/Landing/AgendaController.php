<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    /**
     * Menampilkan halaman index agenda
     */
    public function index()
    {
        // Urutkan agenda dari yang terbaru
        $agendas = Agenda::latest()->paginate(10);
        return view('admin.landing.agenda.index', compact('agendas'));
    }

    /**
     * Menyimpan agenda baru
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'judul'           => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            // Tambahkan 'PPDB' ke dalam daftar kategori yang valid
            'kategori'        => 'required|in:Akademik,Kegiatan,Libur,Rapat,PPDB', 
            'deskripsi'       => 'nullable|string',
        ]);

        // 2. Simpan Data
        Agenda::create([
            'judul'           => $request->judul,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai ?? $request->tanggal_mulai, // Jika kosong, set sama dengan mulai
            'kategori'        => $request->kategori,
            'deskripsi'       => $request->deskripsi,
        ]);

        return redirect()->route('admin.landing.agenda.index')->with('success', 'Agenda berhasil ditambahkan.');
    }

    /**
     * Update agenda yang sudah ada
     */
    public function update(Request $request, $id)
    {
        $agenda = Agenda::findOrFail($id);

        $request->validate([
            'judul'           => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'kategori'        => 'required|in:Akademik,Kegiatan,Libur,Rapat,PPDB',
            'deskripsi'       => 'nullable|string',
        ]);

        $agenda->update([
            'judul'           => $request->judul,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai ?? $request->tanggal_mulai,
            'kategori'        => $request->kategori,
            'deskripsi'       => $request->deskripsi,
        ]);

        return redirect()->route('admin.landing.agenda.index')->with('success', 'Agenda berhasil diperbarui.');
    }

    /**
     * Hapus agenda
     */
    public function destroy($id)
    {
        $agenda = Agenda::findOrFail($id);
        $agenda->delete();

        return redirect()->route('admin.landing.agenda.index')->with('success', 'Agenda berhasil dihapus.');
    }
}