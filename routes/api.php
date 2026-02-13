<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SyncApiAuth;
use App\Http\Controllers\Api\SchoolSyncController;

// --- CONTROLLER PENERIMA PENGAJUAN ---
use App\Http\Controllers\Api\TerimaPengajuanController; 
use App\Http\Controllers\Api\TerimaPengajuanPdController; // 🔥 Tambahan untuk PD

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Sync Data Dasar (Sekolah -> KCD)
// Digunakan untuk sinkronisasi tabel Master (Guru, Siswa, dll)
Route::post('/sync/{table}', [SchoolSyncController::class, 'handle'])
     ->middleware(SyncApiAuth::class);

Route::prefix('v1')->group(function () {
    
    // =============================================================
    // LAYANAN GTK (GURU & TENAGA KEPENDIDIKAN)
    // =============================================================
    Route::prefix('gtk')->group(function () {
        // URL: http://alamat-kcd.test/api/v1/gtk/request-awal
        Route::post('/request-awal', [TerimaPengajuanController::class, 'terimaRequestAwal']);
        
        // URL: http://alamat-kcd.test/api/v1/gtk/terima-berkas
        Route::post('/terima-berkas', [TerimaPengajuanController::class, 'terimaBerkas']);
    });

    // =============================================================
    // LAYANAN PD (PESERTA DIDIK / SISWA)
    // =============================================================
    Route::prefix('pd')->group(function () {
        // URL: http://alamat-kcd.test/api/v1/pd/request-awal
        Route::post('/request-awal', [TerimaPengajuanPdController::class, 'terimaRequestAwalPd']);
        
        // URL: http://alamat-kcd.test/api/v1/pd/terima-berkas
        Route::post('/terima-berkas', [TerimaPengajuanPdController::class, 'terimaBerkasPd']);
    });

    // Endpoint cadangan/general jika diperlukan
    Route::post('/terima-pengajuan', [TerimaPengajuanController::class, 'handle']);
});

// Endpoint standar user Sanctum
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');