<?php
// app/Http/Controllers/Api/PengajuanIzinJamController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanIzinJam;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule; // <-- Import Rule

class PengajuanIzinJamController extends Controller
{
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'jenis_izin'    => 'required|string|max:100',
            'tanggal'       => 'required|date',
            'keterangan'    => 'required|string|min:5',
            'file_lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            // --- Validasi Waktu yang Baru ---
            // 'jam_mulai' wajib ada JIKA "Izin Pulang Cepat" ATAU "Izin Keluar Kantor"
            'jam_mulai'     => [
                Rule::requiredIf(function () {
                    return request()->jenis_izin == 'Izin Pulang Cepat' ||
                           request()->jenis_izin == 'Izin Keluar Kantor (Sementara)';
                }),
                'nullable',
                'date_format:H:i',
            ],
            // 'jam_selesai' wajib ada JIKA "Izin Datang Terlambat" ATAU "Izin Keluar Kantor"
           'jam_selesai'   => [
                Rule::requiredIf(function () {
                    return request()->jenis_izin == 'Izin Datang Terlambat' ||
                           request()->jenis_izin == 'Izin Keluar Kantor (Sementara)';
                }),
                'nullable',
                'date_format:H:i',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Proses upload file (jika ada)
        $filePath = null;
        if ($request->hasFile('file_lampiran')) {
            try {
                $path = $request->file('file_lampiran')->store('public/lampiran_izin_jam');
                $filePath = Storage::url($path);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Gagal upload file', 'error' => $e->getMessage()], 500);
            }
        }

        // Simpan ke database
        try {
            $izin = PengajuanIzinJam::create([
                'user_id'         => $request->user()->id,
                'jenis_izin'      => $request->jenis_izin,
                'tanggal'         => $request->tanggal,
                'jam_mulai'       => $request->jam_mulai, // Akan null jika izin datang terlambat
                'jam_selesai'     => $request->jam_selesai, // Akan null jika izin pulang cepat
                'keterangan'      => $request->keterangan,
                'file_lampiran'   => $filePath,
                'status'          => 'diajukan',
            ]);

            return response()->json(['message' => 'Pengajuan Izin Per Jam berhasil dikirim', 'data' => $izin], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan data', 'error' => $e->getMessage()], 500);
        }
    }
}