<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SyncApiAuth;
use App\Http\Controllers\Api\SchoolSyncController;

// --- TAMBAHAN: Controller Penerima Pengajuan ---
use App\Http\Controllers\Api\TerimaPengajuanController; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Sync Data (Sekolah -> KCD)
// Endpoint ini menerima data dari aplikasi sekolah
Route::post('/sync/{table}', [SchoolSyncController::class, 'handle'])
     ->middleware(SyncApiAuth::class);

     Route::prefix('v1')->group(function () {
    
        // 1. Menerima permohonan awal dan surat permohonan dari sekolah
        // URL: http://alamat-kcd.test/api/v1/request-awal
        Route::post('/request-awal', [TerimaPengajuanController::class, 'terimaRequestAwal']);
        
        // 2. Menerima unggahan berkas persyaratan yang sudah dilengkapi sekolah
        // URL: http://alamat-kcd.test/api/v1/terima-berkas
        Route::post('/terima-berkas', [TerimaPengajuanController::class, 'terimaBerkas']);
        
        // 3. Endpoint tambahan jika diperlukan untuk sinkronisasi lainnya
        Route::post('/terima-pengajuan', [TerimaPengajuanController::class, 'handle']);
    });

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');