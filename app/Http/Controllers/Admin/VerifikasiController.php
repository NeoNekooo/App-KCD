<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSekolah;
use App\Models\TipeSurat;
use App\Models\TugasPegawaiKcd;
use App\Models\NomorSuratSetting;
use App\Http\Controllers\Admin\Administrasi\NomorSuratSettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class VerifikasiController extends Controller
{
    /**
     * 1. HALAMAN UTAMA (INDEX)
     */
        public function index(Request $request)
        {
            $user = Auth::user();
            $title = 'Daftar Pengajuan Masuk';
            $roleUser = strtolower($user->role);
            $isKasubag = $roleUser === 'kasubag';
            $isKepala  = $roleUser === 'kepala';
    
            $query = PengajuanSekolah::query();
    
            // --- A. FILTER KATEGORI (TUGAS KEPEGAWAIAN) ---
            $kategoriTarget = $request->kategori;
            if ($roleUser === 'kepegawaian' && $user->pegawai_kcd_id) {
                $penugasan = TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)
                    ->where('is_active', 1)
                    ->first();
    
                if ($penugasan && !empty($penugasan->kategori_layanan)) {
                    $masterKeys = ['umum', 'all', 'semua-layanan', 'koordinator'];
                    if (!in_array(strtolower($penugasan->kategori_layanan), $masterKeys)) {
                        $kategoriTarget = $penugasan->kategori_layanan;
                        $request->merge(['kategori' => $kategoriTarget]);
                    }
                }
            }
    
            // --- B. Tentukan Status yang Diizinkan Berdasarkan Role User ---
            $allowedStatuses = [];
            if ($roleUser === 'kepegawaian') {
                $allowedStatuses = ['Proses', 'Atur Syarat', 'Lengkapi Berkas', 'Verifikasi Berkas', 'Perlu Revisi', 'ACC', 'Selesai'];
            } elseif ($roleUser === 'kasubag') {
                $allowedStatuses = ['Verifikasi Kasubag'];
            } elseif ($roleUser === 'kepala') {
                $allowedStatuses = ['Verifikasi Kepala'];
            } else {
                // Untuk role lain (misal: Super Admin), tampilkan semua kecuali yang Ditolak Final
                $allowedStatuses = ['Proses', 'Atur Syarat', 'Lengkapi Berkas', 'Verifikasi Berkas', 'Verifikasi Kasubag', 'Verifikasi Kepala', 'Perlu Revisi', 'ACC', 'Selesai'];
            }
    
            // Terapkan filter status berdasarkan role
            if (!empty($allowedStatuses)) {
                $query->whereIn('status', $allowedStatuses);
            }
    
            // --- C. FILTER STATUS TAMBAHAN (dari Request) ---
            // Filter ini akan bekerja di atas filter role
            if ($request->filled('status')) {
                // Pastikan status yang diminta ada dalam allowedStatuses untuk role tersebut
                if (in_array($request->status, $allowedStatuses)) {
                    $query->where('status', $request->status);
                } else {
                    // Jika status yang diminta tidak valid untuk role tersebut, return empty result
                    $query->where('status', 'INVALID_STATUS_FOR_ROLE'); 
                }
            }
    
            // Filter berdasarkan kategori yang ditugaskan
            if ($kategoriTarget) {
                $query->where('kategori', 'LIKE', '%' . $kategoriTarget . '%');
            }
    
            // --- D. STATISTIK DASHBOARD ---
            $statQuery = PengajuanSekolah::query();
            if ($kategoriTarget) $statQuery->where('kategori', 'LIKE', '%' . $kategoriTarget . '%');
            
            // Statistik juga perlu disesuaikan per role jika diinginkan, tapi untuk sekarang pakai query yang sama
            $count_proses     = (clone $statQuery)->whereIn('status', ['Proses'])->count();
            $count_upload     = (clone $statQuery)->whereIn('status', ['Atur Syarat', 'Lengkapi Berkas'])->count();
            $count_verifikasi = (clone $statQuery)->whereIn('status', ['Verifikasi Berkas', 'Verifikasi Kasubag', 'Verifikasi Kepala', 'Perlu Revisi'])->count();
            $count_selesai    = (clone $statQuery)->where('status', 'ACC')->count();
    
    
            $data = $query->latest()->paginate(10)->withQueryString();
    
            $templates = TipeSurat::where('kategori', 'layanan')
                ->orWhere('kategori', 'sk')
                ->get();
    
            return view('admin.verifikasi.index', compact(
                'data',
                'title',
                'templates',
                'isKasubag',
                'isKepala',
                'count_proses',
                'count_upload',
                'count_verifikasi',
                'count_selesai'
            ))->with('kategoriUrl', $kategoriTarget);
        }

    /**
     * 2. VERIFIKASI BERKAS OLEH STAF (REVISI)
     */
    public function verifyProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        $dokumenList = $pengajuan->dokumen_syarat;
        $action = $request->input('action');
    
        if ($action == 'reject') {
            // Gunakan validasi yang lebih longgar dulu untuk testing
            $request->validate([
                'alasan_tolak' => 'required' 
            ]);
            
            $statusBaru = 'Perlu Revisi';
        } else {
            $statusBaru = 'Verifikasi Kasubag';
        }
    
        foreach ($dokumenList as $key => $doc) {
            $uniq = $id . '_' . $key;
            $statusToggle = $request->input("status_toggle_{$uniq}"); 
    
            // Ambil ID dokumen, jika tidak ada id pake key index
            $docId = $doc['id'] ?? $key;
    
            if ($statusToggle === '0') { 
                $catatan = $request->input("catatan.{$docId}");
                $dokumenList[$key]['valid'] = false;
                $dokumenList[$key]['catatan'] = $catatan ?: 'Berkas tidak sesuai.';
            } else {
                $dokumenList[$key]['valid'] = true;
                $dokumenList[$key]['catatan'] = null;
            }
        }
    
        $pengajuan->update([
            'dokumen_syarat'   => $dokumenList, 
            'status'           => $statusBaru,
            'alasan_tolak'     => ($action == 'reject') ? $request->alasan_tolak : null,
            'catatan_internal' => $request->catatan_internal,
            'acc_admin_at'     => ($action == 'approve') ? now() : $pengajuan->acc_admin_at,
        ]);
        
        $pesanNotif = ($action == 'reject') ? 'Perbaikan Berkas (Revisi)' : 'Diteruskan ke Kasubag';
        $this->notifySchool($pengajuan, $pesanNotif);
    
        $type = ($action == 'reject') ? 'warning' : 'success';
        return redirect()->back()->with($type, 'Proses berhasil: ' . $statusBaru);
    }

    /**
     * 3. PROSES KASUBAG
     */
    public function kasubagProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        $dokumenList = $pengajuan->dokumen_syarat; // Ambil list dokumen
        $action = $request->input('action');
    
        if ($action == 'reject') {
            $statusBaru = 'Verifikasi Berkas'; // Kembali ke staf
        } else {
            $statusBaru = 'Verifikasi Kepala'; // Teruskan ke Kepala
        }
    
        // 🔥 PROSES STATUS TIAP DOKUMEN (Agar Catatan Per Berkas Tersimpan)
        if (is_array($dokumenList)) {
            foreach ($dokumenList as $key => $doc) {
                $uniq = $id . '_' . $key;
                $statusToggle = $request->input("status_toggle_{$uniq}"); 
                $docId = $doc['id'] ?? $key;
    
                if ($statusToggle === '0') { 
                    $catatan = $request->input("catatan.{$docId}");
                    $dokumenList[$key]['valid'] = false;
                    $dokumenList[$key]['catatan'] = $catatan ?: 'Ditolak oleh Kasubag.';
                } else {
                    $dokumenList[$key]['valid'] = true;
                    $dokumenList[$key]['catatan'] = null;
                }
            }
        }
    
        // UPDATE DATA PENGAJUAN
        $pengajuan->update([
            'dokumen_syarat'   => $dokumenList,
            'status'           => $statusBaru,
            'alasan_tolak'     => $request->alasan_tolak, // Simpan alasan tolak global
            'catatan_internal' => $request->catatan_internal,
            'acc_kasubag_at'   => ($action == 'approve') ? now() : $pengajuan->acc_kasubag_at,
        ]);
    
        if ($action == 'approve') {
            // Matikan ini sementara jika masih blank page untuk testing
            $this->notifySchool($pengajuan, 'Diteruskan ke Kepala KCD');
            return back()->with('success', 'Disetujui Kasubag. Diteruskan ke Kepala KCD.');
        } else {
            return back()->with('warning', 'Data dikembalikan ke staf untuk revisi.');
        }
    }

    /**
     * 4. PROSES KEPALA (ACC AKHIR)
     */
    public function kepalaProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        $dokumenList = $pengajuan->dokumen_syarat;
        $action = $request->input('action');
    
        // 1. PROSES STATUS TIAP DOKUMEN (Agar Catatan Per Berkas Tersimpan)
        if (is_array($dokumenList)) {
            foreach ($dokumenList as $key => $doc) {
                $uniq = $id . '_' . $key;
                $statusToggle = $request->input("status_toggle_{$uniq}"); 
                $docId = $doc['id'] ?? $key;
    
                if ($statusToggle === '0') { 
                    $catatan = $request->input("catatan.{$docId}");
                    $dokumenList[$key]['valid'] = false;
                    $dokumenList[$key]['catatan'] = $catatan ?: 'Ditolak oleh Kepala KCD.';
                } else {
                    $dokumenList[$key]['valid'] = true;
                    $dokumenList[$key]['catatan'] = null;
                }
            }
        }
    
        // 2. LOGIKA JIKA DITOLAK (Kembali ke Meja Kasubag)
        if ($action == 'reject') {
            $pengajuan->update([
                'dokumen_syarat'   => $dokumenList,
                'status'           => 'Verifikasi Kasubag', // Kembali ke Kasubag
                'alasan_tolak'     => $request->alasan_tolak, // Alasan kenapa ditolak (Global)
                'catatan_internal' => 'CATATAN KEPALA: ' . $request->catatan_internal // Pesan internal
            ]);
            return back()->with('error', 'Data dikembalikan ke Kasubag.');
        }
    
        // 3. LOGIKA JIKA DISETUJUI (ACC AKHIR)
        $request->validate(['template_id' => 'required|exists:tipe_surats,id']);
    
        // Logika Penomoran SK
        $nomorBaru = NomorSuratSettingController::getPreviewNomor('sk');
        if ($nomorBaru == '[Format Belum Diatur]') {
            $nomorBaru = "800/" . rand(100, 999) . "/KCD.XII/" . date('Y');
        } else {
            $setting = \App\Models\NomorSuratSetting::where('kategori', 'sk')->first();
            if ($setting) $setting->increment('nomor_terakhir');
        }
    
        $pengajuan->update([
            'dokumen_syarat'   => $dokumenList,
            'status'           => 'ACC',
            'acc_kepala_at'    => now(),
            'nomor_sk'         => $nomorBaru,
            'tgl_selesai'      => date('Y-m-d'),
            'template_id'      => $request->template_id,
            'alasan_tolak'     => null, // Reset karena sudah disetujui
            'catatan_internal' => $request->catatan_internal
        ]);
    
        // Kirim notifikasi Webhook (Matikan sementara jika masih blank page di localhost)
        $this->notifySchool($pengajuan, 'Selesai (ACC)');
    
        return back()->with('success', 'SK telah terbit dan pengajuan disetujui!');
    }
    
    /**
     * 5. PERSETUJUAN AWAL & SYARAT
     */
    public function approveInitial($id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        $pengajuan->update(['status' => 'Atur Syarat']);
        $this->notifySchool($pengajuan, 'Permohonan Diterima');
        return back()->with('success', 'Disetujui awal. Silakan tentukan daftar persyaratan.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['alasan_tolak' => 'required|string']);
        $pengajuan = PengajuanSekolah::findOrFail($id);
        $pengajuan->update(['status' => 'Ditolak', 'alasan_tolak' => $request->alasan_tolak]);
        $this->notifySchool($pengajuan, 'Permohonan Ditolak');
        return back()->with('danger', 'Permohonan ditolak.');
    }

    public function setSyarat(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        $request->validate(['syarat' => 'required|array|min:1', 'syarat.*' => 'required|string']);

        $syaratList = [];
        foreach ($request->syarat as $nama) {
            $syaratList[] = ['id' => (string) Str::uuid(), 'nama' => $nama, 'file' => null, 'valid' => null, 'catatan' => null];
        }

        $pengajuan->update(['dokumen_syarat' => $syaratList, 'status' => 'Lengkapi Berkas']);
        $this->notifySchool($pengajuan, 'Daftar Syarat Terkirim');
        return back()->with('success', 'Syarat dikirim ke sekolah.');
    }

    /**
     * 6. KIRIM ULANG NOTIFIKASI ACC (MANUAL TRIGGER) - NEW METHOD
     * Ini fungsi baru yang dipanggil tombol pesawat kertas.
     */
    public function resendAcc($id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);

        // Pastikan statusnya memang sudah ACC / Selesai
        $status = strtolower($pengajuan->status);
        if (!in_array($status, ['acc', 'selesai', 'selesai (acc)'])) {
            return back()->with('error', 'Hanya pengajuan berstatus ACC yang bisa dikirim ulang.');
        }

        // Panggil lagi fungsi notifikasi di bawah
        $this->notifySchool($pengajuan, 'Selesai (ACC)');

        return back()->with('success', 'Notifikasi ACC & Link SK berhasil dikirim ulang ke sekolah!');
    }

    /**
     * 7. WEBHOOK NOTIFIKASI KE SEKOLAH
     */
    private function notifySchool($pengajuan, $statusLabel)
    {
        if (!$pengajuan->url_callback) return;
        try {
            $payload = [
                'uuid'           => $pengajuan->uuid,
                'status_kcd'     => $pengajuan->status,
                'pesan'          => $statusLabel,
                'alasan_tolak'   => $pengajuan->alasan_tolak,
                'dokumen_syarat' => $pengajuan->dokumen_syarat,
                'updated_at'     => now()->toDateTimeString()
            ];

            // Kalau ACC, lampirkan link downloadnya
            if ($pengajuan->status == 'ACC') {
                $payload['hasil_sk'] = [
                    'nomor_sk'     => $pengajuan->nomor_sk,
                    // Route 'cetak.sk' ini route di KCD buat download PDF hasil generate
                    'download_url' => route('cetak.sk', $pengajuan->uuid)
                ];
            }

            Http::withHeaders(['X-API-KEY' => env('API_SECRET_KEY')])
                ->timeout(10)->post($pengajuan->url_callback, $payload);
        } catch (\Exception $e) {
            Log::error("Webhook Gagal: " . $e->getMessage());
        }
    }
}
