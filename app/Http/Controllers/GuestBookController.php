<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AntrianTamu;
use App\Models\PegawaiKcd;
use App\Models\KeperluanCategory;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GuestBookController extends Controller
{
    /**
     * Menampilkan Form Buku Tamu QR Code (Public Landing Page)
     */
    public function index()
    {
        // Ambil data Pejabat/Pegawai KCD untuk Dropdown 'Ingin Bertemu Siapa'
        $pegawais = PegawaiKcd::with('jabatanKcd')->orderBy('nama', 'asc')->get();
        $instansi = \App\Models\Instansi::first();
        $categories = KeperluanCategory::orderBy('name', 'asc')->get();

        return view('guest.buku-tamu.index', compact('pegawais', 'instansi', 'categories'));
    }

    /**
     * Menyimpan data form tamu dan membuat Nomor Antrian
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'              => 'required|string|max:255',
            'npsn'              => 'nullable|string|max:20',
            'nomor_hp'          => 'required|string|max:20',
            'asal_instansi'     => 'required|string|max:255',
            'jabatan_pengunjung' => 'required|string|max:100',
            'keperluan'         => 'required|string|max:500',
        ]);

        // Logic Auto Generate Nomer Antrian (langsung angka, e.g., 001)
        $today = Carbon::today();
        
        $countToday = AntrianTamu::whereDate('created_at', $today)->count();
        $urutan = $countToday + 1;

        // Format nomor: Langsung angka dengan padding 3 digit
        $nomorBaru = str_pad($urutan, 3, '0', STR_PAD_LEFT);

        // Simpan Data
        $antrian = AntrianTamu::create([
            'nomor_antrian'     => $nomorBaru,
            'nama'              => $request->nama,
            'npsn'              => $request->npsn,
            'nomor_hp'          => $request->nomor_hp,
            'asal_instansi'     => $request->asal_instansi,
            'jabatan_pengunjung' => $request->jabatan_pengunjung,
            'keperluan'         => $request->keperluan,
            'tujuan_pegawai_id' => $request->tujuan_pegawai_id, // Tambahkan ini biar tersimpan!
            'status'            => 'menunggu', 
            'jumlah_panggilan'  => 0,
        ]);

        // Simpan data di session agar tamu bisa melihat tiketnya usai redirect
        // Pake back() biar lebih stabil di semua jenis device
        return back()->with([
            'tiket_success' => true,
            'tiket_id'      => $antrian->id,
            'tiket_nomor'   => $antrian->nomor_antrian,
            'tiket_nama'    => $antrian->nama,
            'tiket_tujuan'  => $antrian->tujuanPegawai ? $antrian->tujuanPegawai->nama : 'Umum/Resepsionis',
            'tiket_waktu'   => $antrian->created_at->format('d M Y, H:i')
        ]);
    }

    /**
     * Request Print dari HP Tamu
     */
    public function requestPrint($id)
    {
        $antrian = AntrianTamu::findOrFail($id);
        $antrian->print_requested = true;
        $antrian->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Permintaan cetak dikirim ke resepsionis.'
        ]);
    }
}
