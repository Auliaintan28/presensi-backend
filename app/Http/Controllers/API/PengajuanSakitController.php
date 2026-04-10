<?php
// app/Http/Controllers/Api/PengajuanSakitController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\PengajuanSakit;
use Carbon\Carbon; // <-- Import Carbon untuk hitung hari

class PengajuanSakitController extends Controller
{
    public function store(Request $request)
    {
        // Hitung total hari (tanggal selesai - tanggal mulai) + 1
        $totalHari = 0;
        if ($request->tanggal_mulai && $request->tanggal_selesai) {
             $totalHari = Carbon::parse($request->tanggal_mulai)
                                 ->diffInDays(Carbon::parse($request->tanggal_selesai)) + 1;
        }

        // Validasi
        $validator = Validator::make($request->all(), [
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'required|string|min:5',
            
            // Validasi SKD: WAJIB JIKA $totalHari > 1
            'file_lampiran'   => [
                Rule::requiredIf($totalHari > 1),
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
            ], 422);
        }

        $filePath = null;

        // Proses upload file jika ada
        if ($request->hasFile('file_lampiran')) {
            try {
                $path = $request->file('file_lampiran')->store('public/lampiran_sakit');
                $filePath = Storage::url($path); 

            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Gagal mengupload file SKD',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        // Simpan ke database
        try {
            $sakit = PengajuanSakit::create([
                'user_id'         => $request->user()->id,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'keterangan'      => $request->keterangan,
                'file_lampiran'   => $filePath,
                'status'          => 'diajukan',
            ]);

            return response()->json([
                'message' => 'Pengajuan sakit berhasil dikirim',
                'data' => $sakit
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data pengajuan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}