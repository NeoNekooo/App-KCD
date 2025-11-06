<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\JadwalPelajaran;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class GuruAbsensiController extends Controller
{
    /**
     * Menampilkan jadwal mengajar guru untuk hari ini.
     */
    public function index()
    {
        $pengguna = Auth::user();

        // Pastikan pengguna yang login terhubung dengan data GTK
        if (!$pengguna->ptk_id) {
            return back()->with('error', 'Akun Anda tidak terhubung dengan data GTK.');
        }

        // Tentukan hari ini dalam Bahasa Indonesia
        $namaHariIni = \Carbon\Carbon::now()->isoFormat('dddd');

        // Ambil semua jadwal mengajar guru yang login pada hari ini
        $jadwalHariIni = JadwalPelajaran::where('ptk_id', $pengguna->ptk_id)
            ->where('hari', $namaHariIni)
            ->with('rombel') // Eager load relasi rombel
            ->orderBy('jam_mulai')
            ->get();

        return view('guru.absensi.index', compact('jadwalHariIni'));
    }

    /**
     * Menampilkan form absensi untuk jadwal yang dipilih.
     */
    public function show(JadwalPelajaran $jadwal)
    {
        // Ambil data rombel dari jadwal
        $rombel = $jadwal->rombel;
        $tanggal = now()->format('Y-m-d');

        // Ambil daftar siswa dari rombel tersebut
        $siswas = Siswa::where('rombongan_belajar_id', $rombel->rombongan_belajar_id)
                      ->orderBy('nama', 'asc')
                      ->get();

        // Cek data absensi yang mungkin sudah ada untuk hari ini & jadwal ini
        $absensiRecords = AbsensiSiswa::where('tanggal', $tanggal)
                                      ->where('jadwal_id', $jadwal->id)
                                      ->get()
                                      ->keyBy('siswa_id');
        
        // KITA BISA MENGGUNAKAN KEMBALI VIEW DARI FITUR SEBELUMNYA!
        // Ini praktik yang bagus untuk menghindari duplikasi kode.
        return view('admin.absensi.siswa.show', compact('rombel', 'siswas', 'tanggal', 'absensiRecords', 'jadwal'));
    }

    /**
     * Menyimpan data absensi per jam pelajaran.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jadwal_id' => 'required|exists:jadwal_pelajaran,id',
            'absensi' => 'required|array',
            'absensi.*.status' => 'required|in:Hadir,Sakit,Izin,Alfa',
        ]);
        
        foreach ($request->absensi as $siswaId => $data) {
            AbsensiSiswa::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'tanggal' => $request->tanggal,
                    'jadwal_id' => $request->jadwal_id,
                ],
                [
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'dicatat_oleh' => Auth::id(),
                ]
            );
        }

        return Redirect::route('guru.absensi.show', $request->jadwal_id)
                       ->with('success', 'Absensi berhasil disimpan.');
    }
}
