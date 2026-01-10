<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SyncApiAuth;
use App\Http\Controllers\Api\GenericSyncController;

// --- TAMBAHAN: Controller Penerima Pengajuan ---
use App\Http\Controllers\Api\TerimaPengajuanController; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Sync Data Pokok (Existing)
Route::post('/sync/{entity}', [GenericSyncController::class, 'handleSync'])
     ->middleware(SyncApiAuth::class);

// 2. TERIMA PENGAJUAN DARI SEKOLAH (BARU)
// Ini endpoint yang ditembak oleh aplikasi Sekolah saat tombol kirim diklik
Route::post('/terima-pengajuan', [TerimaPengajuanController::class, 'handle']);
Route::post('/request-awal', [TerimaPengajuanController::class, 'terimaRequestAwal']);
Route::post('/terima-berkas', [TerimaPengajuanController::class, 'terimaBerkas']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');