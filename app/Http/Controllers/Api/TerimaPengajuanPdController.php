<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Jobs\DownloadAndArchiveActionJob; 

class TerimaPengajuanPdController extends Controller
{
    /**
     * STEP 1: Terima Data Awal Peserta Didik (Identitas & URL Surat)
     */
    public function terimaRequestAwalPd(Request $request)
    {
        // 1. Cek API Key
        if ($request->header('X-API-KEY') !== env('API_SECRET_KEY')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        // 2. Validasi: Menggunakan NISN dan Nama Siswa
        $validator = Validator::make($request->all(), [
            'uuid'            => 'required|uuid',
            'cadisdik_id'     => 'nullable|uuid', // 🔥 UUID wilayah dari sekolah
            'npsn'            => 'required|string|max:10',
            'nama_sekolah'    => 'required|string|max:255',
            'nama_siswa'      => 'required|string|max:255', // Nama Siswa
            'nisn'            => 'required|string|max:20',  // NISN Siswa
            'kategori'        => 'required|string|max:100',
            'judul'           => 'required|string|max:255',
            'file_permohonan' => 'required|url',
            'url_callback'    => 'required|url',
            'data_siswa_json' => 'nullable|array', // Data tambahan (tgl lahir, gender, dll)
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            // 🔥 OTOMATIS CARI instansi_id (Angka) dari cadisdik_id (UUID) 🔥
            $instansiId = null;
            if ($request->filled('cadisdik_id')) {
                $instansiId = \App\Models\Instansi::where('cadisdik_id', $request->cadisdik_id)->value('id');
            }

            // Jika tidak ketemu via UUID, coba cari via NPSN Sekolah yang sudah ada di database KCD
            if (!$instansiId) {
                $instansiId = \App\Models\Sekolah::where('npsn', $request->npsn)->value('instansi_id');
            }

            // 3. Simpan ke database KCD
            $pengajuan = PengajuanSekolah::updateOrCreate(
                ['uuid' => $request->uuid],
                [
                    'instansi_id'     => $instansiId, // 🔥 Simpan hasil pencarian otomatis
                    'npsn'            => $request->npsn,
                    'nama_sekolah'    => $request->nama_sekolah,
                    'nama_guru'       => $request->nama_siswa, // Simpan ke kolom nama_guru sebagai pengaju
                    'nip'             => $request->nisn,       // Simpan NISN ke kolom NIP (atau kolom nisn jika ada)
                    'tipe_pengaju'    => 'PD',                 // 🔥 Tandai sebagai Peserta Didik
                    'kategori'        => $request->kategori,
                    'judul'           => $request->judul,
                    'file_permohonan' => $request->file_permohonan,
                    'url_callback'    => $request->url_callback,
                    'data_siswa_json' => $request->data_siswa_json, // Simpan snapshot data siswa
                    'status'          => 'Proses',
                ]
            );

            // 4. Dispatch job untuk download surat permohonan sekolah
            if ($request->filled('file_permohonan')) {
                DownloadAndArchiveActionJob::dispatch(
                    $request->file_permohonan,
                    $pengajuan->id,
                    'Surat Permohonan'
                );
            }

            Log::info("API KCD: Pengajuan PD Masuk - UUID: {$request->uuid}");
            return response()->json(['status' => 'success', 'message' => 'Data awal siswa berhasil diterima.']);

        } catch (\Exception $e) {
            Log::error("API KCD PD Fatal Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * STEP 2: Terima Berkas Persyaratan Siswa (Sama dengan GTK tapi spesifik PD)
     */
    public function terimaBerkasPd(Request $request)
    {
        if ($request->header('X-API-KEY') !== env('API_SECRET_KEY')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'uuid_sekolah'   => 'required|uuid|exists:pengajuan_sekolahs,uuid',
            'dokumen_syarat' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $pengajuan = PengajuanSekolah::where('uuid', $request->uuid_sekolah)->first();
            
            // Update daftar syarat di database
            $pengajuan->update([
                'dokumen_syarat' => $request->dokumen_syarat,
                'status'         => 'Verifikasi Berkas'
            ]);

            // Bersihkan arsip lama jika sekolah mengirim ulang perbaikan
            $pengajuan->dokumenLayanan()->delete();
            $archivePath = "arsip_dokumen/{$pengajuan->id}";
            if (Storage::disk('public')->exists($archivePath)) {
                Storage::disk('public')->deleteDirectory($archivePath);
            }

            // Arsipkan ulang semua file (Surat Utama + Syarat)
            DownloadAndArchiveActionJob::dispatch($pengajuan->file_permohonan, $pengajuan->id, 'Surat Permohonan');

            foreach ($request->dokumen_syarat as $dokumen) {
                if (!empty($dokumen['url'])) {
                    DownloadAndArchiveActionJob::dispatch($dokumen['url'], $pengajuan->id, $dokumen['nama']);
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Seluruh berkas persyaratan siswa berhasil diterima.']);
        } catch (\Exception $e) {
            Log::error("API KCD PD Berkas Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Server Error.'], 500);
        }
    }
}