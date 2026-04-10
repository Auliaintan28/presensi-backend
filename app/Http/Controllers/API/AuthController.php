<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'errors' => [
                    'email' => ['Email tidak terdaftar']
                ]
            ], 401);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()
                ->json([
                    'errors' => [
                        'password' => ['Password salah']
                    ]
                ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        //$user = User::where('email', $request['email'])->firstOrFail();

        // 3. --- PENTING: Cek Role ---
        // Hanya 'pegawai' yang boleh login ke aplikasi HP
        if ($user->role !== 'pegawai') {
        Auth::logout();
        return response()->json([
            'errors' => [
                'email' => ['Akses ditolak. Akun ini bukan pegawai.']
            ]
        ], 403);
    }
        // 4. Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        $userData = $user->toArray();
        $userData['jabatan'] = is_array($user->jabatan)
            ? implode(', ', $user->jabatan)
            : $user->jabatan;

        // 5. Kirim respons yang bersih
        return response()
            ->json([
                'success'       => true,
                'message'       => 'Hi ' . $user->name . ', selamat datang!',
                'access_token'  => $token,
                'token_type'    => 'Bearer',
                'user'          => $userData
            ]);
    }

    // method for user logout and delete token
    public function logout()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->tokens()->delete();

        return [
            'message' => 'Anda telah berhasil logout'
        ];
    }
}
