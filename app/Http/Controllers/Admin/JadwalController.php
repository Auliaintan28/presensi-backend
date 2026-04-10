<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shift;
use App\Models\JadwalKerja;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public function index()
    {
        // Ambil data pegawai urut nama
        $pegawai = User::where('role', 'pegawai')->orderBy('name')->get();
        // Ambil data shift
        $shifts = Shift::orderBy('jam_masuk')->get();

        return view('jadwal.index', compact('pegawai', 'shifts'));
    }

    public function generate(Request $request)
    {
        // 1. Naikkan batas waktu & memori agar tidak putus di tengah jalan
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        // 2. Validasi
        $request->validate([
            'user_ids' => 'required|array',
            'shift_id' => 'required|exists:shifts,id',
            'mode'     => 'required|string',
        ], [
            'shift_id.required' => 'Silakan pilih Shift terlebih dahulu.',
            'user_ids.required' => 'Anda belum memilih pegawai. Harap centang minimal satu.',
        ]);

        $shiftId = $request->shift_id;
        $userIds = $request->user_ids;
        $mode = $request->mode;

        // 3. Tentukan Rentang Tanggal
        if ($mode == 'reguler') {
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            $startDate = Carbon::create($tahun, $bulan, 1, 0, 0, 0);
            $endDate = $startDate->copy()->endOfMonth();
        } else {
            $startDate = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $endDate = Carbon::parse($request->tanggal_akhir)->endOfDay();
        }

        $period = CarbonPeriod::create($startDate, $endDate);
        $totalData = 0;

        // 4. Gunakan Transaction agar Data Aman (Semua tersimpan atau tidak sama sekali)
        DB::transaction(function () use ($period, $userIds, $shiftId, $mode, &$totalData) {

            $now = Carbon::now();

            foreach ($period as $date) {
                // Logic Skip Minggu (Reguler)
                if ($mode == 'reguler' && $date->isSunday()) {
                    continue;
                }

                $tanggalStr = $date->toDateString();

                // LANGKAH A: Hapus jadwal lama pada tanggal ini untuk user yang dipilih
                // Ini mencegah duplikat tanpa butuh setingan 'unique' di database
                JadwalKerja::whereIn('user_id', $userIds)
                    ->where('tanggal', $tanggalStr)
                    ->delete();

                // LANGKAH B: Siapkan data baru
                $dataInsert = [];
                foreach ($userIds as $userId) {
                    $dataInsert[] = [
                        'user_id'    => $userId,
                        'shift_id'   => $shiftId,
                        'tanggal'    => $tanggalStr,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // LANGKAH C: Simpan per Hari (Lebih ringan memori dibanding simpan sebulan sekaligus)
                if (!empty($dataInsert)) {
                    JadwalKerja::insert($dataInsert);
                    $totalData += count($dataInsert);
                }
            }
        });

        return redirect()->back()->with('success', "Berhasil menyimpan data shift");
    }
}
