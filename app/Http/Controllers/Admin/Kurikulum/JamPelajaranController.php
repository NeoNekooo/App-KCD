<?php

namespace App\Http\Controllers\Admin\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\JamPelajaran;
use Illuminate\Http\Request;

class JamPelajaranController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil data dan mengelompokkannya berdasarkan hari
        // Agar di View bisa kita buat Tab per Hari (Senin tab sendiri, Jumat tab sendiri)
        $jamPelajarans = JamPelajaran::orderBy('urutan')->get()->groupBy('hari');

        $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('admin.kurikulum.jam-pelajaran.index', compact('jamPelajarans', 'daftarHari'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|array', // Bisa pilih banyak hari sekaligus
            'nama' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'tipe' => 'required',
            'urutan' => 'required|integer'
        ]);

        // Fitur pintar: User bisa centang Senin, Selasa, Rabu sekaligus
        // Sistem akan membuatkan row untuk masing-masing hari tersebut.
        foreach ($request->hari as $hari) {
            // Cek apakah urutan sudah ada untuk hari tersebut (biar tidak bentrok)
            $exists = JamPelajaran::where('hari', $hari)->where('urutan', $request->urutan)->exists();

            if ($exists) {
                // Opsional: Skip atau update, di sini kita skip/error
                continue;
            }

            JamPelajaran::create([
                'hari' => $hari,
                'nama' => $request->nama,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'tipe' => $request->tipe,
                'urutan' => $request->urutan,
            ]);
        }

        return redirect()->back()->with('success', 'Data Jam Pelajaran berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $jam = JamPelajaran::findOrFail($id);

        $request->validate([
            'nama' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'tipe' => 'required',
            'urutan' => 'required|integer'
        ]);

        $jam->update($request->all());

        return redirect()->back()->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        JamPelajaran::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
