<?php

namespace App\Http\Controllers\Admin\Absensi;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGtk;
use App\Models\JadwalPelajaran;
use App\Models\Gtk;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AbsensiGtkController extends Controller
{
    /**
     * Menampilkan halaman Kios Absensi QR Code untuk Guru.
     */
    public function showScanner()
    {
        $namaHariIni = Carbon::now()->isoFormat('dddd');
        $jadwalHariIni = DB::table('pengaturan_absensi')->where('hari', $namaHariIni)->first();
        return view('admin.absensi.gtk.scanner', compact('jadwalHariIni'));
    }

    // ===================================================================
    // METODE ABSENSI MANUAL (LOGIKA DIPERBARUI)
    // ===================================================================

    /**
     * Menampilkan formulir absensi manual HANYA UNTUK GTK YANG PUNYA JADWAL.
     */
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::now()->toDateString());
        $namaHari = Carbon::parse($tanggal)->isoFormat('dddd');

        // 1. Ambil ID guru (dari kolom 'ptk_id' di jadwal) yang punya jadwal di hari ini
        $ptkIdsWajibHadir = JadwalPelajaran::where('hari', $namaHari)
            ->distinct()
            ->pluck('ptk_id');

        // 2. Ambil data GTK HANYA yang wajib hadir
        // !! PERBAIKAN !!: Menggunakan whereIn('id', ...) karena ptk_id di jadwal merujuk ke gtks.id
        $gtks = Gtk::whereIn('id', $ptkIdsWajibHadir) // <-- DIUBAH DARI 'ptk_id'
            ->orderBy('nama')
            ->get();

        // 3. Ambil absensi yg sudah ada di tanggal tsb (menggunakan gtk_id dari $gtks)
        $absensiRecords = AbsensiGtk::where('tanggal', $tanggal)
            ->whereIn('gtk_id', $gtks->pluck('id')) // 'id' dari $gtks adalah 'gtk_id'
            ->get()
            ->keyBy('gtk_id'); 

        $statusOptions = ['Hadir', 'Sakit', 'Izin', 'Cuti', 'Dinas Luar', 'Alfa']; 

        return view('admin.absensi.gtk.index', compact(
            'gtks', 
            'absensiRecords', 
            'tanggal',
            'namaHari', 
            'statusOptions'
        ));
    }

    /**
     * Menyimpan data dari formulir absensi manual.
     * LOGIKA BARU: Otomatis set 'Alfa' jika guru punya jadwal tapi tidak dipilih.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $tanggal = $request->tanggal;
        $namaHari = Carbon::parse($tanggal)->isoFormat('dddd');
        $adminId = Auth::id() ?? null;

        // 1. Ambil SEMUA ptk_id yang seharusnya hadir hari itu (yang merujuk ke gtks.id)
        $ptkIdsWajibHadir = JadwalPelajaran::where('hari', $namaHari)
            ->distinct()
            ->pluck('ptk_id');
            
        // 2. Dapatkan data GTK (termasuk 'id' (gtk_id) dan 'ptk_id')
        // !! PERBAIKAN !!: Ambil GTK berdasarkan 'id' (sesuai ptk_id dari jadwal)
        $gtkWajibHadir = Gtk::whereIn('id', $ptkIdsWajibHadir) // <-- DIUBAH DARI 'ptk_id'
            ->select('id', 'ptk_id') // 'id' adalah gtk_id
            ->get();

        // 3. Ambil data input dari form
        $inputAbsensi = $request->input('absensi', []);

        // 4. Loop melalui SEMUA guru yang wajib hadir (GTK Models)
        foreach ($gtkWajibHadir as $guru) {
            
            $gtkId = $guru->id; // Ini adalah 'gtk_id' untuk tabel absensi

            // Cek data dari form
            $data = $inputAbsensi[$gtkId] ?? null;
            $status = $data['status'] ?? null; // Jika tidak ada input, status = null
            $keterangan = $data['keterangan'] ?? null;

            // 5. LOGIKA UTAMA: Tentukan status
            if (is_null($status)) {
                $status = 'Alfa';
                $keterangan = $keterangan ?? 'Tidak ada kabar (Otomatis)';
            }

            // 6. Tentukan data yg akan disimpan
            $saveData = [
                'status' => $status,
                'keterangan' => $keterangan,
                // 'dicatat_oleh_id' => $adminId, // Aktifkan jika Anda punya kolom ini
            ];

            // 7. Jika status BUKAN 'Hadir', hapus jam scan (konsistensi data)
            if ($status != 'Hadir') {
                $saveData['jam_masuk'] = null;
                $saveData['jam_pulang'] = null;
                $saveData['status_kehadiran'] = null;
            }

            // 8. Simpan ke database menggunakan 'gtk_id'
            AbsensiGtk::updateOrCreate(
                [
                    'gtk_id' => $gtkId,
                    'tanggal' => $tanggal,
                ],
                $saveData
            );
        }

        return back()->with('success', 'Data absensi GTK berhasil disimpan. Guru yang tidak dipilih otomatis ditandai Alfa.');
    }

    // ===================================================================
    // METODE LAPORAN (Tidak perlu diubah dari versi sebelumnya)
    // ===================================================================

    /**
     * Menampilkan halaman laporan absensi GTK.
     */
    public function laporan(Request $request)
    {
        $gtks = Gtk::orderBy('nama')->select('id', 'nama')->get();
        $query = AbsensiGtk::query()->with('gtk:id,nama');

        if ($request->filled('gtk_id')) {
            $query->where('gtk_id', $request->gtk_id);
        }

        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', Carbon::now()->endOfMonth()->toDateString());

        $query->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        $laporan = $query->orderBy('tanggal', 'desc')
                          ->orderBy('gtk_id', 'asc')
                          ->paginate(25)
                          ->withQueryString(); 

        return view('admin.absensi.gtk.laporan', compact(
            'laporan', 
            'gtks',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }

    // ===================================================================
    // METODE HANDLE SCAN (KIOSK) (Logika Diperbarui)
    // ===================================================================

    /**
     * Menangani logika saat QR Code Guru dipindai.
     */
    public function handleScan(Request $request)
    {
        $token = $request->input('token');
        $waktuScan = Carbon::now();
        $namaHariIni = $waktuScan->isoFormat('dddd');
        $tanggalScan = $waktuScan->toDateString();

        DB::beginTransaction();
        try {
            // $guru adalah Model Gtk
            $guru = Gtk::where('qr_token', $token)->first(); 
            if (!$guru) {
                return response()->json(['success' => false, 'message' => 'QR Code Guru tidak valid!'], 404);
            }

            // Cek absensi pakai 'gtk_id' (yaitu $guru->id)
            $absensiHariIni = AbsensiGtk::where('gtk_id', $guru->id) 
                ->where('tanggal', $tanggalScan)
                ->lockForUpdate()
                ->first();

            // [VALIDASI] Cek jika sudah diabsen manual
            if ($absensiHariIni && $absensiHariIni->status != 'Hadir' && !is_null($absensiHariIni->status)) {
                DB::commit(); 
                return response()->json([
                    'success' => false, 
                    'message' => "Scan ditolak. Status Anda hari ini: '{$absensiHariIni->status}'."
                ], 409); 
            }

            // 4. TENTUKAN JAM KERJA DINAMIS GURU
            // !! PERBAIKAN !!: Menggunakan 'id' dari model $guru (karena ptk_id di jadwal = gtks.id)
            $jadwalKerja = JadwalPelajaran::where('ptk_id', $guru->id) // <-- DIUBAH DARI $guru->ptk_id
                ->where('hari', $namaHariIni)
                ->selectRaw('MIN(jam_mulai) as jam_masuk_seharusnya, MAX(jam_selesai) as jam_pulang_seharusnya')
                ->first();

            // 5. Validasi jika guru tidak punya jadwal
            if (!$jadwalKerja || !$jadwalKerja->jam_masuk_seharusnya) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki jadwal mengajar hari ini.'], 400);
            }

            $batasMasuk = Carbon::parse($tanggalScan . ' ' . $jadwalKerja->jam_masuk_seharusnya);
            $batasPulang = Carbon::parse($tanggalScan . ' ' . $jadwalKerja->jam_pulang_seharusnya);
            
            // 6. Tentukan alur: MASUK atau PULANG
            if ($absensiHariIni && $absensiHariIni->jam_masuk) {
                $response = $this->prosesAbsensiPulang($absensiHariIni, $waktuScan, $batasPulang, $guru);
            } else {
                $response = $this->prosesAbsensiMasuk($absensiHariIni, $guru, $waktuScan, $batasMasuk, $tanggalScan);
            }
            
            DB::commit();
            return $response;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memproses scan GTK: '. $e->getMessage() . ' | File: ' . $e->getFile() . ' | Baris: ' . $e->getLine());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.'], 500);
        }
    }
    
    // ===================================================================
    // PRIVATE HELPER METHODS (Untuk KiosK)
    // ===================================================================

    private function prosesAbsensiMasuk($absensiHariIni, $guru, $waktuScan, $batasMasuk, $tanggalScan)
    {
        $statusKehadiran = 'Tepat Waktu';
        $menitTerlambat = 0;
        $detikTerlambat = 0;

        if ($waktuScan->isAfter($batasMasuk)) {
            $statusKehadiran = 'Terlambat';
            $totalDetikTerlambat = $waktuScan->diffInSeconds($batasMasuk);
            if ($totalDetikTerlambat === 0) $totalDetikTerlambat = 1;
            $menitTerlambat = floor($totalDetikTerlambat / 60);
            $detikTerlambat = $totalDetikTerlambat % 60;
        }
        
        AbsensiGtk::updateOrCreate(
            ['gtk_id' => $guru->id, 'tanggal' => $tanggalScan], // 'id' dari model $guru adalah 'gtk_id'
            [
                'status' => 'Hadir', 
                'jam_masuk' => $waktuScan->toTimeString(),
                'status_kehadiran' => $statusKehadiran,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Selamat Datang, {$guru->nama}!",
            'status' => $statusKehadiran,
            'gtk' => $guru, 
            'menit_terlambat' => (int) $menitTerlambat,
            'detik_terlambat' => (int) $detikTerlambat
        ]);
    }

    private function prosesAbsensiPulang($absensi, $waktuScan, $batasPulang, $guru)
    {
        if ($absensi->jam_pulang) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absen pulang sebelumnya.'], 409);
        }

        $absensi->jam_pulang = $waktuScan->toTimeString();
        $absensi->save();

        return response()->json([
            'success' => true, 
            'message' => "Sampai Jumpa, {$guru->nama}!", 
            'status' => 'Pulang', 
            'gtk' => $guru
        ]);
    }

    // ===================================================================
    // API METHODS UNTUK LIVE DASHBOARD (KiosK)
    // ===================================================================

    public function getTodaysScans()
    {
        $today = Carbon::now()->toDateString();
        $absensiHariIni = AbsensiGtk::where('tanggal', $today)
            ->whereNotNull('jam_masuk')
            ->with('gtk:id,nama,foto') 
            ->orderBy('updated_at', 'desc')
            ->get();
        return response()->json($absensiHariIni);
    }

    public function getRecentScans()
    {
        $recentScans = AbsensiGtk::where('updated_at', '>=', now()->subSeconds(6))
            ->with('gtk:id,nama,foto') 
            ->orderBy('updated_at', 'asc')
            ->get();
        return response()->json($recentScans);
    }

    public function getUnscannedData(Request $request)
    {
        $today = Carbon::now();
        $namaHariIni = $today->isoFormat('dddd');
        $tanggalScan = $today->toDateString();

        $scannedGtkIds = AbsensiGtk::where('tanggal', $tanggalScan)
            ->whereNotNull('jam_masuk')
            ->pluck('gtk_id');

        // Ambil 'ptk_id' dari jadwal (yang merujuk ke gtks.id)
        $ptkIdsWajibHadir = JadwalPelajaran::where('hari', $namaHariIni)
            ->distinct()
            ->pluck('ptk_id'); 

        // !! PERBAIKAN !!: Cari Gtk berdasarkan 'id' (sesuai ptk_id dari jadwal)
        $unscannedGurus = Gtk::whereIn('id', $ptkIdsWajibHadir) // <-- DIUBAH DARI 'ptk_id'
            ->whereNotIn('id', $scannedGtkIds) // Filter yg belum scan pakai 'id' (gtk_id)
            ->orderBy('nama')
            ->select('id', 'nama', 'foto')
            ->get();

        return response()->json([
            'unscanned_students' => $unscannedGurus, 
            'rombels' => [] 
        ]);
    }
}