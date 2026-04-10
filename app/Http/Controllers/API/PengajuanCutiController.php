<?php
// app/Http/Controllers/Api/PengajuanCutiController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // <-- Penting untuk file
use Illuminate\Validation\Rule; // <-- Penting untuk validasi dinamis
use App\Models\PengajuanCuti;
use App\Models\JenisCuti;

class PengajuanCutiController extends Controller
{
    /**
     * Menyimpan pengajuan cuti baru dari Flutter
     */
    public function store(Request $request)
    {
        // 1. Ambil data Jenis Cuti untuk validasi lampiran
        // Kita butuh ID jenis cuti yang 'perlu_lampiran' == true
        $jenisCutiWajibLampiran = JenisCuti::where('perlu_lampiran', true)
                                         ->pluck('id')
                                         ->toArray();
                                         
        // 2. Validasi input
        $validator = Validator::make($request->all(), [
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'id_jenis_cuti'   => 'required|exists:jenis_cuti,id',
            'keterangan'      => 'required|string|min:5',
            
            // Validasi file_lampiran:
            // - WAJIB jika id_jenis_cuti ada di dalam array $jenisCutiWajibLampiran
            // - Boleh null jika tidak
            // - Jika ada, harus file gambar/pdf maks 2MB
            'file_lampiran'   => [
                Rule::requiredIf(function () use ($request, $jenisCutiWajibLampiran) {
                    return in_array($request->id_jenis_cuti, $jenisCutiWajibLampiran);
                }),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048', // 2MB
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        $filePath = null;

        // 3. Proses upload file jika ada
        if ($request->hasFile('file_lampiran')) {
            try {
                // Simpan di folder 'storage/app/public/lampiran_cuti'
                // Nama file akan di-generate unik
                $path = $request->file('file_lampiran')->store('public/lampiran_cuti');
                
                // Ubah path agar bisa diakses public
                // Hasil: 'storage/lampiran_cuti/namafile.jpg'
                $filePath = Storage::url($path); 

            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Gagal mengupload file',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        // 4. Simpan ke database
        try {
            $cuti = PengajuanCuti::create([
                'user_id'         => $request->user()->id, // Ambil ID user yg login
                'jenis_cuti_id'   => $request->id_jenis_cuti,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'keterangan'      => $request->keterangan,
                'file_lampiran'   => $filePath,
                'status'          => 'diajukan', // Default status
            ]);

            // 5. Beri respons sukses
            return response()->json([
                'message' => 'Pengajuan cuti berhasil dikirim',
                'data' => $cuti
            ], 201); // 201 Created

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data pengajuan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            // Ambil ID user yang sedang login
            $userId = $request->user()->id;

            // Ambil data cuti, urutkan dari terbaru
            // 'with('jenisCuti')' -> Eager Loading untuk ambil nama jenis cuti
            $riwayat = PengajuanCuti::with('jenisCuti') 
                                ->where('user_id', $userId)
                                ->latest() // 'latest()' = order by created_at DESC
                                ->get();

            // Format data agar mudah dibaca oleh Flutter
            $formattedRiwayat = $riwayat->map(function ($item) {
                return [
                    'id' => $item->id,
                    'jenis' => $item->jenisCuti->nama_cuti, // Ambil 'nama_cuti' dari relasi
                    'tanggal_mulai' => $item->tanggal_mulai,
                    'tanggal_selesai' => $item->tanggal_selesai,
                    'status' => $item->status,
                    'keterangan' => $item->keterangan,
                    // Kirim URL lengkap jika ada
                    'file_lampiran' => $item->file_lampiran ? url($item->file_lampiran) : null, 
                ];
            });

            // Kirim respons sukses
            return response()->json([
                'message' => 'Riwayat cuti berhasil diambil',
                'data' => $formattedRiwayat
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil riwayat cuti',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}