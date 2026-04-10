<?php
// app/Http/Controllers/Api/ProfileController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // <-- Untuk cek password
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // <-- Untuk validasi email unik
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Update data profil (Email, No HP, Foto)
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id), // Email harus unik, KECUALI email dia sendiri
            ],
            'no_hp' => 'nullable|string|max:15',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // maks 2MB
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $filePath = $user->foto_profil; // Ambil path foto lama

        // Proses upload file jika ada
        if ($request->hasFile('foto_profil')) {
            try {

                // Hapus foto lama jika ada
                if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                    Storage::disk('public')->delete($user->foto_profil);
                }

                // Upload foto baru
                $path = $request->file('foto_profil')->store('foto_profil', 'public');

                // Simpan path ke database
                $filePath = $path;
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Gagal mengupload file',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        // Simpan ke database
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $user->update([
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'foto_profil' => $filePath,
            ]);

            // Ambil data user yang terbaru (termasuk NIP, Jabatan, dll)
            $updatedUser = User::find($user->id);

            return response()->json([
                'message' => 'Profil berhasil diperbarui',
                'data' => $updatedUser->toArray()
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui profil', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Ganti Password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:6|confirmed', // 'confirmed' akan cek 'password_baru_confirmation'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        // 1. Cek apakah password lama cocok
        if (!Hash::check($request->password_lama, $user->password)) {
            return response()->json(['message' => 'Password lama tidak sesuai'], 401);
        }

        // 2. Jika cocok, update password baru
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $user->update([
                'password' => Hash::make($request->password_baru)
            ]);

            return response()->json(['message' => 'Password berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui password', 'error' => $e->getMessage()], 500);
        }
    }
}
