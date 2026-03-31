<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AntrianTamu;
use Carbon\Carbon;

class AntrianController extends Controller
{
    /**
     * Dashboard List Antrian Resepsionis
     */
    public function index()
    {
        // Tampilkan hanya antrian hari ini yang masih aktif (belum selesai/batal)
        // Atau urutkan berdasarkan status "menunggu" dan "dipanggil"
        $today = Carbon::today();
        
        $antrians = AntrianTamu::with('tujuanPegawai.jabatanKcd')
                    ->whereDate('created_at', $today)
                    ->orderByRaw("FIELD(status, 'dipanggil', 'menunggu', 'selesai', 'batal') ASC")
                    ->orderBy('id', 'asc')
                    ->get();

        return view('admin.antrian.index', compact('antrians'));
    }

    /**
     * Menyelesaikan atau memanggil tamu (Max 3x sesuai req)
     */
    public function panggil(Request $request, $id)
    {
        $antrian = AntrianTamu::findOrFail($id);
        
        // Cek kalau udah dipanggil 3x
        if ($antrian->jumlah_panggilan >= 3) {
            // Selesaikan otomatis secara logic jika diminta (bisa opsional)
            return redirect()->back()->with('error', 'Tamu sudah dipanggil batas maksimal 3x, klik "Lewati Selesai" jika tidak merespons.');
        }

        $antrian->status = 'dipanggil';
        $antrian->waktu_panggilan = Carbon::now();
        $antrian->jumlah_panggilan += 1;
        $antrian->save();

        return redirect()->back()->with('success', 'Berhasil memanggil! Antrian di-highlight di layar TV.');
    }

    public function selesai($id)
    {
        $antrian = AntrianTamu::findOrFail($id);
        $antrian->status = 'selesai';
        $antrian->waktu_selesai = Carbon::now();
        $antrian->save();

        return redirect()->back()->with('success', 'Status tamu berhasil diselesaikan.');
    }

    public function destroy($id)
    {
        $antrian = AntrianTamu::findOrFail($id);
        $antrian->status = 'batal';
        $antrian->save();

        return redirect()->back()->with('success', 'Antrian tamu dibatalkan.');
    }

    /**
     * AJAX Get Partial Table Body for Realtime Update
     */
    public function getPartial()
    {
        $today = Carbon::today();
        $antrians = AntrianTamu::with('tujuanPegawai.jabatanKcd')
                    ->whereDate('created_at', $today)
                    ->orderByRaw("FIELD(status, 'dipanggil', 'menunggu', 'selesai', 'batal') ASC")
                    ->orderBy('id', 'asc')
                    ->get();

        return view('admin.antrian._table_body', compact('antrians'))->render();
    }
}
