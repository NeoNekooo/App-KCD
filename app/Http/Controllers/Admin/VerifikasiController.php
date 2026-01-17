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
    // =========================================================================
    // 1. HALAMAN UTAMA (INDEX)
    // =========================================================================
    public function index(Request $request)
    {
        $user = Auth::user();
        $title = 'Daftar Pengajuan Masuk';
        
        // Identifikasi apakah user adalah Kasubag berdasarkan jabatan di tabel pegawai_kcds
        $isKasubag = ($user->pegawaiKcd && strcasecmp($user->pegawaiKcd->jabatan, 'Kasubag') === 0);
        
        $query = PengajuanSekolah::query();

        // ---------------------------------------------------------------------
        // A. LOGIKA HAK AKSES (FILTER KATEGORI LAYANAN)
        // ---------------------------------------------------------------------
        $kategoriTarget = $request->kategori; 

        if ($user->role === 'Pegawai' && $user->pegawai_kcd_id) {
            // Kasubag memiliki akses monitoring ke semua kategori layanan
            if (!$isKasubag) {
                // Pegawai Biasa: Hanya bisa melihat kategori sesuai surat tugas
                $penugasan = TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)
                                            ->where('is_active', 1)
                                            ->first();

                if ($penugasan && !empty($penugasan->kategori_layanan)) {
                    $masterKeys = ['umum', 'all', 'semua-layanan', 'koordinator'];
                    if (!in_array(strtolower($penugasan->kategori_layanan), $masterKeys)) {
                        $hakAkses = $penugasan->kategori_layanan;
                        
                        // Keamanan: Cegah manipulasi kategori via URL
                        if ($request->filled('kategori') && $request->kategori !== $hakAkses) {
                            abort(403, 'AKSES DITOLAK. Tugas Anda hanya: ' . strtoupper(str_replace('-', ' ', $hakAkses)));
                        }
                        $kategoriTarget = $hakAkses; 
                        $request->merge(['kategori' => $hakAkses]);
                    }
                }
            }
        }

        // ---------------------------------------------------------------------
        // B. FILTER PENCARIAN & KATEGORI
        // ---------------------------------------------------------------------
        if ($kategoriTarget) {
            $query->where(function($q) use ($kategoriTarget) {
                $q->where('kategori', 'LIKE', '%' . $kategoriTarget . '%')
                  ->orWhere('kategori', 'LIKE', '%' . str_replace('-', ' ', $kategoriTarget) . '%');
            });
            $title = 'Verifikasi: ' . ucwords(str_replace('-', ' ', $kategoriTarget));
        }

        // ---------------------------------------------------------------------
        // C. LOGIKA TAMPILAN STATUS (ALUR KERJA BIROKRASI)
        // ---------------------------------------------------------------------
        if ($isKasubag) {
            // Kasubag memantau berkas di mejanya, meja Kepala, dan yang sudah selesai
            $query->whereIn('status', ['Verifikasi Kasubag', 'Verifikasi Kepala', 'ACC'])
                  ->orderByRaw("FIELD(status, 'Verifikasi Kasubag', 'Verifikasi Kepala', 'ACC') ASC");
        } elseif ($user->role == 'Kepala') {
            // Kepala hanya melihat berkas yang sudah divalidasi oleh Kasubag
            $query->whereIn('status', ['Verifikasi Kepala', 'ACC'])
                  ->orderByRaw("FIELD(status, 'Verifikasi Kepala', 'ACC') ASC");
        } else {
            // Admin/Pegawai melihat dari Tiket Baru sampai tahap Verifikasi Berkas
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
        }

        // ---------------------------------------------------------------------
        // D. STATISTIK REAL-TIME
        // ---------------------------------------------------------------------
        $statQuery = PengajuanSekolah::query();
        if ($kategoriTarget) {
            $statQuery->where(function($q) use ($kategoriTarget) {
                $q->where('kategori', 'LIKE', '%' . $kategoriTarget . '%')
                  ->orWhere('kategori', 'LIKE', '%' . str_replace('-', ' ', $kategoriTarget) . '%');
            });
        }

        $count_proses     = (clone $statQuery)->where('status', 'Proses')->count(); 
        $count_upload     = (clone $statQuery)->where('status', 'Menunggu Upload')->count(); 
        $count_verifikasi = (clone $statQuery)->whereIn('status', ['Verifikasi Berkas', 'Verifikasi Kasubag', 'Verifikasi Kepala'])->count();
        $count_selesai    = (clone $statQuery)->where('status', 'ACC')->count(); 

        $data = $query->latest()->paginate(10)->withQueryString();
        $templates = TipeSurat::where('kategori', 'sk')->get();

        return view('admin.verifikasi.index', compact(
            'data', 'title', 'templates',
            'count_proses', 'count_upload', 'count_verifikasi', 'count_selesai'
        ))->with('kategoriUrl', $kategoriTarget);
    }

    // =========================================================================
    // 2. PEGAWAI: VALIDASI AWAL & ATUR SYARAT (STATUS: PROSES)
    // =========================================================================
    /**
     * Digunakan untuk menyetujui permohonan awal dan mengirim daftar persyaratan ke sekolah.
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

        // Update status menjadi Menunggu Upload agar sekolah bisa mengirim berkas
        $pengajuan->update([
            'dokumen_syarat' => $syaratList,
            'status'         => 'Menunggu Upload'
        ]);

        $this->notifySchool($pengajuan, 'Permohonan Disetujui, Silakan Upload Berkas Persyaratan');
        
        return back()->with('success', 'Permohonan divalidasi. Daftar persyaratan telah dikirim ke sekolah.');
    }

    /**
     * Digunakan jika permohonan awal (Proses) ditolak karena data tidak lengkap.
     */
    public function initialReject(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        
        $pengajuan->update([
            'status' => 'Revisi',
            'catatan_internal' => $request->catatan
        ]);

        $this->notifySchool($pengajuan, 'Permohonan Awal Ditolak: ' . $request->catatan);
        
        return back()->with('warning', 'Permohonan dikembalikan ke sekolah.');
    }

    // =========================================================================
    // 3. PEGAWAI: VERIFIKASI DOKUMEN (STATUS: VERIFIKASI BERKAS)
    // =========================================================================
    /**
     * Memeriksa berkas yang diupload sekolah. Jika OK lanjut ke Kasubag, jika tidak balik ke Sekolah.
     */
    public function verifyProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        $dokumenList = $pengajuan->dokumen_syarat;
        $isAllValid = true;

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

        $action = $request->input('action'); 

        // Jika ada berkas salah atau admin memilih tombol reject
        if ($action == 'reject' || !$isAllValid) {
            $pengajuan->update([
                'dokumen_syarat' => $dokumenList,
                'status' => 'Revisi'
            ]);
            $this->notifySchool($pengajuan, 'Berkas perlu revisi. Silakan periksa catatan di aplikasi.');
            return back()->with('warning', 'Berkas dikembalikan ke sekolah untuk revisi.');
        } 
        else {
            // Teruskan ke meja Kasubag
            $pengajuan->update([
                'dokumen_syarat' => $dokumenList,
                'status' => 'Verifikasi Kasubag',
                'acc_admin_at' => Carbon::now()
            ]);
            $this->notifySchool($pengajuan, 'Berkas diverifikasi oleh Admin, menunggu validasi Kasubag.');
            return back()->with('success', 'Verifikasi dokumen berhasil. Diteruskan ke Kasubag.');
        }
    }

    // =========================================================================
    // 4. KASUBAG: VALIDASI BERJENJANG (STATUS: VERIFIKASI KASUBAG)
    // =========================================================================
    /**
     * Memvalidasi hasil kerja Pegawai Verifikator. Jika salah balik ke Pegawai, jika OK lanjut ke Kepala.
     */
    public function kasubagProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        $action = $request->input('action');

        // Jika Kasubag menolak, berkas balik ke status Verifikasi Berkas (Meja Pegawai)
        if ($action == 'reject') {
            $pengajuan->update([
                'status' => 'Verifikasi Berkas', 
                'catatan_internal' => $request->input('catatan_internal')
            ]);
            return back()->with('warning', 'Berkas dikembalikan ke Pegawai Verifikator untuk pengecekan ulang.');
        } 
        else {
            // Teruskan ke meja Kepala KCD
            $pengajuan->update([
                'status' => 'Verifikasi Kepala',
                'catatan_internal' => $request->input('catatan_internal'),
                'acc_kasubag_at' => Carbon::now()
            ]);
            return back()->with('success', 'Validasi Kasubag sukses. Diteruskan ke Kepala KCD.');
        }
    }

    // =========================================================================
    // 5. KEPALA: APPROVAL AKHIR & CETAK SK (STATUS: VERIFIKASI KEPALA)
    // =========================================================================
    /**
     * Persetujuan akhir. Jika ACC, nomor surat terbit dan SK bisa dicetak.
     */
    public function kepalaProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        $action = $request->input('action');

        // Jika Kepala menolak, kembalikan ke Kasubag
        if ($action == 'reject') {
            $pengajuan->update(['status' => 'Verifikasi Kasubag']); 
            return back()->with('error', 'Pengajuan dikembalikan ke Kasubag untuk ditinjau kembali.');
        } 
        else {
            $request->validate([
                'template_id' => 'required|exists:tipe_surats,id',
            ], ['template_id.required' => 'Silakan pilih Template SK terlebih dahulu!']);

            // Generate nomor SK otomatis
            $nomorBaru = NomorSuratSettingController::getPreviewNomor('sk');

            if ($nomorBaru == '[Format Belum Diatur]') {
                $nomorBaru = "800 / " . rand(100,999) . " - KCD.XII / " . date('Y');
            } else {
                $setting = \App\Models\NomorSuratSetting::where('kategori', 'sk')->first();
                if($setting) $setting->increment('nomor_terakhir');
            }

            $pengajuan->update([
                'status'         => 'ACC',
                'acc_kepala_at'  => Carbon::now(),
                'nomor_sk'       => $nomorBaru, 
                'tgl_selesai'    => date('Y-m-d'),
                'template_id'    => $request->template_id 
            ]);

            $this->notifySchool($pengajuan, 'Pengajuan Disetujui (ACC). SK Telah Terbit.');
            return back()->with('success', 'Dokumen berhasil di-ACC. Nomor SK: ' . $nomorBaru);
        }
    }

    // =========================================================================
    // 6. HELPER: NOTIFIKASI API KE SEKOLAH (WEBHOOK)
    // =========================================================================
    private function notifySchool($pengajuan, $statusLabel)
    {
        try {
            if ($pengajuan->url_callback) {
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
                        'tanggal_terbit'=> date('Y-m-d'),
                        'download_url'  => route('cetak.sk', $pengajuan->uuid) 
                    ];
                }

                Http::withHeaders([
                    'X-CLIENT-SECRET' => env('SEKOLAH_CALLBACK_SECRET') 
                ])->timeout(10)->post($pengajuan->url_callback, $payload);
            }
        } catch (\Exception $e) {
            Log::error("Gagal mengirim Webhook: " . $e->getMessage());
        }
    }
}