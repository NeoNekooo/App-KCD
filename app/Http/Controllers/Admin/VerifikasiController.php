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
        
        $query = PengajuanSekolah::query();

        // ---------------------------------------------------------------------
        // A. LOGIKA HAK AKSES PEGAWAI (MODE TUGAS)
        // ---------------------------------------------------------------------
        $kategoriTarget = $request->kategori; 

        if ($user->role === 'Pegawai' && $user->pegawai_kcd_id) {
            $penugasan = TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)
                                        ->where('is_active', 1)
                                        ->first();

            if ($penugasan && !empty($penugasan->kategori_layanan)) {
                // Cek apakah dia Pegawai "Sapu Jagat" (Umum)
                $masterKeys = ['umum', 'all', 'semua-layanan', 'koordinator'];
                
                if (in_array(strtolower($penugasan->kategori_layanan), $masterKeys)) {
                    // Mode Umum: Tidak ada pembatasan, ikuti request URL
                } else {
                    // Mode Spesifik: Paksa filter sesuai tugas
                    $hakAkses = $penugasan->kategori_layanan;
                    
                    // Security Check: Kalau coba akses kategori lain lewat URL, tolak.
                    if ($request->filled('kategori') && $request->kategori !== $hakAkses) {
                        abort(403, 'AKSES DITOLAK. Tugas Anda hanya: ' . strtoupper(str_replace('-', ' ', $hakAkses)));
                    }

                    // Paksa variable filter
                    $kategoriTarget = $hakAkses; 
                    $request->merge(['kategori' => $hakAkses]);
                }
            }
        }

        // ---------------------------------------------------------------------
        // B. FILTER QUERY (PENCARIAN)
        // ---------------------------------------------------------------------
        if ($kategoriTarget) {
            $query->where(function($q) use ($kategoriTarget) {
                $q->where('kategori', 'LIKE', '%' . $kategoriTarget . '%')
                  ->orWhere('kategori', 'LIKE', '%' . str_replace('-', ' ', $kategoriTarget) . '%');
            });
            // Update Title Halaman biar sesuai konteks
            $title = 'Verifikasi: ' . ucwords(str_replace('-', ' ', $kategoriTarget));
        }

        // ---------------------------------------------------------------------
        // C. FILTER STATUS (BERDASARKAN ROLE)
        // ---------------------------------------------------------------------
        if ($user->role == 'kasubag') {
            $query->whereIn('status', ['Verifikasi Kasubag', 'Verifikasi Kepala', 'ACC'])
                  ->orderByRaw("FIELD(status, 'Verifikasi Kasubag') DESC");
        } elseif ($user->role == 'kepala') {
            $query->whereIn('status', ['Verifikasi Kepala', 'ACC'])
                  ->orderByRaw("FIELD(status, 'Verifikasi Kepala') DESC");
        } else {
            // Admin & Pegawai
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
        }

        // ---------------------------------------------------------------------
        // D. HITUNG STATISTIK (DENGAN CLONE QUERY AGAR AKURAT)
        // ---------------------------------------------------------------------
        $statQuery = clone $query;
        
        // Reset filter status untuk statistik (kecuali Kasubag/Kepala yg memang terbatas)
        if ($user->role !== 'kasubag' && $user->role !== 'kepala') {
             $statQuery = PengajuanSekolah::query();
             if($kategoriTarget) {
                $statQuery->where(function($q) use ($kategoriTarget) {
                    $q->where('kategori', 'LIKE', '%' . $kategoriTarget . '%')
                      ->orWhere('kategori', 'LIKE', '%' . str_replace('-', ' ', $kategoriTarget) . '%');
                });
             }
        }

        $count_proses     = (clone $statQuery)->where('status', 'Proses')->count(); 
        $count_upload     = (clone $statQuery)->where('status', 'Menunggu Upload')->count(); 
        $count_verifikasi = (clone $statQuery)->whereIn('status', ['Verifikasi Berkas', 'Verifikasi Kasubag', 'Verifikasi Kepala'])->count();
        $count_selesai    = (clone $statQuery)->where('status', 'ACC')->count(); 

        // ---------------------------------------------------------------------
        // E. EKSEKUSI DATA
        // ---------------------------------------------------------------------
        $data = $query->latest()->paginate(10)->withQueryString();
        $templates = TipeSurat::where('kategori', 'sk')->get();

        // Kirim data ke View (Perbaikan: pakai ->with() untuk kategoriUrl)
        return view('admin.verifikasi.index', compact(
            'data', 'title', 'templates',
            'count_proses', 'count_upload', 'count_verifikasi', 'count_selesai'
        ))->with('kategoriUrl', $kategoriTarget);
    }

    // =========================================================================
    // 2. ADMIN: SET SYARAT (TIKET BARU)
    // =========================================================================
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
        return back()->with('success', 'Persyaratan dikirim! Menunggu sekolah upload berkas.');
    }

    // =========================================================================
    // 3. ADMIN/PEGAWAI: VERIFIKASI BERKAS
    // =========================================================================
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

        if ($action == 'reject' || !$isAllValid) {
            $pengajuan->update([
                'dokumen_syarat' => $dokumenList,
                'status' => 'Revisi'
            ]);
            $this->notifySchool($pengajuan, 'Revisi Berkas');
            return back()->with('warning', 'Berkas dikembalikan ke sekolah untuk revisi.');
        } 
        else {
            $pengajuan->update([
                'dokumen_syarat' => $dokumenList,
                'status' => 'Verifikasi Kasubag',
                'acc_admin_at' => Carbon::now()
            ]);
            $this->notifySchool($pengajuan, 'Sedang Diverifikasi Kasubag');
            return back()->with('success', 'Berkas valid! Diteruskan ke Kasubag.');
        }
    }

    // =========================================================================
    // 4. KASUBAG PROCESS
    // =========================================================================
    public function kasubagProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        $action = $request->input('action');

        if ($action == 'revisi' || $action == 'reject') {
            $pengajuan->update([
                'status' => 'Revisi',
                'catatan_internal' => $request->input('catatan_internal')
            ]);
            $this->notifySchool($pengajuan, 'Revisi (Kasubag)'); 
            return back()->with('warning', 'Berkas dikembalikan untuk revisi.');
        } 
        else {
            $pengajuan->update([
                'status' => 'Verifikasi Kepala',
                'catatan_internal' => $request->input('catatan_internal'),
                'acc_kasubag_at' => Carbon::now()
            ]);
            return back()->with('success', 'Validasi sukses! Diteruskan ke Kepala KCD.');
        }
    }

    // =========================================================================
    // 5. KEPALA PROCESS (ACC & NOMOR SK)
    // =========================================================================
    public function kepalaProcess(Request $request, $id)
    {
        $pengajuan = PengajuanSekolah::findOrFail($id);
        $action = $request->input('action');

        if ($action == 'tolak' || $action == 'reject') {
            $pengajuan->update(['status' => 'Revisi']); 
            $this->notifySchool($pengajuan, 'Ditolak Kepala KCD');
            return back()->with('error', 'Pengajuan ditolak.');
        } 
        else {
            $request->validate([
                'template_id' => 'required|exists:tipe_surats,id',
            ], ['template_id.required' => 'Pilih Template SK terlebih dahulu!']);

            $nomorBaru = NomorSuratSettingController::getPreviewNomor('sk');

            if ($nomorBaru == '[Format Belum Diatur]') {
                $nomorBaru = "800 / " . rand(100,999) . " - KCD.XII / " . date('Y');
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
            return back()->with('success', 'Dokumen berhasil di-ACC. Nomor: ' . $nomorBaru);
        }
    }

    // =========================================================================
    // 6. HELPER: NOTIFIKASI KE SEKOLAH (WEBHOOK)
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
            Log::error("Gagal webhook: " . $e->getMessage());
        }
    }
}