<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AntrianTamu;
use Carbon\Carbon;

class AntrianDisplayController extends Controller
{
    /**
     * Halaman Utama TV KCD Display Layar per wilayah
     */
    public function index($wilayah)
    {
        $instansi = \App\Models\Instansi::all()->first(function($ins) use ($wilayah) {
            return $ins->cadisdik_id === $wilayah || 
                   ($ins->cadisdik && $ins->cadisdik->short_slug === $wilayah);
        });

        if (!$instansi) abort(404);

        return view('admin.antrian.display', compact('instansi'));
    }

    /**
     * AJAX endpoint for pulling updates every X seconds (Per Wilayah)
     */
    public function getUpdates(Request $request, $wilayah)
    {
        $today = Carbon::today();
        
        $instansi = \App\Models\Instansi::all()->first(function($ins) use ($wilayah) {
            return $ins->cadisdik_id === $wilayah || 
                   ($ins->cadisdik && $ins->cadisdik->short_slug === $wilayah);
        });

        if (!$instansi) return response()->json(['error' => 'Not Found'], 404);
        
        // Yg sedang dipanggil saat ini ditaruh kiri
        $sedangDipanggil = AntrianTamu::withoutGlobalScopes()
            ->with('tujuanPegawai.jabatanKcd')
            ->where('instansi_id', $instansi->id)
            ->whereDate('created_at', $today)
            ->where('status', 'dipanggil')
            ->orderBy('waktu_panggilan', 'desc')
            ->get()
            ->map(function ($q) {
                return [
                    'id'               => $q->id,
                    'nomor_antrian'    => $q->nomor_antrian,
                    'nama'             => $q->nama,
                    'tujuan'           => $q->tujuanPegawai ? $q->tujuanPegawai->nama : 'Petugas',
                    'keperluan'        => $q->keperluan,
                    'npsn'             => $q->npsn,
                    'jabatan_pengunjung' => $q->jabatan_pengunjung,
                    'jumlah_panggilan' => $q->jumlah_panggilan
                ];
            });

        // Yg masih antre / menunggu (untuk list kanan)
        $daftarMenunggu = AntrianTamu::withoutGlobalScopes()
            ->with('tujuanPegawai.jabatanKcd')
            ->where('instansi_id', $instansi->id)
            ->whereDate('created_at', $today)
            ->where('status', 'menunggu')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($q) {
                return [
                    'id'               => $q->id,
                    'nomor_antrian'    => $q->nomor_antrian,
                    'nama'             => $q->nama,
                    'tujuan'           => $q->tujuanPegawai ? $q->tujuanPegawai->nama : 'Petugas',
                    'keperluan'        => $q->keperluan,
                    'npsn'             => $q->npsn,
                    'jabatan_pengunjung' => $q->jabatan_pengunjung
                ];
            });

        // Antrian yang minta dicetak oleh tamu dari HP
        $toPrint = AntrianTamu::withoutGlobalScopes()
            ->where('instansi_id', $instansi->id)
            ->whereDate('created_at', $today)
            ->where('print_requested', true)
            ->get();

        return response()->json([
            'dipanggil' => $sedangDipanggil,
            'menunggu'  => $daftarMenunggu,
            'to_print'  => $toPrint
        ]);
    }

    /**
     * Tampilan Thermal Ticket untuk Iframe Print
     */
    public function ticketThermal($id)
    {
        $antrian = AntrianTamu::withoutGlobalScopes()->with('tujuanPegawai')->findOrFail($id);
        $instansi = \App\Models\Instansi::find($antrian->instansi_id) ?? \App\Models\Instansi::first();
        return view('admin.antrian.ticket_thermal', compact('antrian', 'instansi'));
    }

    /**
     * Tandai tiket sudah berhasil dicetak oleh TV
     */
    public function markAsPrinted($id)
    {
        $antrian = AntrianTamu::findOrFail($id);
        $antrian->print_requested = false;
        $antrian->save();

        return response()->json(['status' => 'success']);
    }
}
