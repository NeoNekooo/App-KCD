<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Str;
use App\Models\DokumenLayanan;
use App\Jobs\DownloadAndArchiveActionJob; 

class TerimaPengajuanController extends Controller
{
    /**
     * STEP 1: Terima Data Awal (Identitas & URL Surat Permohonan)
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
            'url_callback'    => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            // Simpan URL asli ke pengajuan_sekolahs
            $pengajuan = PengajuanSekolah::updateOrCreate(
                ['uuid' => $request->uuid],
                [
                    'npsn'            => $request->npsn,
                    'nama_sekolah'    => $request->nama_sekolah,
                    'nama_guru'       => $request->nama_guru,
                    'nip'             => $request->nip,
                    'kategori'        => Str::slug($request->kategori),
                    'judul'           => $request->judul,
                    'file_permohonan' => $request->file_permohonan, // Simpan URL asli
                    'url_callback'    => $request->url_callback,
                    'status'          => 'Proses',
                ]
            );

            // Dispatch job untuk download dan arsipkan file di latar belakang
            if ($request->filled('file_permohonan')) {
                DownloadAndArchiveActionJob::dispatch(
                    $request->file_permohonan,
                    $pengajuan->id,
                    'Surat Permohonan'
                );
            }

            Log::info("API KCD: Data Masuk Berhasil - UUID: {$request->uuid}");
            return response()->json(['status' => 'success', 'message' => 'Data awal berhasil diterima. Proses arsip akan berjalan di latar belakang.']);

        } catch (\Exception $e) {
            Log::error("API KCD Fatal Error (Request Awal): " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * STEP 2: Terima Berkas Persyaratan (Array of URLs)
     */
    public function terimaBerkas(Request $request)
    {
        if ($request->header('X-API-KEY') !== env('API_SECRET_KEY')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'uuid'                   => 'required|uuid|exists:pengajuan_sekolahs,uuid',
            'dokumen_syarat'         => 'required|array',
            'dokumen_syarat.*.nama'  => 'required|string',
            'dokumen_syarat.*.url'   => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $pengajuan = PengajuanSekolah::where('uuid', $request->uuid)->first();
            
            // Simpan array URL asli ke pengajuan_sekolahs untuk verifikasi
            $pengajuan->update([
                'dokumen_syarat' => $request->dokumen_syarat,
                'status'         => 'Verifikasi Berkas'
            ]);

            // --- LOGIKA BARU: HAPUS TOTAL DAN BUAT ULANG SEMUA ARSIP ---
            
            // 1. Hapus semua record arsip lama di database
            $pengajuan->dokumenLayanan()->delete();

            // 2. Hapus direktori file lama untuk menghemat storage
            $archivePath = "arsip_dokumen/{$pengajuan->id}";
            if (Storage::disk('public')->exists($archivePath)) {
                Storage::disk('public')->deleteDirectory($archivePath);
            }

            // 3. Arsipkan KEMBALI surat permohonan utama
            if ($pengajuan->file_permohonan) {
                DownloadAndArchiveActionJob::dispatch(
                    $pengajuan->file_permohonan,
                    $pengajuan->id,
                    'Surat Permohonan'
                );
            }
            
            // 4. Arsipkan semua dokumen persyaratan yang baru dikirim
            foreach ($request->dokumen_syarat as $dokumen) {
                // Hanya proses jika URL nya ada dan tidak kosong
                if (!empty($dokumen['url'])) {
                    DownloadAndArchiveActionJob::dispatch(
                        $dokumen['url'],
                        $pengajuan->id,
                        $dokumen['nama']
                    );
                }
            }
            // --- AKHIR LOGIKA BARU ---

            return response()->json(['status' => 'success', 'message' => 'Berkas berhasil diterima. Proses arsip akan berjalan di latar belakang.']);
        } catch (\Exception $e) {
            Log::error("API KCD Berkas Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Server Error.'], 500);
        }
    }

    public function handle(Request $request) {
        return response()->json(['message' => 'Endpoint Ready']);
    }
}