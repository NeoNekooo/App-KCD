<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, Log, Http, Storage, File};
use Illuminate\Support\Str;
use App\Models\DokumenLayanan;
use App\Jobs\DownloadAndArchiveActionJob; 

class TerimaPengajuanController extends Controller
{
    /**
     * STEP 1: Terima Data Awal (Identitas & Snapshot Profil)
     */
    public function terimaRequestAwal(Request $request)
    {
        if ($request->header('X-API-KEY') !== env('API_SECRET_KEY')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'uuid'            => 'required|uuid',
            'npsn'            => 'required|string|max:10',
            'nama_sekolah'    => 'required|string|max:255',
            'nama_guru'       => 'required|string|max:255',
            'nip'             => 'nullable|string|max:50',
            'kategori'        => 'required|string|max:100',
            'judul'           => 'required|string|max:255',
            'file_permohonan' => 'required|url',
            'data_siswa_json' => 'nullable|array', // Snapshot biodata
            'url_callback'    => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            // 🔥 SEKARANG MENYIMPAN data_siswa_json KE KOLOM data_profil_json 🔥
            $pengajuan = PengajuanSekolah::updateOrCreate(
                ['uuid' => $request->uuid],
                [
                    'npsn'             => $request->npsn,
                    'nama_sekolah'     => $request->nama_sekolah,
                    'nama_guru'        => $request->nama_guru,
                    'nip'              => $request->nip,
                    'kategori'         => Str::slug($request->kategori),
                    'judul'            => $request->judul,
                    'file_permohonan'  => $request->file_permohonan,
                    'url_callback'     => $request->url_callback,
                    'data_siswa_json' => $request->data_siswa_json, // Simpan array snapshot
                    'status'           => 'Proses',
                ]
            );

            if ($request->filled('file_permohonan')) {
                DownloadAndArchiveActionJob::dispatch(
                    $request->file_permohonan,
                    $pengajuan->id,
                    'Surat Permohonan'
                );
            }

            Log::info("API KCD: Data GTK Masuk - UUID: {$request->uuid}");
            return response()->json(['status' => 'success', 'message' => 'Data awal diterima.']);

        } catch (\Exception $e) {
            Log::error("API KCD Error (Request Awal): " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Server Error'], 500);
        }
    }

    /**
     * STEP 2: Terima Berkas Persyaratan (Update Berkas)
     */
    public function terimaBerkas(Request $request)
    {
        if ($request->header('X-API-KEY') !== env('API_SECRET_KEY')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        // 🔥 FIX: Support uuid_sekolah dan field 'file' dari sekolah 🔥
        $targetUuid = $request->uuid_sekolah ?? $request->uuid;

        $validator = Validator::make($request->all(), [
            'uuid_sekolah'           => 'nullable|uuid|exists:pengajuan_sekolahs,uuid',
            'uuid'                   => 'nullable|uuid|exists:pengajuan_sekolahs,uuid',
            'dokumen_syarat'         => 'required|array',
            'dokumen_syarat.*.nama'  => 'required|string',
            'dokumen_syarat.*.file'  => 'nullable|url', // Kunci baru dari sekolah
            'dokumen_syarat.*.url'   => 'nullable|url',  // Support kunci lama
        ]);

        if ($validator->fails() || empty($targetUuid)) {
            return response()->json(['status' => 'error', 'message' => 'UUID tidak valid', 'errors' => $validator->errors()], 422);
        }

        try {
            $pengajuan = PengajuanSekolah::where('uuid', $targetUuid)->first();
            
            // Simpan berkas ke database lokal
            $pengajuan->update([
                'dokumen_syarat' => $request->dokumen_syarat,
                'status'         => 'Verifikasi Berkas'
            ]);

            // --- LOGIKA HAPUS DAN ARSIP ULANG ---
            $pengajuan->dokumenLayanan()->delete();
            $archivePath = "arsip_dokumen/{$pengajuan->id}";
            if (Storage::disk('public')->exists($archivePath)) {
                Storage::disk('public')->deleteDirectory($archivePath);
            }

            if ($pengajuan->file_permohonan) {
                DownloadAndArchiveActionJob::dispatch($pengajuan->file_permohonan, $pengajuan->id, 'Surat Permohonan');
            }
            
            foreach ($request->dokumen_syarat as $dokumen) {
                // 🔥 FIX: Ambil URL dari field 'file' atau 'url' 🔥
                $fileUrl = $dokumen['file'] ?? $dokumen['url'] ?? null;

                if (!empty($fileUrl)) {
                    DownloadAndArchiveActionJob::dispatch(
                        $fileUrl,
                        $pengajuan->id,
                        $dokumen['nama']
                    );
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Berkas berhasil diperbarui dan diarsipkan ulang.']);
        } catch (\Exception $e) {
            Log::error("API KCD Berkas Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Server Error.'], 500);
        }
    }

    public function handle(Request $request) {
        return response()->json(['message' => 'Endpoint Ready']);
    }
}