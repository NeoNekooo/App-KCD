<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSekolah;
use App\Models\TipeSurat;
use App\Models\NomorSuratSetting;
use App\Http\Controllers\Admin\Administrasi\NomorSuratSettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VerifikasiPdController extends Controller
{
    /**
     * Menampilkan daftar pengajuan Peserta Didik dengan filter Tab
     */
    public function index(Request $request)
    {
        $title = 'Layanan Peserta Didik';
        
        // 1. Ambil daftar kategori unik secara dinamis
        $list_kategori = PengajuanSekolah::where('tipe_pengaju', 'PD')
        ->whereNotNull('kategori')
        ->distinct()
        ->pluck('kategori');
    
        // 2. Query Utama: Filter khusus tipe PD
        $query = PengajuanSekolah::query()->where('tipe_pengaju', 'PD');
    
        // 3. Filter Berdasarkan Tab Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
    
        // 4. Filter Berdasarkan Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        // 5. Hitung Statistik global PD
        $statQuery = PengajuanSekolah::where('tipe_pengaju', 'PD');
    
        $count_proses     = (clone $statQuery)->whereIn('status', ['Proses', 'Verifikasi Berkas'])->count();
        $count_upload     = (clone $statQuery)->where('status', 'Lengkapi Berkas')->count();
        $count_verifikasi = (clone $statQuery)->whereIn('status', ['Verifikasi Berkas', 'Perlu Revisi'])->count();
        $count_selesai    = (clone $statQuery)->where('status', 'ACC')->count();
    
        // 6. Ambil Data dengan Pagination
        $data = $query->latest()->paginate(10)->withQueryString();
    
        // 7. Ambil Template SK
        $templates = TipeSurat::where('kategori', 'siswa')->get();
    
        return view('admin.verifikasi_pd.index', compact(
            'data', 'title', 'templates', 'list_kategori',
            'count_proses', 'count_upload', 'count_verifikasi', 'count_selesai'
        ));
    }

    /**
     * Memproses Validasi Awal (Setuju Surat Permohonan Sekolah)
     */
    public function approveInitial($id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        
        $pengajuan->update([
            'status' => 'Atur Syarat'
        ]);

        // 🔥 Segarkan data agar status terbaru terbaca oleh webhook
        $pengajuan->refresh(); 

        $this->notifySchool($pengajuan, 'Permohonan Diterima (Silakan Tunggu Daftar Syarat)');
        return back()->with('success', 'Permohonan divalidasi. Silakan tentukan daftar persyaratan.');
    }

    /**
     * Menyimpan Daftar Syarat & Kirim ke Sekolah
     */
    public function setSyarat(Request $request, $id)
    {
        $request->validate([
            'syarat' => 'required|array',
            'syarat.*' => 'required|string'
        ]);

        $pengajuan = PengajuanSekolah::findOrFail($id);
        
        // Bungkus syarat menjadi format JSON standar
        $dokumenSyarat = [];
        foreach ($request->syarat as $s) {
            $dokumenSyarat[] = [
                'id'      => (string) Str::uuid(),
                'nama'    => $s,
                'file'    => null,
                'valid'   => null,
                'catatan' => null
            ];
        }

        $pengajuan->update([
            'dokumen_syarat' => $dokumenSyarat,
            'status'         => 'Lengkapi Berkas' // Status pemicu upload di Sekolah
        ]);

        // 🔥 WAJIB: Refresh agar model mengirim data syarat terbaru
        $pengajuan->refresh(); 

        // Kirim data ke sekolah
        $this->notifySchool($pengajuan, 'Daftar Persyaratan Telah Ditetapkan');

        return back()->with('success', 'Daftar persyaratan berhasil dikirim ke pihak sekolah.');
    }

    /**
     * Memproses Verifikasi Akhir: Langsung ACC atau Revisi
     */
    public function verifyProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        $dokumenList = $pengajuan->dokumen_syarat;
        $action = $request->input('action');

        // --- A. LOGIKA JIKA ADMIN MEMILIH REVISI ---
        if ($action == 'reject') {
            $request->validate([
                'alasan_tolak' => 'required|string'
            ]);
            
            $this->updateIndividualDocStatus($request, $id, $dokumenList);

            $pengajuan->update([
                'dokumen_syarat'   => $dokumenList,
                'status'           => 'Perlu Revisi',
                'alasan_tolak'     => $request->alasan_tolak,
                'catatan_internal' => $request->catatan_internal,
            ]);

            $pengajuan->refresh(); // 🔥 Refresh data revisi
            $this->notifySchool($pengajuan, 'Perbaikan Berkas Siswa (Revisi)');
            return back()->with('warning', 'Berkas dikembalikan ke sekolah.');
        }

        // --- B. LOGIKA JIKA ADMIN MEMILIH SETUJU (ACC) ---
        $request->validate([
            'template_id' => 'required|exists:tipe_surats,id'
        ]);

        $nomorBaru = NomorSuratSettingController::getPreviewNomor('sk');
        if ($nomorBaru !== '[Format Belum Diatur]') {
            $setting = NomorSuratSetting::where('kategori', 'sk')->first();
            if ($setting) $setting->increment('nomor_terakhir');
        }

        if (is_array($dokumenList)) {
            foreach ($dokumenList as $key => $doc) {
                $dokumenList[$key]['valid'] = true;
                $dokumenList[$key]['catatan'] = null;
            }
        }

        $pengajuan->update([
            'dokumen_syarat'   => $dokumenList,
            'status'           => 'ACC',
            'nomor_sk'         => $nomorBaru,
            'tgl_selesai'      => date('Y-m-d'),
            'template_id'      => $request->template_id,
            'acc_admin_at'     => now(),
        ]);

        $pengajuan->refresh(); // 🔥 Refresh data ACC
        $this->notifySchool($pengajuan, 'Selesai (ACC)');

        return back()->with('success', 'Pengajuan PD disetujui.');
    }

    /**
     * Helper: Update status validasi tiap file dokumen
     */
    private function updateIndividualDocStatus($request, $id, &$dokumenList)
    {
        if (!is_array($dokumenList)) return;
    
        foreach ($dokumenList as $key => $doc) {
            $uniq = $id . '_' . $key;
            $statusToggle = $request->input("status_toggle_{$uniq}"); 
            $docId = $doc['id'] ?? $key;
    
            // Pastikan statusToggle tidak null sebelum memproses
            if ($statusToggle !== null) {
                if ($statusToggle === '0') { 
                    $dokumenList[$key]['valid'] = false;
                    $dokumenList[$key]['catatan'] = $request->input("catatan.{$docId}") ?: 'Berkas tidak sesuai/buram.';
                } else {
                    $dokumenList[$key]['valid'] = true;
                    $dokumenList[$key]['catatan'] = null;
                }
            }
        }
    }

    /**
     * Webhook: Kirim notifikasi status balik ke aplikasi sekolah
     */
    private function notifySchool($pengajuan, $statusLabel)
    {
        if (!$pengajuan->url_callback) return;

        try {
            // Payload yang dikirim ke Sekolah
            $payload = [
                'uuid'           => $pengajuan->uuid, // 🔥 UBAH: Gunakan 'uuid' yang sudah sama dengan sekolah
                'status_kcd'     => $pengajuan->status,
                'pesan'          => $statusLabel,
                'alasan_tolak'   => $pengajuan->alasan_tolak,
                'dokumen_syarat' => $pengajuan->dokumen_syarat, // Array persyaratan
                'updated_at'     => now()->toDateTimeString()
            ];

            if ($pengajuan->status == 'ACC') {
                $payload['sk_download_url'] = route('cetak.sk', $pengajuan->uuid);
                $payload['nomor_sk'] = $pengajuan->nomor_sk;
            }

            Http::withHeaders(['X-API-KEY' => env('API_SECRET_KEY')])
                ->timeout(10)
                ->post($pengajuan->url_callback, $payload);

        } catch (\Exception $e) {
            Log::error("Webhook PD Gagal dikirim ke sekolah: " . $e->getMessage());
        }
    }
}