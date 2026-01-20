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
     * FIXED: Menghapus batasan status agar data tetap terlihat di semua level.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $title = 'Daftar Pengajuan Masuk';
        $roleUser = strtolower($user->role);
        $isKasubag = $roleUser === 'kasubag';
        $isKepala  = $roleUser === 'kepala';

        $query = PengajuanSekolah::query();

        // --- A. FILTER KATEGORI (TUGAS PEGAWAI) ---
        $kategoriTarget = $request->kategori; 
        if ($roleUser === 'pegawai' && $user->pegawai_kcd_id) {
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

        // --- B. FILTER STATUS (DIBUAT TERBUKA AGAR DATA TIDAK HILANG) ---
        // Jika ada filter manual dari dropdown status, gunakan itu.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // JIKA TIDAK ADA FILTER: Semua role bisa melihat semua data di kategorinya.
            // Data tidak akan menghilang saat status berubah.
            $query->whereNotNull('status'); 
        }

        // Filter berdasarkan kategori yang ditugaskan
        if ($kategoriTarget) {
            $query->where('kategori', 'LIKE', '%' . $kategoriTarget . '%');
        }

        // --- C. STATISTIK DASHBOARD ---
        $statQuery = PengajuanSekolah::query();
        if ($kategoriTarget) $statQuery->where('kategori', 'LIKE', '%' . $kategoriTarget . '%');

        $count_proses     = (clone $statQuery)->where('status', 'Proses')->count(); 
        $count_upload     = (clone $statQuery)->whereIn('status', ['Atur Syarat', 'Lengkapi Berkas'])->count(); 
        $count_verifikasi = (clone $statQuery)->whereIn('status', ['Verifikasi Berkas', 'Verifikasi Kasubag', 'Verifikasi Kepala', 'Perlu Revisi'])->count();
        $count_selesai    = (clone $statQuery)->where('status', 'ACC')->count(); 

        // Urutkan data terbaru di atas
        $data = $query->latest()->paginate(10)->withQueryString();
        $templates = TipeSurat::where('kategori', 'sk')->get();

        return view('admin.verifikasi.index', compact(
            'data', 'title', 'templates', 'isKasubag', 'isKepala',
            'count_proses', 'count_upload', 'count_verifikasi', 'count_selesai'
        ))->with('kategoriUrl', $kategoriTarget);
    }

    /**
     * 2. VERIFIKASI BERKAS OLEH STAF
     */
    public function verifyProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        $dokumenList = $pengajuan->dokumen_syarat;
        $isAnyRejected = false;
        $action = $request->input('action');

        foreach ($dokumenList as $key => $doc) {
            $uniq = $id . '_' . $key;
            $statusToggle = $request->input("status_toggle_{$uniq}"); 

            if ($statusToggle === 'false') {
                $catatan = trim($request->input("catatan.{$doc['id']}"));
                $dokumenList[$key]['valid'] = false;
                $dokumenList[$key]['catatan'] = $catatan ?: 'Berkas tidak sesuai.';
                $isAnyRejected = true;
            } else {
                $dokumenList[$key]['valid'] = true;
                $dokumenList[$key]['catatan'] = null;
            }
        }

        if ($action == 'reject' || $isAnyRejected) {
            $pengajuan->update([
                'dokumen_syarat' => $dokumenList, 
                'status' => 'Perlu Revisi',
                'alasan_tolak' => 'Ada dokumen yang perlu diperbaiki.'
            ]);
            $this->notifySchool($pengajuan, 'Perbaikan Berkas (Revisi)');
            return back()->with('warning', 'Berkas dikirim kembali ke sekolah untuk revisi.');
        } else {
            $pengajuan->update([
                'dokumen_syarat' => $dokumenList,
                'status'         => 'Verifikasi Kasubag',
                'acc_admin_at'   => now()
            ]);
            $this->notifySchool($pengajuan, 'Diteruskan ke Kasubag');
            return back()->with('success', 'Berkas valid! Berhasil diteruskan ke Kasubag.');
        }
    }

    /**
     * 3. PROSES KASUBAG
     */
    public function kasubagProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);

        if ($request->input('action') == 'reject') {
            // Kembali ke Staf
            $pengajuan->update([
                'status' => 'Verifikasi Berkas',
                'catatan_internal' => 'DITOLAK KASUBAG: ' . $request->input('catatan_internal')
            ]);
            return back()->with('warning', 'Data dikembalikan ke meja staf.');
        } else {
            // Lanjut ke Kepala
            $pengajuan->update([
                'status' => 'Verifikasi Kepala',
                'catatan_internal' => $request->input('catatan_internal'),
                'acc_kasubag_at' => now()
            ]);
            $this->notifySchool($pengajuan, 'Diteruskan ke Kepala KCD');
            return back()->with('success', 'Disetujui Kasubag. Diteruskan ke Kepala KCD.');
        }
    }

    /**
     * 4. PROSES KEPALA (ACC AKHIR)
     */
    public function kepalaProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        
        if ($request->input('action') == 'reject') {
            // Kembali ke Kasubag
            $pengajuan->update([
                'status' => 'Verifikasi Kasubag',
                'catatan_internal' => 'DITOLAK KEPALA: ' . $request->input('catatan_internal')
            ]); 
            return back()->with('error', 'Data dikembalikan ke Kasubag.');
        }

        $request->validate(['template_id' => 'required|exists:tipe_surats,id']);
        
        $nomorBaru = NomorSuratSettingController::getPreviewNomor('sk');
        if ($nomorBaru == '[Format Belum Diatur]') {
            $nomorBaru = "800/" . rand(100,999) . "/KCD.XII/" . date('Y');
        } else {
            $setting = NomorSuratSetting::where('kategori', 'sk')->first();
            if($setting) $setting->increment('nomor_terakhir');
        }

        $pengajuan->update([
            'status'         => 'ACC',
            'acc_kepala_at' => now(),
            'nomor_sk'      => $nomorBaru, 
            'tgl_selesai'   => date('Y-m-d'),
            'template_id'   => $request->template_id 
        ]);

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
        foreach($request->syarat as $nama) {
            $syaratList[] = ['id' => (string) Str::uuid(), 'nama' => $nama, 'file' => null, 'valid' => null, 'catatan' => null];
        }

        $pengajuan->update(['dokumen_syarat' => $syaratList, 'status' => 'Lengkapi Berkas']);
        $this->notifySchool($pengajuan, 'Daftar Syarat Terkirim');
        return back()->with('success', 'Syarat dikirim ke sekolah.');
    }

    /**
     * 6. WEBHOOK NOTIFIKASI KE SEKOLAH
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

            if ($pengajuan->status == 'ACC') {
                $payload['hasil_sk'] = [
                    'nomor_sk'     => $pengajuan->nomor_sk,
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