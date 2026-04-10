<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\PengajuanCutiController;
use App\Http\Controllers\Api\PengajuanDinasController;
use App\Http\Controllers\Api\PengajuanIzinHarianController;
use App\Http\Controllers\Api\PengajuanIzinJamController;
use App\Http\Controllers\Api\PengajuanSakitController;
use App\Http\Controllers\API\PresensiController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RiwayatPengajuanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
//API route for login user
// Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);
// Route::post('/register', [AuthController::class, 'register']);
//API route for login user
Route::post('/login', [AuthController::class, 'login']);

//Protecting Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return auth()->user();
    });

    // API route for logout user
    // Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
    // Route::get('/get-presensi',  [App\Http\Controllers\API\PresensiController::class, 'getPresensis']);

    // Route::post('/save-presensi', [App\Http\Controllers\API\PresensiController::class, 'savePresensi']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/get-presensi',  [PresensiController::class, 'getPresensis']);
    Route::post('/save-presensi', [PresensiController::class, 'savePresensi']);
    Route::post('/cuti', [PengajuanCutiController::class, 'store']);
    Route::get('/cuti', [PengajuanCutiController::class, 'index']);
    Route::post('/sakit', [PengajuanSakitController::class, 'store']);
    Route::post('/izin-harian', [PengajuanIzinHarianController::class, 'store']);
    Route::post('/izin-jam', [PengajuanIzinJamController::class, 'store']);
    Route::post('/dinas', [PengajuanDinasController::class, 'store']);
    Route::get('/riwayat-gabungan', [RiwayatPengajuanController::class, 'index']);
    Route::post('/profile-update', [ProfileController::class, 'updateProfile']);
    Route::post('/password-update', [ProfileController::class, 'updatePassword']);
    Route::get('/jadwal-piket', [PresensiController::class, 'getJadwalPiket']);
});
