<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSekolah;
use App\Models\TipeSurat;
use App\Models\TugasPegawaiKcd; 
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
     * Mengatur tampilan daftar pengajuan berdasarkan Role (Staf, Kasubag, Kepala)
     * dan melakukan filter kategori layanan sesuai tugas pegawai.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $title = 'Daftar Pengajuan Masuk';
        
        // Identifikasi Role User secara spesifik
        $isKasubag = ($user->pegawaiKcd && strcasecmp($user->pegawaiKcd->jabatan, 'Kasubag') === 0) || $user->role === 'kasubag';
        $isKepala = strtolower($user->role) === 'kepala';

        $query = PengajuanSekolah::query();

        // --- A. LOGIKA FILTER KATEGORI (HAK AKSES PEGAWAI) ---
        $kategoriTarget = $request->kategori; 
        
        // Pegawai biasa (bukan Kasubag) hanya boleh melihat kategori yang ditugaskan kepadanya
        if ($user->role === 'Pegawai' && $user->pegawai_kcd_id && !$isKasubag) {
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

        // --- B. FILTER STATUS (BERDASARKAN MEJA KERJA) ---
        if ($isKasubag) {
            // Kasubag melihat berkas yang butuh validasinya, yang sudah diteruskan ke Kepala, atau sudah ACC
            $query->whereIn('status', ['Verifikasi Kasubag', 'Verifikasi Kepala', 'ACC'])
                  ->orderByRaw("FIELD(status, 'Verifikasi Kasubag', 'Verifikasi Kepala', 'ACC') ASC");
        } elseif ($isKepala) {
            // Kepala hanya melihat berkas di mejanya atau yang sudah selesai
            $query->whereIn('status', ['Verifikasi Kepala', 'ACC'])
                  ->orderByRaw("FIELD(status, 'Verifikasi Kepala', 'ACC') ASC");
        } else {
            // Admin/Staf bisa memfilter status secara manual
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
        }

        // Filter berdasarkan kategori yang dipilih atau ditugaskan
        if ($kategoriTarget) {
            $query->where(function($q) use ($kategoriTarget) {
                $q->where('kategori', 'LIKE', '%' . $kategoriTarget . '%')
                  ->orWhere('kategori', 'LIKE', '%' . str_replace('-', ' ', $kategoriTarget) . '%');
            });
            $title = 'Verifikasi: ' . ucwords(str_replace('-', ' ', $kategoriTarget));
        }

        // --- C. HITUNG STATISTIK DASHBOARD ---
        $statQuery = PengajuanSekolah::query();
        if ($kategoriTarget) $statQuery->where('kategori', 'LIKE', '%' . $kategoriTarget . '%');

        $count_proses     = (clone $statQuery)->where('status', 'Proses')->count(); 
        $count_upload     = (clone $statQuery)->where('status', 'Menunggu Upload')->count(); 
        $count_verifikasi = (clone $statQuery)->whereIn('status', ['Verifikasi Berkas', 'Verifikasi Kasubag', 'Verifikasi Kepala'])->count();
        $count_selesai    = (clone $statQuery)->where('status', 'ACC')->count(); 

        $data = $query->latest()->paginate(10)->withQueryString();
        $templates = TipeSurat::where('kategori', 'sk')->get();

        return view('admin.verifikasi.index', compact(
            'data', 'title', 'templates', 'isKasubag', 'isKepala',
            'count_proses', 'count_upload', 'count_verifikasi', 'count_selesai'
        ))->with('kategoriUrl', $kategoriTarget);
    }

    /**
     * 2. SET SYARAT (MINTA UPLOAD DOKUMEN)
     * Mengirimkan daftar persyaratan dokumen yang harus dipenuhi sekolah.
     */
    public function setSyarat(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);

        $request->validate([
            'syarat' => 'required|array|min:1',
            'syarat.*' => 'required|string',
        ]);

        $syaratList = [];
        foreach($request->syarat as $index => $nama) {
            $syaratList[] = [
                'id'      => $index + 1,
                'nama'    => $nama,
                'file'    => null,
                'valid'   => false,
                'catatan' => null
            ];
        }

        $pengajuan->update([
            'dokumen_syarat' => $syaratList,
            'status'         => 'Menunggu Upload'
        ]);

        $this->notifySchool($pengajuan, 'Lengkapi Berkas');

        return back()->with('success', 'Persyaratan dokumen berhasil dikirim ke sekolah!');
    }

    /**
     * 3. VERIFIKASI BERKAS (LEVEL STAF)
     * Memeriksa satu per satu dokumen yang diupload sekolah.
     */
    public function verifyProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        $dokumenList = $pengajuan->dokumen_syarat;
        $isAllValid = true;

        // Loop untuk mengecek catatan revisi per dokumen
        foreach ($dokumenList as $key => $doc) {
            $catatanInput = $request->input("catatan.{$doc['id']}");
            if (!empty($catatanInput)) {
                $dokumenList[$key]['valid'] = false;
                $dokumenList[$key]['catatan'] = $catatanInput;
                $isAllValid = false;
            } else {
                $dokumenList[$key]['valid'] = true;
                $dokumenList[$key]['catatan'] = null;
            }
        }

        if ($request->input('action') == 'reject' || !$isAllValid) {
            // Jika ada yang tidak valid, status menjadi Revisi
            $pengajuan->update(['dokumen_syarat' => $dokumenList, 'status' => 'Revisi']);
            $this->notifySchool($pengajuan, 'Perbaikan Berkas');
            return back()->with('warning', 'Berkas dikembalikan ke sekolah untuk diperbaiki.');
        } else {
            // Jika semua oke, lanjut ke meja Kasubag
            $pengajuan->update([
                'dokumen_syarat' => $dokumenList,
                'status'         => 'Verifikasi Kasubag',
                'acc_admin_at'   => Carbon::now()
            ]);
            $this->notifySchool($pengajuan, 'Sedang Diverifikasi Kasubag');
            return back()->with('success', 'Berkas valid! Diteruskan ke Kasubag.');
        }
    }

    /**
     * 4. PROSES KASUBAG
     * Validasi oleh Kepala Sub Bagian Tata Usaha.
     */
    public function kasubagProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);

        if ($request->input('action') == 'reject') {
            // Dikembalikan ke level Staf (Verifikasi Berkas) jika Kasubag menolak secara internal
            $pengajuan->update([
                'status' => 'Verifikasi Berkas',
                'catatan_internal' => 'DITOLAK KASUBAG: ' . $request->input('catatan_internal')
            ]);
            return back()->with('warning', 'Dikembalikan ke Staf Pegawai.');
        } else {
            // Diteruskan ke Kepala KCD
            $pengajuan->update([
                'status' => 'Verifikasi Kepala',
                'catatan_internal' => $request->input('catatan_internal'),
                'acc_kasubag_at' => Carbon::now()
            ]);
            $this->notifySchool($pengajuan, 'Sedang Diverifikasi Kepala');
            return back()->with('success', 'Diteruskan ke Kepala KCD.');
        }
    }

    /**
     * 5. PROSES KEPALA (APPROVAL AKHIR)
     * Memberikan persetujuan final dan menerbitkan nomor SK.
     */
    public function kepalaProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        
        if ($request->input('action') == 'reject') {
            // Jika Kepala menolak, balik ke Kasubag
            $pengajuan->update(['status' => 'Verifikasi Kasubag']); 
            return back()->with('error', 'Dikembalikan ke Kasubag.');
        }

        $request->validate(['template_id' => 'required|exists:tipe_surats,id']);
        
        // Ambil Nomor SK otomatis berdasarkan pengaturan
        $nomorBaru = NomorSuratSettingController::getPreviewNomor('sk');

        if ($nomorBaru == '[Format Belum Diatur]') {
            $nomorBaru = "800/" . rand(100,999) . "/KCD.XII/" . date('Y');
        } else {
            $setting = \App\Models\NomorSuratSetting::where('kategori', 'sk')->first();
            if($setting) $setting->increment('nomor_terakhir');
        }

        $pengajuan->update([
            'status'        => 'ACC',
            'acc_kepala_at' => Carbon::now(),
            'nomor_sk'      => $nomorBaru, 
            'tgl_selesai'   => date('Y-m-d'),
            'template_id'   => $request->template_id 
        ]);

        $this->notifySchool($pengajuan, 'Selesai (ACC)');
        return back()->with('success', 'Dokumen Berhasil di-ACC.');
    }

    /**
     * 6. HELPER WEBHOOK (NOTIFIKASI KE SEKOLAH)
     * Mengirim data terbaru ke API sekolah agar database mereka terupdate otomatis.
     */
    private function notifySchool($pengajuan, $statusLabel)
    {
        try {
            if ($pengajuan->url_callback) {
                // Gunakan status asli Bahasa Indonesia karena Sekolah sudah disesuaikan
                $payload = [
                    'uuid'         => $pengajuan->uuid,
                    'status'       => $pengajuan->status, 
                    'pesan'        => $statusLabel,
                    'requirements' => $pengajuan->dokumen_syarat,
                    'updated_at'   => now()->toDateTimeString()
                ];

                if ($pengajuan->status == 'ACC') {
                    $payload['hasil_sk'] = [
                        'nomor_sk'      => $pengajuan->nomor_sk,
                        'download_url'  => route('cetak.sk', $pengajuan->uuid) 
                    ];
                }

                // Mengirim request ke sekolah menggunakan kunci keamanan dari .env
                $response = Http::withHeaders([
                    'X-CLIENT-SECRET' => env('API_SECRET_KEY') 
                ])->timeout(10)->post($pengajuan->url_callback, $payload);

                if ($response->failed()) {
                    Log::error("Webhook Gagal ke {$pengajuan->url_callback}. Status: " . $response->status());
                }
            }
        } catch (\Exception $e) {
            Log::error("Gagal menjalankan Webhook: " . $e->getMessage());
        }
    }
}