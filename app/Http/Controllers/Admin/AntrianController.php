<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AntrianTamu;
use App\Models\KeperluanCategory;
use App\Exports\AntrianExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AntrianController extends Controller
{
    /**
     * Dashboard List Antrian Resepsionis
     */
    public function index()
    {
        // Tampilkan semua riwayat antrian
        // Urutkan berdasarkan status (dipanggil/menunggu dulu) lalu tanggal terbaru
        $antrians = AntrianTamu::with('tujuanPegawai.jabatanKcd')
                    ->orderByRaw("FIELD(status, 'dipanggil', 'menunggu', 'selesai', 'batal') ASC")
                    ->orderBy('created_at', 'desc')
                    ->get();

        $categories = KeperluanCategory::orderBy('id', 'desc')->get();

        return view('admin.antrian.index', compact('antrians', 'categories'));
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
        $antrians = AntrianTamu::with('tujuanPegawai.jabatanKcd')
                    ->orderByRaw("FIELD(status, 'dipanggil', 'menunggu', 'selesai', 'batal') ASC")
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('admin.antrian._table_body', compact('antrians'))->render();
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:keperluan_categories,name'
        ]);

        KeperluanCategory::create(['name' => $request->name]);
    
        return redirect()->back()->with('success', 'Kategori keperluan berhasil ditambahkan.')->with('open_modal_kategori', true);
    }
    
    public function destroyCategory($id)
    {
        $category = KeperluanCategory::findOrFail($id);
        $category->delete();
    
        return redirect()->back()->with('success', 'Kategori keperluan berhasil dihapus.')->with('open_modal_kategori', true);
    }

    /**
     * Export Excel Antrian Tamu
     */
    public function export()
    {
        return Excel::download(new AntrianExport, 'Data_Antrian_Tamu_' . date('d-m-Y') . '.xlsx');
    }
}
