<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Str; 

class TerimaPengajuanController extends Controller
{
    /**
     * STEP 1: Terima Data Awal (Identitas & Surat Permohonan)
     * Alamat: POST /api/v1/request-awal
     */
    public function terimaRequestAwal(Request $request)
    {
        // 1. Validasi API Key
        if ($request->header('X-API-KEY') !== env('API_SECRET_KEY')) {
            Log::warning('API KCD: Unauthorized access attempt', ['ip' => $request->ip()]);
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        // 2. Validasi Input
        $validator = Validator::make($request->all(), [
            'uuid'            => 'required|uuid',
            'npsn'            => 'required|string|max:10',
            'nama_sekolah'    => 'required|string|max:255',
            'nama_guru'       => 'required|string|max:255',
            'nip'             => 'nullable|string|max:50',
            'kategori'        => 'required|string|max:100', 
            'judul'           => 'required|string|max:255',
            'file_permohonan' => 'required|string', 
            'url_callback'    => 'required|url', 
        ]);

        if ($validator->fails()) {
            Log::error("API KCD: Validasi Gagal", $validator->errors()->toArray());
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $kategoriSlug = Str::slug($request->kategori); 

            // 3. Simpan / Update ke Database KCD
            // Menggunakan updateOrCreate agar jika sekolah kirim ulang (retry), data tidak duplikat
            PengajuanSekolah::updateOrCreate(
                ['uuid' => $request->uuid],
                [
                    'npsn'            => $request->npsn,
                    'nama_sekolah'    => $request->nama_sekolah,
                    'nama_guru'       => $request->nama_guru,
                    'nip'             => $request->nip,
                    'kategori'        => $kategoriSlug,
                    'judul'           => $request->judul,
                    'file_permohonan' => $request->file_permohonan, 
                    'url_callback'    => $request->url_callback,
                    'status'          => 'Proses', // Tahap awal untuk diperiksa verifikator KCD
                ]
            );

            Log::info("API KCD: Data Masuk Berhasil - UUID: {$request->uuid}");
            
            return response()->json([
                'status' => 'success', 
                'message' => 'Data awal berhasil diterima server KCD.'
            ]);

        } catch (\Exception $e) {
            Log::error("API KCD Fatal Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * STEP 2: Terima Berkas Persyaratan
     * Alamat: POST /api/v1/terima-berkas
     */
    public function terimaBerkas(Request $request)
    {
        if ($request->header('X-API-KEY') !== env('API_SECRET_KEY')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'uuid'           => 'required|uuid|exists:pengajuan_sekolahs,uuid',
            'dokumen_syarat' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = PengajuanSekolah::where('uuid', $request->uuid)->first();
            $data->update([
                'dokumen_syarat' => $request->dokumen_syarat, 
                'status'         => 'Verifikasi Berkas' 
            ]);

            return response()->json(['status' => 'success', 'message' => 'Berkas berhasil diterima.']);
        } catch (\Exception $e) {
            Log::error("API KCD Berkas Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Server Error.'], 500);
        }
    }

    public function handle(Request $request) {
        return response()->json(['message' => 'Endpoint Ready']);
    }
}