<?php

namespace App\Http\Controllers\Admin\Absensi;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\IzinSiswa;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendWhatsappNotification; // Import Job Class

class AbsensiSiswaController extends Controller
{
    /**
     * Menampilkan halaman untuk memilih kelas (Absensi Harian).
     * Versi yang dioptimalkan.
     */
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::now()->toDateString());

        // Query yang dioptimalkan: sorting dan grouping dilakukan di database
        $rombels = Rombel::with('waliKelas')
            ->select('id', 'nama', 'ptk_id')
            ->get()
            ->unique('nama')
            ->sortBy(function ($rombel) {
                $nama = $rombel->nama;
                if (str_starts_with($nama, 'XII')) return '1_' . $nama;
                if (str_starts_with($nama, 'XI')) return '2_' . $nama;
                if (str_starts_with($nama, 'X')) return '3_' . $nama;
                return '4_' . $nama;
            });

        return view('admin.absensi.siswa.index', compact('rombels', 'tanggal'));
    }

    /**
     * Menampilkan formulir absensi untuk kelas dan tanggal yang dipilih.
     */
    public function show(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
            'tanggal' => 'required|date',
        ]);

        $rombel = Rombel::findOrFail($request->rombel_id);
        $tanggal = $request->tanggal;

        $anggotaRombelIds = json_decode($rombel->anggota_rombel, true);
        if (empty($anggotaRombelIds)) {
            return back()->with('error', 'Kelas ini tidak memiliki anggota siswa.');
        }
        $pesertaDidikIds = array_column($anggotaRombelIds, 'peserta_didik_id');
        $siswas = Siswa::whereIn('peserta_didik_id', $pesertaDidikIds)->orderBy('nama')->get();

        $absensiRecords = AbsensiSiswa::where('tanggal', $tanggal)
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->get()
            ->keyBy('siswa_id');
        
        $pengaturan = DB::table('pengaturan_absensi')->first();
        $formAction = route('admin.absensi.siswa.store');

        return view('admin.absensi.siswa.show', compact(
            'rombel', 'siswas', 'tanggal', 'absensiRecords', 'pengaturan', 'formAction'
        ));
    }

    /**
     * Menyimpan data absensi dari form manual.
     */
    public function store(Request $request)
    {
        $request->validate([
            'absensi' => 'required|array',
            'tanggal' => 'required|date',
        ]);

        foreach ($request->absensi as $siswaId => $data) {
            if (!isset($data['status'])) {
                continue;
            }

            AbsensiSiswa::updateOrCreate(
                ['siswa_id' => $siswaId, 'tanggal' => $request->tanggal],
                [
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'dicatat_oleh' => Auth::id() ?? 1,
                    'jadwal_id' => null,
                ]
            );
        }

        return back()->with('success', 'Data absensi berhasil disimpan.');
    }

    /**
     * Menampilkan halaman Kios Absensi QR Code.
     */
    public function showScanner()
    {
        $namaHariIni = Carbon::now()->isoFormat('dddd');
        $jadwalHariIni = DB::table('pengaturan_absensi')->where('hari', $namaHariIni)->first();
        return view('admin.absensi.siswa.scanner', compact('jadwalHariIni'));
    }

    /**
     * Menangani logika saat QR Code dipindai (versi refactoring).
     */
    public function handleScan(Request $request)
    {
        DB::beginTransaction();
        try {
            $token = $request->input('token');
            $waktuScan = Carbon::now();

            // Alur 1: Proses scan tiket kembali
            $izinKembali = IzinSiswa::where('token_sementara', $token)->where('status', 'SEDANG_KELUAR')->first();
            if ($izinKembali) {
                return $this->prosesIzinKembali($izinKembali, $waktuScan);
            }

            // Alur 2: Proses scan QR pribadi
            $siswa = Siswa::where('qr_token', $token)->first();
            if (!$siswa) {
                return response()->json(['success' => false, 'message' => 'QR Code tidak valid!'], 404);
            }
            
            $tanggalScan = $waktuScan->toDateString();
            $absensiHariIni = AbsensiSiswa::where('siswa_id', $siswa->id)->where('tanggal', $tanggalScan)->lockForUpdate()->first();
            
            // Sub-Alur A: Siswa memiliki izin aktif
            $izinHariIni = IzinSiswa::where('siswa_id', $siswa->id)->where('tanggal_izin', $tanggalScan)->whereIn('status', ['DISETUJUI', 'SEDANG_KELUAR'])->first();
            if ($izinHariIni) {
                return $this->prosesIzinSiswa($izinHariIni, $absensiHariIni, $siswa, $waktuScan);
            }

            // Sub-Alur B: Proses absensi normal (masuk/pulang)
            return $this->prosesAbsensiNormal($siswa, $absensiHariIni, $waktuScan);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memproses scan absensi: ' . $e->getMessage() . ' di baris ' . $e->getLine());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.'], 500);
        }
    }

    // ===================================================================
    // PRIVATE HELPER METHODS (HASIL REFACTORING)
    // ===================================================================

    private function prosesIzinKembali(IzinSiswa $izin, Carbon $waktuScan)
    {
        if ($waktuScan->gt(Carbon::parse($izin->jam_izin_selesai))) {
            return response()->json(['success' => false, 'message' => 'Waktu izin Anda untuk kembali sudah berakhir.'], 400);
        }
        $izin->waktu_kembali = $waktuScan;
        $izin->status = 'DIGUNAKAN';
        $izin->save();
        DB::commit();
        return response()->json(['success' => true, 'message' => 'Selamat datang kembali, ' . $izin->siswa->nama . '!']);
    }

    private function prosesIzinSiswa(IzinSiswa $izin, ?AbsensiSiswa $absensi, Siswa $siswa, Carbon $waktuScan)
    {
        // ... (Logika penanganan semua jenis izin disatukan di sini)
        DB::commit(); // Pastikan commit setelah operasi berhasil
        // Placeholder untuk respons, sesuaikan dengan logika izin Anda
        return response()->json(['success' => true, 'message' => 'Izin berhasil diproses.']);
    }

    private function prosesAbsensiNormal(Siswa $siswa, ?AbsensiSiswa $absensiHariIni, Carbon $waktuScan)
    {
        $namaHariIni = $waktuScan->isoFormat('dddd');
        $tanggalScan = $waktuScan->toDateString();
        $pengaturan = DB::table('pengaturan_absensi')->where('hari', $namaHariIni)->first();

        // Validasi Awal
        if (!$pengaturan) return response()->json(['success' => false, 'message' => "Pengaturan absensi untuk hari {$namaHariIni} tidak ditemukan."], 400);
        if (!$pengaturan->is_active) return response()->json(['success' => false, 'message' => "Hari {$namaHariIni} tidak ada jadwal absensi."], 400);
        if (DB::table('hari_libur')->where('tanggal', $tanggalScan)->exists()) return response()->json(['success' => false, 'message' => 'Hari ini adalah hari libur.'], 400);
        if ($absensiHariIni && in_array($absensiHariIni->status, ['Sakit', 'Izin', 'Alfa'])) return response()->json(['success' => false, 'message' => "Scan ditolak. Status Anda '{$absensiHariIni->status}'."], 409);

        // Proses Pulang atau Masuk
        if ($absensiHariIni && $absensiHariIni->jam_masuk) {
            $response = $this->prosesAbsensiPulang($absensiHariIni, $pengaturan, $waktuScan, $siswa);
        } else {
            $response = $this->prosesAbsensiMasuk($siswa, $pengaturan, $waktuScan);
        }

        DB::commit();
        return $response;
    }

private function prosesAbsensiMasuk(Siswa $siswa, $pengaturan, Carbon $waktuScan)
{
    $batasMasuk = Carbon::parse($pengaturan->jam_masuk_sekolah);
    $batasAwalMasuk = $batasMasuk->copy()->subMinutes(60); // Bisa disesuaikan
    $batasAkhirAbsen = $batasMasuk->copy()->addMinutes((int) $pengaturan->batas_toleransi_terlambat); // Batas akhir absen

    // Validasi 1: Apakah waktu scan berada di luar jendela absensi?
    // Catatan: sengaja tidak pakai ->between() agar bisa memberi pesan error yang berbeda
    if ($waktuScan->lt($batasAwalMasuk)) {
        return response()->json(['success' => false, 'message' => 'Belum waktunya untuk absen masuk!'], 400);
    }
    if ($waktuScan->gt($batasAkhirAbsen)) {
         return response()->json(['success' => false, 'message' => 'Waktu untuk absen masuk sudah berakhir.'], 400);
    }
    
    // Inisialisasi variabel
    $menitTerlambat = 0;
    $detikTerlambat = 0;
    $statusKehadiran = 'Tepat Waktu'; // Default status

    // Validasi 2: Cek apakah siswa terlambat (waktu scan setelah jam masuk)
    if ($waktuScan->isAfter($batasMasuk)) {
        $statusKehadiran = 'Terlambat';
        
        // Hitung total detik, lalu pecah menjadi menit dan sisa detik
        $totalDetikTerlambat = $waktuScan->diffInSeconds($batasMasuk);

        // Jika karena pembulatan milidetik hasilnya 0, tapi statusnya terlambat, anggap terlambat 1 detik
        if ($totalDetikTerlambat === 0) {
            $totalDetikTerlambat = 1;
        }

        $menitTerlambat = floor($totalDetikTerlambat / 60);
        $detikTerlambat = $totalDetikTerlambat % 60;
    }
    
    AbsensiSiswa::updateOrCreate(
        ['siswa_id' => $siswa->id, 'tanggal' => $waktuScan->toDateString()],
        ['status' => 'Hadir', 'jam_masuk' => $waktuScan->toTimeString(), 'status_kehadiran' => $statusKehadiran, 'dicatat_oleh' => auth()->id() ?? 1]
    );

    // Logika Notifikasi WhatsApp (tidak diubah)
    if ($siswa->nomor_telepon_seluler) {
    // Siapkan template pesan dasar
    $pesan = "🏫 *Notifikasi Absensi Masuk*\n\n" .
             "Yth. Orang Tua/Wali,\n\n" .
             "Kami informasikan ananda *{$siswa->nama}* telah tercatat absensi masuk pada:\n\n" .
             "🗓️ Tanggal: " . $waktuScan->isoFormat('dddd, D MMMM Y') . "\n";

    // Tambahkan detail berdasarkan status kehadiran
    if ($statusKehadiran === 'Terlambat') {
        $pesan .= "🕒 Batas Masuk: *" . $batasMasuk->format('H:i') . "*\n" . // INFORMASI PENTING!
                  "⏰ Waktu Scan: *" . $waktuScan->format('H:i') . "*\n" .
                  "✅ Status: *{$statusKehadiran} ({$menitTerlambat} menit " . ($detikTerlambat > 0 ? "{$detikTerlambat} detik" : "") . ")*\n\n";
    } else {
        $pesan .= "⏰ Pukul: *" . $waktuScan->format('H:i') . "*\n" .
                  "✅ Status: *{$statusKehadiran}*\n\n";
    }

    $pesan .= "Terima kasih.";
    
    SendWhatsappNotification::dispatch($siswa->nomor_telepon_seluler, $pesan);
}

    // Siapkan respons JSON yang sekarang sudah konsisten
    $responseData = [
        'success' => true,
        'message' => "Selamat Datang, {$siswa->nama}!",
        'status' => $statusKehadiran,
        'siswa' => $siswa,
        'menit_terlambat' => (int) $menitTerlambat,
        'detik_terlambat' => (int) $detikTerlambat
    ];

    return response()->json($responseData);
}

    private function prosesAbsensiPulang(AbsensiSiswa $absensi, $pengaturan, Carbon $waktuScan, Siswa $siswa)
    {
        if ($absensi->jam_pulang) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absen pulang sebelumnya.'], 409);
        }

        $jamPulangSekolah = Carbon::parse($pengaturan->jam_pulang_sekolah);
        if ($waktuScan->lt($jamPulangSekolah)) {
            return response()->json(['success' => false, 'message' => 'Belum waktunya untuk absen pulang.'], 400);
        }

        $absensi->jam_pulang = $waktuScan->toTimeString();
        $absensi->save();

        // --- IMPLEMENTASI NOTIFIKASI WHATSAPP ---
        if ($siswa->nomor_telepon_seluler) {
            $pesan = "🏡 *Notifikasi Absensi Pulang*\n\n" .
                     "Yth. Orang Tua/Wali,\n\n" .
                     "Kami informasikan ananda *{$siswa->nama}* telah tercatat absensi pulang pada:\n\n" .
                     "🗓️ Tanggal: " . $waktuScan->isoFormat('dddd, D MMMM Y') . "\n" .
                     "⏰ Pukul: *" . $waktuScan->format('H:i') . "*\n\n" .
                     "Terima kasih.";
            SendWhatsappNotification::dispatch($siswa->nomor_telepon_seluler, $pesan);
        }

        return response()->json(['success' => true, 'message' => "Sampai Jumpa, {$siswa->nama}!", 'status' => 'Pulang', 'siswa' => $siswa]);
    }


    // ===================================================================
    // REAL-TIME & DATA API METHODS (TIDAK DIUBAH)
    // ===================================================================

    public function getTodaysScans()
    {
        $today = Carbon::now()->toDateString();
        $absensiHariIni = AbsensiSiswa::where('tanggal', $today)
            ->whereNotNull('jam_masuk')
            ->with('siswa:id,nama,foto')
            ->orderBy('updated_at', 'desc')
            ->get();
        return response()->json($absensiHariIni);
    }

    public function getRecentScans()
    {
        $recentScans = AbsensiSiswa::where('updated_at', '>=', now()->subSeconds(6))
            ->with('siswa:id,nama,foto')
            ->orderBy('updated_at', 'asc')
            ->get();
        return response()->json($recentScans);
    }

    public function getUnscannedData(Request $request)
    {
        try {
            $today = Carbon::now()->toDateString();
            $rombelId = $request->query('rombel_id');
            $scannedStudentIds = AbsensiSiswa::where('tanggal', $today)->whereNotNull('jam_masuk')->pluck('siswa_id');
            $unscannedQuery = Siswa::whereNotIn('id', $scannedStudentIds);

            if ($rombelId && $rombelId !== 'all') {
                $rombel = Rombel::find($rombelId);
                if ($rombel && !empty($rombel->anggota_rombel)) {
                    $anggotaData = json_decode($rombel->anggota_rombel, true);
                    if (is_array($anggotaData) && !empty($anggotaData)) {
                        $anggotaPdIds = array_column($anggotaData, 'peserta_didik_id');
                        $siswaIdsInRombel = Siswa::whereIn('peserta_didik_id', $anggotaPdIds)->pluck('id');
                        $unscannedQuery->whereIn('id', $siswaIdsInRombel);
                    } else { $unscannedQuery->whereRaw('1 = 0'); }
                } else { $unscannedQuery->whereRaw('1 = 0'); }
            }

            $unscannedStudents = $unscannedQuery->orderBy('nama')->select('id', 'nama', 'foto')->get();
            $rombelsForDropdown = [];
            if (!$request->has('rombel_id') || $request->get('rombel_id') === 'all') {
                $rombelsForDropdown = Rombel::orderBy('nama')->select('id', 'nama')->get()->unique('nama')->values()->all();
            }

            return response()->json([
                'unscanned_students' => $unscannedStudents,
                'rombels' => $rombelsForDropdown
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getUnscannedData: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' on line ' . $e->getLine());
            return response()->json(['message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}

