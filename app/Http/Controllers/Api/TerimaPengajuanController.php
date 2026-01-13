<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Str; // Tambah helper Str

class TerimaPengajuanController extends Controller
{
    /**
     * STEP 1: Terima Data Awal (Judul & Identitas)
     * Tugas: Mencatat data baru dari sekolah. SK BELUM ADA di tahap ini.
     */
    public function terimaRequestAwal(Request $request)
    {
        // 1. Cek API Key
        if ($request->header('X-API-KEY') !== env('API_SECRET_KEY')) {
            Log::warning('Unauthorized API Access Attempt from IP: ' . $request->ip());
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        // 2. Validasi Input
        $validator = Validator::make($request->all(), [
            'uuid'         => 'required|uuid',
            'npsn'         => 'required|string|max:10',
            'nama_sekolah' => 'required|string|max:255',
            'nama_guru'    => 'required|string|max:255',
            'nip'          => 'nullable|string|max:50',
            'kategori'     => 'required|string|max:100', 
            'judul'        => 'required|string|max:255',
            'url_callback' => 'required|url', 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            // 3. Normalisasi Kategori jadi Slug (Biar Rapi di DB)
            // Contoh: "Kenaikan Pangkat" -> "kenaikan-pangkat"
            $kategoriSlug = Str::slug($request->kategori); 

            // 4. Simpan / Update Data
            PengajuanSekolah::updateOrCreate(
                ['uuid' => $request->uuid],
                [
                    'npsn'         => $request->npsn,
                    'nama_sekolah' => $request->nama_sekolah,
                    'nama_guru'    => $request->nama_guru,
                    'nip'          => $request->nip,
                    'kategori'     => $kategoriSlug, // Simpan format slug
                    'judul'        => $request->judul,
                    'url_callback' => $request->url_callback,
                    'status'       => 'Proses', 
                ]
            );

            Log::info("API: Pengajuan Diterima - Kategori: {$kategoriSlug}, UUID: {$request->uuid}");
            
            return response()->json([
                'status' => 'success', 
                'message' => 'Data awal berhasil diterima server KCD.'
            ]);

        } catch (\Exception $e) {
            Log::error("API Error (RequestAwal): " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Server Error.'], 500);
        }
    }

    /**
     * STEP 2: Terima Update Berkas
     * Tugas: Menerima file syarat. SK JUGA BELUM ADA di tahap ini.
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
            
            // Update Berkas & Status
            $data->update([
                'dokumen_syarat' => $request->dokumen_syarat, 
                'status'         => 'Verifikasi Berkas' 
            ]);

            Log::info("API: Berkas Diterima - UUID: {$request->uuid}");
            
            return response()->json([
                'status' => 'success',
                'message' => 'Berkas berhasil diterima.'
            ]);

        } catch (\Exception $e) {
            Log::error("API Error (TerimaBerkas): " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Server Error.'], 500);
        }
    }
}