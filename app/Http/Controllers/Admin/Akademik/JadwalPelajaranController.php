<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\Rombel;
use App\Models\Gtk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class JadwalPelajaranController extends Controller
{
    /**
     * Menampilkan halaman untuk memilih Rombel.
     */
    public function index()
    {
        $rombels = Rombel::orderBy('nama')->get();
        return view('admin.akademik.jadwal.index', compact('rombels'));
    }

    /**
     * Menampilkan form untuk mengedit jadwal pelajaran sebuah Rombel.
     */
    public function edit(Rombel $rombel)
    {
        // Decode JSON dari kolom pembelajaran
        $mataPelajaranList = json_decode($rombel->pembelajaran, true) ?? [];

        // Kumpulkan semua ptk_id dari daftar mata pelajaran
        $ptkIds = array_column($mataPelajaranList, 'ptk_id');

        // Ambil data GTK (guru) berdasarkan ptk_id yang terkumpul dalam satu query
        $gurus = Gtk::whereIn('ptk_id', $ptkIds)->pluck('nama', 'ptk_id');

        // Tambahkan nama guru ke setiap mata pelajaran dalam daftar
        $mataPelajaranList = array_map(function ($mapel) use ($gurus) {
            $mapel['nama_guru'] = $gurus[$mapel['ptk_id']] ?? 'Guru Tidak Ditemukan';
            return $mapel;
        }, $mataPelajaranList);

        // Ambil jadwal yang sudah ada dari database
        $jadwalSudahAda = JadwalPelajaran::where('rombel_id', $rombel->id)
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        return view('admin.akademik.jadwal.edit', compact('rombel', 'mataPelajaranList', 'jadwalSudahAda'));
    }

    /**
     * Menyimpan entri jadwal pelajaran baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
            'pembelajaran_key' => 'required',
            'hari' => 'required|string',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $rombel = Rombel::findOrFail($request->rombel_id);
        $pembelajaranData = json_decode($rombel->pembelajaran, true);

        // Ambil detail mapel dan guru dari array JSON berdasarkan key/index yang dikirim
        $mapelInfo = $pembelajaranData[$request->pembelajaran_key];

        JadwalPelajaran::create([
            'rombel_id' => $rombel->id,
            'ptk_id' => $mapelInfo['ptk_id'],
            'mata_pelajaran' => $mapelInfo['nama_mata_pelajaran'],
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'tahun_ajaran_id' => '2023/2024', // Ganti dengan tapel aktif
            'semester_id' => 'Ganjil',      // Ganti dengan semester aktif
        ]);

        return Redirect::route('admin.akademik.jadwal.edit', $rombel->id)
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    /**
     * Menghapus entri jadwal pelajaran.
     */
    public function destroy(JadwalPelajaran $jadwal)
    {
        $rombelId = $jadwal->rombel_id;
        $jadwal->delete();

        return Redirect::route('admin.akademik.jadwal.edit', $rombelId)
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}
