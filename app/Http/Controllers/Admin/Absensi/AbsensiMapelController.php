<?php

namespace App\Http\Controllers\Admin\Absensi;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\AbsensiMapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiMapelController extends Controller
{
    /**
     * [MODIFIKASI v2]
     * Menampilkan SEMUA jadwal pelajaran di hari yang dipilih.
     * Menggunakan mapping hari yang independen dari locale server.
     */
    public function index(Request $request)
    {
        // Ambil tanggal dari request, jika tidak ada, gunakan hari ini
        $tanggalIni = $request->input('tanggal', Carbon::now()->toDateString());
        
        // ===================================================================
        // MODIFIKASI UTAMA: Cara mendapatkan nama hari yang lebih aman
        // ===================================================================
        
        // Carbon::dayOfWeek mengembalikan angka (0 untuk Minggu, 1 untuk Senin, dst)
        // Ini tidak bergantung pada bahasa server.
        $dayOfWeek = Carbon::parse($tanggalIni)->dayOfWeek; 
        
        $daftarHari = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        // $hariDipilih sekarang PASTI berisi nama hari dalam Bahasa Indonesia
        $hariDipilih = $daftarHari[$dayOfWeek];
        
        // ===================================================================
        // AKHIR MODIFIKASI
        // ===================================================================

        // Kueri ini sekarang dijamin mencari nama hari yang benar
        $jadwalMengajar = JadwalPelajaran::where('hari', $hariDipilih)
            ->with([
                'rombel:id,nama', 
                'ptk:id,nama' // Pastikan relasi 'ptk' ada
            ])
            ->orderBy('jam_mulai')
            ->get();
            
        // Cek absensi yang sudah terisi
        $jadwalIds = $jadwalMengajar->pluck('id');
        $absensiTerisi = AbsensiMapel::whereIn('jadwal_pelajaran_id', $jadwalIds)
            ->where('tanggal', $tanggalIni)
            ->select('jadwal_pelajaran_id')
            ->distinct()
            ->pluck('jadwal_pelajaran_id');
            
        return view('admin.absensi.mapel.index', compact('jadwalMengajar', 'absensiTerisi', 'tanggalIni'));
    }
    public function show(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_pelajaran,id',
            'tanggal' => 'required|date',
        ]);

        $jadwal = JadwalPelajaran::with('rombel')->findOrFail($request->jadwal_id);
        $rombel = $jadwal->rombel; 
        $tanggal = $request->tanggal;

        $anggotaRombelIds = json_decode($rombel->anggota_rombel, true);
        if (empty($anggotaRombelIds)) {
            return back()->with('error', 'Kelas ini tidak memiliki anggota siswa.');
        }
        $pesertaDidikIds = array_column($anggotaRombelIds, 'peserta_didik_id');
        $siswas = Siswa::whereIn('peserta_didik_id', $pesertaDidikIds)->orderBy('nama')->get();

        $absensiRecords = AbsensiMapel::where('jadwal_pelajaran_id', $jadwal->id)
            ->where('tanggal', $tanggal)
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        $formAction = route('admin.absensi.mapel.store');

        return view('admin.absensi.mapel.show', compact(
            'jadwal', 'rombel', 'siswas', 'tanggal', 'absensiRecords', 'formAction'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'absensi' => 'required|array',
            'tanggal' => 'required|date',
            'jadwal_id' => 'required|exists:jadwal_pelajaran,id',
        ]);

        $jadwalId = $request->jadwal_id;
        $tanggal = $request->tanggal;

        foreach ($request->absensi as $siswaId => $data) {
            if (!isset($data['status'])) {
                continue; 
            }

            AbsensiMapel::updateOrCreate(
                [
                    'jadwal_pelajaran_id' => $jadwalId,
                    'siswa_id' => $siswaId,
                    'tanggal' => $tanggal,
                ],
                [
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'dicatat_oleh_gtk_id' => null, 
                ]
            );
        }

        return redirect()->route('admin.absensi.mapel.index', ['tanggal' => $tanggal])
                         ->with('success', 'Absensi mapel berhasil disimpan.');
    }
}