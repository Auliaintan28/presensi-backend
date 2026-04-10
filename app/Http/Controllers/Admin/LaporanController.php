<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Presensi;
use App\Models\PengajuanCuti;
use App\Models\PengajuanSakit;
use App\Models\PengajuanIzinHarian;
use App\Models\PengajuanDinas;
use Carbon\Carbon;
// Tambahkan Import Ini
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function cetak(Request $request) // Atau ganti nama jadi 'proses' sesuai form action
    {
        // 1. Validasi
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // 2. Persiapan Gambar (Logo) untuk PDF/View
        $logoPuskesmas = $this->imageToBase64(public_path('assets/logo-puskesmas.png'));
        $logoKabupaten = $this->imageToBase64(public_path('assets/logo-kabupaten.png'));

        // 3. Ambil Data Pegawai & Hitung Statistik
        $pegawai = User::where('role', 'pegawai')->orderBy('name')->get();

        foreach ($pegawai as $p) {
            // A. Presensi
            $presensi = Presensi::where('user_id', $p->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();

            $p->total_hadir = $presensi->count();
            $p->total_terlambat = $presensi->where('is_terlambat', true)->count();

            // B. Pengajuan (Cuti/Sakit/Izin/Dinas)
            $p->total_cuti = $this->hitungPengajuan(PengajuanCuti::class, $p->id, $bulan, $tahun);
            $p->total_sakit = $this->hitungPengajuan(PengajuanSakit::class, $p->id, $bulan, $tahun);
            $p->total_izin  = $this->hitungPengajuan(PengajuanIzinHarian::class, $p->id, $bulan, $tahun);
            $p->total_dinas = $this->hitungPengajuan(PengajuanDinas::class, $p->id, $bulan, $tahun);
        }

        $namaBulan = Carbon::create()->month($bulan)->locale('id')->isoFormat('MMMM');

        // Kumpulkan data dalam array agar rapi saat dipassing
        $data = [
            'pegawai'       => $pegawai,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'namaBulan'     => $namaBulan,
            'logoKabupaten' => $logoKabupaten,
            'logoPuskesmas' => $logoPuskesmas
        ];

        return view('laporan.cetak', $data);
    }

    // --- Helper Functions untuk mempersingkat kode ---

    private function imageToBase64($path)
    {
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return '';
    }

    private function hitungPengajuan($model, $userId, $bulan, $tahun)
    {
        return $model::where('user_id', $userId)
            ->where('status', 'disetujui')
            ->whereMonth('tanggal_mulai', $bulan)
            ->whereYear('tanggal_mulai', $tahun)
            ->count();
    }

    public function unduhPdf(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // 2. Persiapan Gambar (Logo) untuk PDF/View
        $logoPuskesmas = $this->imageToBase64(public_path('assets/logo-puskesmas.png'));
        $logoKabupaten = $this->imageToBase64(public_path('assets/logo-kabupaten.png'));

        // 3. Ambil Data Pegawai & Hitung Statistik
        $pegawai = User::where('role', 'pegawai')->orderBy('name')->get();

        foreach ($pegawai as $p) {
            // A. Presensi
            $presensi = Presensi::where('user_id', $p->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();

            $p->total_hadir = $presensi->count();
            $p->total_terlambat = $presensi->where('is_terlambat', true)->count();

            // B. Pengajuan (Cuti/Sakit/Izin/Dinas)
            $p->total_cuti = $this->hitungPengajuan(PengajuanCuti::class, $p->id, $bulan, $tahun);
            $p->total_sakit = $this->hitungPengajuan(PengajuanSakit::class, $p->id, $bulan, $tahun);
            $p->total_izin  = $this->hitungPengajuan(PengajuanIzinHarian::class, $p->id, $bulan, $tahun);
            $p->total_dinas = $this->hitungPengajuan(PengajuanDinas::class, $p->id, $bulan, $tahun);
        }

        $namaBulan = Carbon::create()->month($bulan)->locale('id')->isoFormat('MMMM');

        // Kumpulkan data dalam array agar rapi saat dipassing
        $data = [
            'pegawai'       => $pegawai,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'namaBulan'     => $namaBulan,
            'logoKabupaten' => $logoKabupaten,
            'logoPuskesmas' => $logoPuskesmas
        ];

        // Selalu generate & download PDF:
        $pdf = Pdf::loadView('laporan.cetak', $data);
        $pdf->setPaper('A4', 'landscape');
       
        // Ambil timestamp saat ini (detik)
        $timestamp = time();

        // Atau gunakan format tanggal yang lebih mudah dibaca
        $timestampFormatted = now()->format('Ymd_His');

        // Gunakan nama file yang unik:
        $fileName = 'Laporan-Presensi-' . $namaBulan . '-' . $tahun . '-' . $timestampFormatted . '.pdf';

        return $pdf->download($fileName);
    }
}
