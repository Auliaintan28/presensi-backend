<?php
// app/Http/Controllers/Api/RiwayatPengajuanController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// Impor semua model
use App\Models\PengajuanCuti;
use App\Models\PengajuanSakit;
use App\Models\PengajuanDinas;
use App\Models\PengajuanIzinHarian;
use App\Models\PengajuanIzinJam;

class RiwayatPengajuanController extends Controller
{
    /**
     * Mengambil semua riwayat pengajuan (gabungan) milik user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $semuaRiwayat = collect();

        // 1. Ambil Data Cuti (dengan relasi jenis cuti)
        $cuti = PengajuanCuti::with('jenisCuti', 'admin')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($item) {
                return $this->formatStandar(
                    $item->id,
                    'Cuti', 
                    $item->jenisCuti->nama_cuti, 
                    $item->tanggal_mulai,
                    $item->tanggal_selesai,
                    $item->status,
                    $item->created_at,
                    $item->keterangan,
                    $item->file_lampiran,
                    $item->catatan_admin,
                    $item->admin
                );
            });
        $semuaRiwayat = $semuaRiwayat->merge($cuti);

        // 2. Ambil Data Sakit
       $sakit = PengajuanSakit::with('admin')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($item) {
                return $this->formatStandar(
                    $item->id,
                    'Sakit', 
                    'Pengajuan Sakit', 
                    $item->tanggal_mulai,
                    $item->tanggal_selesai,
                    $item->status,
                    $item->created_at,
                    $item->keterangan,
                    $item->file_lampiran,
                    $item->catatan_admin,
                    $item->admin
                );
            });
        $semuaRiwayat = $semuaRiwayat->merge($sakit);

        // 3. Ambil Data Dinas
        $dinas = PengajuanDinas::with('admin')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($item) {
                return $this->formatStandar(
                    $item->id,
                    'Dinas', 
                    'Perjalanan Dinas', 
                    $item->tanggal_mulai,
                    $item->tanggal_selesai,
                    $item->status,
                    $item->created_at,
                    $item->keterangan . " (Tujuan: " . $item->tujuan . ")",
                    $item->file_lampiran,
                    $item->catatan_admin,
                    $item->admin
                );
            });
        $semuaRiwayat = $semuaRiwayat->merge($dinas);

        // 4. Ambil Data Izin Harian
        $izinHarian = PengajuanIzinHarian::with('admin')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($item) {
                return $this->formatStandar(
                    $item->id,
                    'Izin', 
                    $item->jenis_izin, 
                    $item->tanggal_mulai,
                    $item->tanggal_selesai,
                    $item->status,
                    $item->created_at,
                    $item->keterangan,
                    $item->file_lampiran,
                    $item->catatan_admin,
                    $item->admin
                );
            });
        $semuaRiwayat = $semuaRiwayat->merge($izinHarian);

        // 5. Ambil Data Izin Per Jam
        $izinJam = PengajuanIzinJam::with('admin')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($item) {
                return $this->formatStandar(
                    $item->id,
                    'Izin', 
                    $item->jenis_izin, 
                    $item->tanggal, 
                    $item->tanggal, 
                    $item->status,
                    $item->created_at,
                    $item->keterangan . " (Jam: " . $item->jam_mulai . " - " . $item->jam_selesai . ")",
                    $item->file_lampiran,
                    $item->catatan_admin,
                    $item->admin
                );
            });
        $semuaRiwayat = $semuaRiwayat->merge($izinJam);

        // 6. Lakukan Filtering (sesuai Q4)
        $filteredRiwayat = $semuaRiwayat;

        // Filter berdasarkan Status
        if ($request->has('status') && $request->status != 'Semua') {
            $filteredRiwayat = $filteredRiwayat->where('status', $request->status);
        }

        // Filter berdasarkan Tipe
        if ($request->has('tipe') && $request->tipe != 'Semua') {
            $filteredRiwayat = $filteredRiwayat->where('tipe', $request->tipe);
        }

        // 7. Lakukan Sorting (sesuai Q1)
        // Urutkan berdasarkan 'created_at' (kapan diajukan), terbaru di atas
        $sortedRiwayat = $filteredRiwayat->sortByDesc('created_at');

        return response()->json([
            'success' => true,
            'message' => 'Riwayat gabungan berhasil diambil',
            'data' => $sortedRiwayat->values()->all() // .values()->all() untuk reset index array
        ]);
    }

    /**
     * Helper untuk standarisasi format data
     */
    private function formatStandar($id, $tipe, $jenisPengajuan, $tglMulai, $tglSelesai, $status, $createdAt, $keterangan, $file, $catatanAdmin = null, $adminObj = null)
    {
        // Format Tanggal Pengajuan (Q2 - Kanan Atas)
        $tglPengajuan = Carbon::parse($createdAt)->locale('id_ID')->isoFormat('D MMM YYYY');
        
        // Format Rentang Tanggal (Q2 - Kiri Bawah)
        $tglMulaiF = Carbon::parse($tglMulai)->locale('id_ID')->isoFormat('D MMM');
        $tglSelesaiF = Carbon::parse($tglSelesai)->locale('id_ID')->isoFormat('D MMM YYYY');
        $rentangTanggal = $tglMulaiF . " - " . $tglSelesaiF;
        if ($tglMulai == $tglSelesai) {
            $rentangTanggal = $tglSelesaiF; // Jika 1 hari, tampilkan 1 tanggal
        }

        return [
            'id' => $id,
            'tipe' => $tipe,
            'jenis_pengajuan' => $jenisPengajuan,
            'rentang_tanggal' => $rentangTanggal,
            'tanggal_pengajuan' => $tglPengajuan,
            'status' => $status, // "diajukan", "disetujui", "ditolak"
            'keterangan' => $keterangan,
            'file_lampiran' => $file ? url($file) : null, // Kirim URL lengkap jika ada
            'created_at' => $createdAt->toIso8601String(), // Untuk sorting
            'catatan_admin' => $catatanAdmin, 
            // Cek apakah ada objek admin, jika ada ambil namanya
            'nama_admin' => $adminObj ? $adminObj->name : '-',
        ];
    }
}