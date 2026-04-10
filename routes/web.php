<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\LokasiController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\PersetujuanController;
use App\Http\Controllers\Admin\ShiftController;

// Halaman Awal (Redirect ke Login)
Route::get('/', function () {
    return redirect('/login');
});

// Route untuk Login/Logout
Route::get('/login', [DashboardController::class, 'showLogin'])->name('login');
Route::post('/login', [DashboardController::class, 'processLogin']);
Route::post('/logout', [DashboardController::class, 'logout'])->name('logout');

// Group Route KHUSUS ADMIN (Dilindungi Middleware)
Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');

    Route::resource('pegawai', PegawaiController::class)->names('pegawai');

    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');

    Route::post('/jadwal/generate', [JadwalController::class, 'generate'])->name('jadwal.generate');

    Route::resource('admin/shift', ShiftController::class)->except(['create', 'edit', 'show']);

    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');

    Route::get('/persetujuan', [PersetujuanController::class, 'index'])->name('persetujuan.index');

    Route::get('/persetujuan/detail/{jenis}/{id}', [PersetujuanController::class, 'show'])->name('persetujuan.show');

    Route::post('/persetujuan/update/{id}', [PersetujuanController::class, 'update'])->name('persetujuan.update');

    Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');

    Route::post('/lokasi', [LokasiController::class, 'update'])->name('lokasi.update');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    Route::post('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');

    Route::get('/laporan/unduh-pdf', [LaporanController::class, 'unduhPdf'])->name('laporan.unduh.pdf');

});
