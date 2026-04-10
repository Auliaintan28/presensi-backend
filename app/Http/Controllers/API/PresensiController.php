<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Presensi;
use App\Models\PengajuanDinas;
use App\Models\PengajuanCuti;
use App\Models\PengajuanSakit;
use App\Models\PengajuanIzinHarian;
use App\Models\JadwalKerja;
use App\Models\LokasiKantor;
use App\Models\Shift;

class PresensiController extends Controller
{
    public function getPresensis(Request $request)
    {
        try {
            $user = Auth::user();
            $today = Carbon::today()->toDateString();

            // Ambil parameter dari URL (query string)
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            // 1. Siapkan Query Dasar
            $query = Presensi::where('user_id', $user->id)
                ->whereDate('tanggal', '!=', $today) // Tetap exclude hari ini agar tidak double di UI
                ->orderBy('tanggal', 'desc');

            // 2. Cek apakah ada filter?
            if ($startDate && $endDate) {
                // Jika ada filter, ambil sesuai range tanggal
                $query->whereBetween('tanggal', [$startDate, $endDate]);
                // Eksekusi query dengan filter
                $riwayatPresensi = $query->get();
            } else {
                // Jika TIDAK ada filter (Homepage mode), ambil 30 data saja
                $riwayatPresensi = $query->take(30)->get();
            }

            // 3. Format riwayat
            $formattedRiwayat = $riwayatPresensi->map(function ($item) {
                $pulangFormatted = null;
                if ($item->pulang !== null) {
                    $pulang = Carbon::parse($item->pulang)->locale('id');
                    $pulang->settings(['formatFunction' => 'translatedFormat']);
                    $pulangFormatted = $pulang->format('H:i');
                }

                $datetime = Carbon::parse($item->tanggal)->locale('id');
                $datetime->settings(['formatFunction' => 'translatedFormat']);

                $masuk = Carbon::parse($item->masuk)->locale('id');
                $masuk->settings(['formatFunction' => 'translatedFormat']);

                return [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'is_hari_ini' => false,
                    'tanggal' => $datetime->format('l, j F Y'),
                    'masuk' => $masuk->format('H:i'),
                    'pulang' => $pulangFormatted,
                    'latitude_datang' => $item->latitude_datang,
                    'longitude_datang' => $item->longitude_datang,
                    'latitude_pulang' => $item->latitude_pulang,
                    'longitude_pulang' => $item->longitude_pulang,
                    'is_terlambat' => (bool) $item->is_terlambat,
                    'is_pulang_cepat' => (bool) $item->is_pulang_cepat,
                    'created_at' => $item->created_at->toIso8601String(),
                    'updated_at' => $item->updated_at->toIso8601String(),
                ];
            });

            // 4. Gabungkan data
            $finalData = $formattedRiwayat;

            // Logika ini BAGUS: Hanya tampilkan "Hari Ini" jika User TIDAK sedang memfilter tanggal
            if (!$startDate && !$endDate) {
                $dataHariIni = $this->_getDataHariIni($user, $today);
                if ($dataHariIni) {
                    $finalData->prepend($dataHariIni);
                }
            }

            $jabatanString = is_array($user->jabatan) 
                ? implode(', ', $user->jabatan) 
                : $user->jabatan;

            // 2. Pastikan Foto Profil menggunakan URL lengkap (http://...)
            // Kita gunakan accessor 'foto_profil_url' yang sudah kita buat di Model User
            $fotoUrl = $user->foto_profil_url;

            $jadwalHariIni = JadwalKerja::with('shift')
                ->where('user_id', $user->id)
                ->where('tanggal', $today)
                ->first();

            $namaShift = "Libur / Tidak Ada Jadwal";
            $jamMasukShift = "-";
            $jamPulangShift = "-";

            if ($jadwalHariIni) {
                $namaShift = $jadwalHariIni->shift->nama_shift;
                $jamMasukShift = Carbon::parse($jadwalHariIni->shift->jam_masuk)->format('H:i');
                $jamPulangShift = Carbon::parse($jadwalHariIni->shift->jam_pulang)->format('H:i');
            }

            // Ambil Lokasi Kantor
            $lokasi = LokasiKantor::find(1);

            return response()->json([
                'success' => true,
                'message' => 'Sukses menampilkan data',
                'data' => $finalData,
                'foto_profil' => $user->foto_profil_url,
                'jabatan' => is_array($user->jabatan) ? implode(', ', $user->jabatan) : $user->jabatan,

                // TAMBAHAN DATA BARU UNTUK FLUTTER
                'info_jadwal' => [
                    'nama_shift' => $namaShift,
                    'jam_masuk' => $jamMasukShift,
                    'jam_pulang' => $jamPulangShift,
                ],

                'lokasi_kantor' => [
                    'latitude' => $lokasi->latitude ?? -0.841867185790068,
                    'longitude' => $lokasi->longitude ?? 119.89267195242888,
                    'radius' => $lokasi->radius_meter ?? 50,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    private function _getDataHariIni($user, $today)
    {
        $datetime = Carbon::parse($today)->locale('id');
        $datetime->settings(['formatFunction' => 'translatedFormat']);
        $todayFormatted = $datetime->format('l, j F Y');

        // 1. Cek Perjalanan Dinas
        $dinas = PengajuanDinas::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->first();

        if ($dinas) {
            return $this->_formatAbsen('DINAS', $dinas->tujuan, $todayFormatted);
        }

        // 2. Cek Cuti
        $cuti = PengajuanCuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->first();

        if ($cuti) {
            return $this->_formatAbsen('CUTI', 'Pengajuan Cuti Disetujui', $todayFormatted);
        }

        // 3. Cek Sakit
        $sakit = PengajuanSakit::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->first();

        if ($sakit) {
            return $this->_formatAbsen('SAKIT', $sakit->keterangan, $todayFormatted);
        }

        // 4. Cek Izin Harian
        $izin = PengajuanIzinHarian::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->first();

        if ($izin) {
            return $this->_formatAbsen('IZIN', $izin->jenis_izin, $todayFormatted);
        }

        // 5. Cek presensi normal
        $presensi = Presensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($presensi) {
            // Format jam pulang
            $pulangFormatted = null;
            if ($presensi->pulang !== null) {
                $pulang = Carbon::parse($presensi->pulang)->locale('id');
                $pulang->settings(['formatFunction' => 'translatedFormat']);
                $pulangFormatted = $pulang->format('H:i');
            }

            // Format jam masuk
            $masuk = Carbon::parse($presensi->masuk)->locale('id');
            $masuk->settings(['formatFunction' => 'translatedFormat']);

            return [
                'id' => $presensi->id,
                'user_id' => $presensi->user_id,
                'is_hari_ini' => true,
                'tanggal' => $todayFormatted,
                'masuk' => $masuk->format('H:i'),
                'pulang' => $pulangFormatted,
                'latitude_datang' => $presensi->latitude_datang,
                'longitude_datang' => $presensi->longitude_datang,
                'latitude_pulang' => $presensi->latitude_pulang,
                'longitude_pulang' => $presensi->longitude_pulang,
                'is_terlambat' => (bool) $presensi->is_terlambat,
                'is_pulang_cepat' => (bool) $presensi->is_pulang_cepat,
                'created_at' => $presensi->created_at->toIso8601String(),
                'updated_at' => $presensi->updated_at->toIso8601String(),
            ];
        }

        // 6. Jika tidak ada data sama sekali
        return null;
    }

    private function _formatAbsen($status, $keterangan, $todayFormatted)
    {
        return [
            'id' => 0,
            'user_id' => Auth::user()->id,
            'is_hari_ini' => true,
            'tanggal' => $todayFormatted,
            'masuk' => $status,
            'pulang' => $status,
            'latitude_datang' => null,
            'longitude_datang' => null,
            'latitude_pulang' => null,
            'longitude_pulang' => null,
            'is_terlambat' => false,
            'is_pulang_cepat' => false,
            'keterangan_tambahan' => $keterangan,
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];
    }

    function savePresensi(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $user = Auth::user();

        $now = now(); // sudah ikut timezone Makassar
        $today = $now->toDateString();
        $jamSekarangString = $now->format('H:i:s');

        // 1. CEK JADWAL SHIFT HARI INI
        $jadwal = JadwalKerja::with('shift')
            ->where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki jadwal kerja hari ini.',
                'data' => null
            ], 200);
        }

        $jamMasukShift = $jadwal->shift->jam_masuk;
        $jamPulangShift = $jadwal->shift->jam_pulang;

        if ($now->isFriday() && $jamMasukShift < '12:00:00') {
            $jamPulangShift = '14:30:00';
        }

        $presensi = Presensi::whereDate('tanggal', $today)
            ->where('user_id', $user->id)
            ->first();

        if ($presensi == null) {

            $isTerlambat = $jamSekarangString > $jamMasukShift;

            $presensi = Presensi::create([
                'user_id' => $user->id,
                'tanggal' => $today,
                'masuk' => $now,
                'is_terlambat' => $isTerlambat,
                'latitude_datang' => $request->latitude,
                'longitude_datang' => $request->longitude,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Presensi Masuk Tersimpan',
                'data' => $presensi
            ]);
        } else {

            if ($presensi->pulang !== null) {
                return response()->json([
                    'success' => false,
                    'message' => "Anda sudah melakukan presensi pulang",
                    'data' => null
                ]);
            }

            $isPulangCepat = $jamSekarangString < $jamPulangShift;

            $presensi->update([
                'pulang' => $now,
                'is_pulang_cepat' => $isPulangCepat,
                'latitude_pulang' => $request->latitude,
                'longitude_pulang' => $request->longitude
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Presensi Pulang Tersimpan',
                'data' => $presensi
            ]);
        }
    }

    public function getJadwalPiket(Request $request)
    {
        try {
            // Ambil tanggal dari request, kalau tidak ada pakai hari ini
            $tanggal = $request->query('tanggal') ?? Carbon::today()->toDateString();

            // Ambil Semua Shift yang ada di database
            $shifts = Shift::with(['jadwalKerja' => function ($query) use ($tanggal) {
                $query->where('tanggal', $tanggal)->with('user');
            }])
                ->where('nama_shift', 'NOT LIKE', '%Regular%')
                ->get();

            // Format datanya agar enak dibaca Flutter
            $dataJadwal = $shifts->map(function ($shift) {
                // Ambil list pegawai dari relasi jadwal_kerja
                $listPegawai = $shift->jadwalKerja->map(function ($jadwal) {
                    return [
                        'nama' => $jadwal->user->name,
                        'foto_profil' => $jadwal->user->foto_profil,
                        'jabatan' => $jadwal->user->jabatan,
                    ];
                });

                return [
                    'nama_shift' => $shift->nama_shift,
                    'jam_kerja' => Carbon::parse($shift->jam_masuk)->format('H:i') . ' - ' . Carbon::parse($shift->jam_pulang)->format('H:i'),
                    'total_pegawai' => $listPegawai->count(),
                    'pegawai' => $listPegawai
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data jadwal shift berhasil diambil',
                'data' => $dataJadwal,
                'tanggal_terpilih' => Carbon::parse($tanggal)->locale('id')->isoFormat('dddd, D MMMM Y')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
