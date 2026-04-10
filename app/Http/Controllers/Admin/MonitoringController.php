<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LokasiKantor;
use Illuminate\Http\Request;
use App\Models\Presensi;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil tanggal dari filter, atau default hari ini
        $tanggal = $request->tanggal ?? Carbon::today()->toDateString();

        // 2. Ambil data presensi pada tanggal tersebut
        // Load relasi 'user' agar bisa ambil nama pegawai
        $presensi = Presensi::with('user')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('masuk', 'desc') // Yang baru absen muncul paling atas
            ->paginate(10);
        
        //pagination
        $presensi->appends(['tanggal' => $tanggal]);

        $lokasiKantor = LokasiKantor::find(1);

        return view('monitoring.index', compact('presensi', 'tanggal', 'lokasiKantor'));
    }
}