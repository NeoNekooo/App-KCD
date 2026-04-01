<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AntrianTamu;
use Carbon\Carbon;

class AntrianDisplayController extends Controller
{
    /**
     * Halaman Utama TV KCD Display Layar (Admin Only)
     */
    public function index()
    {
        $instansi = \App\Models\Instansi::first();
        return view('admin.antrian.display', compact('instansi'));
    }

    /**
     * AJAX endpoint for pulling updates every X seconds
     * Returns:
     * - Antrian yang sedang dipanggil (status = 'dipanggil') 
     * - Daftar antrian yang masih menunggu (status = 'menunggu')
     * - Trigger triggerVoiceCall bila ada ID baru yang dipanggil.
     */
    public function getUpdates(Request $request)
    {
        $today = Carbon::today();
        
        // Yg sedang dipanggil saat ini ditaruh kiri
        $sedangDipanggil = AntrianTamu::with('tujuanPegawai.jabatanKcd')
            ->whereDate('created_at', $today)
            ->where('status', 'dipanggil')
            ->orderBy('waktu_panggilan', 'desc') // Yg terbaru dipanggil di atas
            ->get()
            ->map(function ($q) {
                return [
                    'id'               => $q->id,
                    'nomor_antrian'    => $q->nomor_antrian,
                    'nama'             => $q->nama,
                    'tujuan'           => $q->tujuanPegawai ? $q->tujuanPegawai->nama : 'Petugas',
                    'keperluan'        => $q->keperluan,
                    'nisn'             => $q->nisn,
                    'jabatan_pengunjung' => $q->jabatan_pengunjung,
                    'jumlah_panggilan' => $q->jumlah_panggilan
                ];
            });

        // Yg masih antre / menunggu (untuk list kanan)
        $daftarMenunggu = AntrianTamu::with('tujuanPegawai.jabatanKcd')
            ->whereDate('created_at', $today)
            ->where('status', 'menunggu')
            ->orderBy('id', 'asc') // First in first out
            ->get()
            ->map(function ($q) {
                return [
                    'id'               => $q->id,
                    'nomor_antrian'    => $q->nomor_antrian,
                    'nama'             => $q->nama,
                    'tujuan'           => $q->tujuanPegawai ? $q->tujuanPegawai->nama : 'Petugas',
                    'keperluan'        => $q->keperluan,
                    'nisn'             => $q->nisn,
                    'jabatan_pengunjung' => $q->jabatan_pengunjung
                ];
            });

        return response()->json([
            'dipanggil' => $sedangDipanggil,
            'menunggu'  => $daftarMenunggu
        ]);
    }
}
