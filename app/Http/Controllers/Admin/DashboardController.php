<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanCuti;
use App\Models\PengajuanDinas;
use App\Models\PengajuanIzinHarian;
use App\Models\PengajuanSakit;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Tampilkan Halaman Login
    public function index()
    {
        $today = Carbon::today();

        // 1. Hitung Statistik Ringkas (Card Atas)
        $totalPegawai = User::where('role', 'pegawai')->count();

        $hadirHariIni = Presensi::whereDate('tanggal', Carbon::today())->count();

        // Hitung yang sedang Izin/Sakit hari ini
        $izinSakitHariIni = PengajuanSakit::whereDate('tanggal_mulai', '<=', Carbon::today())
            ->whereDate('tanggal_selesai', '>=', Carbon::today())
            ->where('status', 'disetujui')
            ->count()
            +
            PengajuanIzinHarian::whereDate('tanggal_mulai', '<=', Carbon::today())
            ->whereDate('tanggal_selesai', '>=', Carbon::today())
            ->where('status', 'disetujui')
            ->count();

        // 2. Card "Perlu Persetujuan" (Jumlah Total Pending)
        $totalPending = PengajuanCuti::where('status', 'diajukan')->count() +
            PengajuanSakit::where('status', 'diajukan')->count() +
            PengajuanIzinHarian::where('status', 'diajukan')->count() +
            PengajuanDinas::where('status', 'diajukan')->count();

        // 3. Tabel "Terlambat Hari Ini" (Ambil 5 orang terbaru)
        $pegawaiTerlambat = Presensi::with('user')
            ->whereDate('tanggal', $today)
            ->where('is_terlambat', true)
            ->orderBy('masuk', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPegawai',
            'hadirHariIni',
            'izinSakitHariIni',
            'totalPending',
            'pegawaiTerlambat'
        ));
    }

    // Tampilkan Form Login
    public function showLogin()
    {
        return view('admin.login');
    }

    // Proses Login
    public function processLogin(Request $request)
    {
        // 1️⃣ Validasi dasar
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);

        // 2️⃣ Cek apakah email terdaftar
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->withErrors(['email' => 'Email tidak terdaftar'])
                ->withInput();
        }

        // 3️⃣ Cek password
        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()
                ->withErrors(['password' => 'Password salah'])
                ->withInput();
        }

        // 4️⃣ Cek role admin
        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            return back()
                ->withErrors(['email' => 'Akses ditolak. Akun ini bukan admin'])
                ->withInput();
        }

        // 5️⃣ Login berhasil
        $request->session()->regenerate();
        return redirect()->intended('admin/dashboard');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
