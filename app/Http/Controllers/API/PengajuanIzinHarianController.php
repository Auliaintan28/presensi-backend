<?php
// app/Http/Controllers/Api/PengajuanIzinHarianController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanIzinHarian;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PengajuanIzinHarianController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_izin'      => 'required|string|max:100',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'required|string|min:5',
            'file_lampiran'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // WAJIB
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Proses upload file (sudah pasti ada)
        $filePath = null;
        try {
            $path = $request->file('file_lampiran')->store('public/lampiran_izin_harian');
            $filePath = Storage::url($path);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal upload file', 'error' => $e->getMessage()], 500);
        }

        // Simpan ke database
        try {
            $izin = PengajuanIzinHarian::create([
                'user_id'         => $request->user()->id,
                'jenis_izin'      => $request->jenis_izin,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'keterangan'      => $request->keterangan,
                'file_lampiran'   => $filePath,
                'status'          => 'diajukan',
            ]);

            return response()->json(['message' => 'Pengajuan Izin Harian berhasil dikirim', 'data' => $izin], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan data', 'error' => $e->getMessage()], 500);
        }
    }
}