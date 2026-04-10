<?php
// app/Http/Controllers/Api/PengajuanDinasController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanDinas; // <-- Ganti nama model
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PengajuanDinasController extends Controller
{
    public function store(Request $request)
    {
        // Validasi, semua field wajib diisi
        $validator = Validator::make($request->all(), [
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tujuan'          => 'required|string|min:5',
            'keterangan'      => 'required|string|min:5',
            'file_lampiran'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // WAJIB
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Proses upload file (sudah pasti ada)
        $filePath = null;
        try {
            // Simpan di folder 'lampiran_dinas'
            $path = $request->file('file_lampiran')->store('public/lampiran_dinas');
            $filePath = Storage::url($path);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal upload file SPT', 'error' => $e->getMessage()], 500);
        }

        // Simpan ke database
        try {
            $dinas = PengajuanDinas::create([ // <-- Ganti nama model
                'user_id'         => $request->user()->id,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'tujuan'          => $request->tujuan,
                'keterangan'      => $request->keterangan,
                'file_lampiran'   => $filePath,
                'status'          => 'diajukan',
            ]);

            return response()->json(['message' => 'Pengajuan Perjalanan Dinas berhasil dikirim', 'data' => $dinas], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan data', 'error' => $e->getMessage()], 500);
        }
    }
}