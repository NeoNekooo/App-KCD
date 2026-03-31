<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AntrianTamu;
use App\Models\PegawaiKcd;
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

        return view('guest.buku-tamu.index', compact('pegawais', 'instansi'));
    }

    /**
     * Menyimpan data form tamu dan membuat Nomor Antrian
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'              => 'required|string|max:255',
            'nik'               => 'nullable|string|max:20',
            'asal_instansi'     => 'required|string|max:255',
            'keperluan'         => 'required|string|max:500',
            'tujuan_pegawai_id' => 'required|exists:pegawai_kcds,id',
        ]);

        // Logic Auto Generate Nomer Antrian (e.g., A-001) reset per hari
        $today = Carbon::today();
        
        $lastAntrian = AntrianTamu::whereDate('created_at', $today)
                                  ->orderBy('id', 'desc')
                                  ->first();

        // Hitung urutan hari ini
        $urutan = 1;
        if ($lastAntrian && $lastAntrian->nomor_antrian) {
            // Asumsi format: A-001
            $parts = explode('-', $lastAntrian->nomor_antrian);
            if(count($parts) == 2) {
                $urutan = intval($parts[1]) + 1;
            } else {
                // Fallback kalau format aneh
                $urutan = AntrianTamu::whereDate('created_at', $today)->count() + 1;
            }
        }

        // Format nomor: A- (ditambah padding 0 tiga kali)
        $nomorBaru = 'A-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);

        // Simpan Data
        $antrian = AntrianTamu::create([
            'nomor_antrian'     => $nomorBaru,
            'nama'              => $request->nama,
            'nik'               => $request->nik,
            'asal_instansi'     => $request->asal_instansi,
            'keperluan'         => $request->keperluan,
            'tujuan_pegawai_id' => $request->tujuan_pegawai_id,
            'status'            => 'menunggu', 
            'jumlah_panggilan'  => 0,
        ]);

        // Simpan ID tiket di session agar tamu bisa melihat tiketnya usai redirect
        return redirect()->route('guest.buku-tamu')->with([
            'tiket_success' => true,
            'tiket_nomor'   => $antrian->nomor_antrian,
            'tiket_nama'    => $antrian->nama,
            'tiket_waktu'   => $antrian->created_at->format('d M Y, H:i')
        ]);
    }
}
