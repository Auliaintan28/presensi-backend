<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;            // Import Wajib
use Illuminate\Pagination\LengthAwarePaginator; // Import Wajib
use App\Models\PengajuanCuti;
use App\Models\PengajuanSakit;
use App\Models\PengajuanIzinHarian;
use App\Models\PengajuanDinas;

class PersetujuanController extends Controller
{
    public function index()
    {
        // Parameter ke-2 adalah nama variabel page di URL (agar tab tidak bentrok)
        $pending = $this->getGabunganPengajuan(['diajukan'], 'page_pending');
        $riwayat = $this->getGabunganPengajuan(['disetujui', 'ditolak'], 'page_riwayat');

        return view('persetujuan.index', compact('pending', 'riwayat'));
    }

    // ... (Method show dan update biarkan tetap sama) ...
    public function show($jenis, $id) { 
        $pengajuan = null;
        switch ($jenis) {
            case 'Cuti': $pengajuan = PengajuanCuti::with('user')->find($id); break;
            case 'Sakit': $pengajuan = PengajuanSakit::with('user')->find($id); break;
            case 'Izin': $pengajuan = PengajuanIzinHarian::with('user')->find($id); break;
            case 'Dinas': $pengajuan = PengajuanDinas::with('user')->find($id); break;
        }
        if (!$pengajuan) return redirect()->route('persetujuan.index')->with('error', 'Data tidak ditemukan');
        $pengajuan->jenis = $jenis;
        return view('persetujuan.show', compact('pengajuan'));
    }

    public function update(Request $request, $id) {
        $request->validate([ 'jenis' => 'required', 'status' => 'required', 'catatan_admin' => 'nullable' ]);
        $model = null;
        switch ($request->jenis) {
            case 'Cuti': $model = PengajuanCuti::find($id); break;
            case 'Sakit': $model = PengajuanSakit::find($id); break;
            case 'Izin': $model = PengajuanIzinHarian::find($id); break;
            case 'Dinas': $model = PengajuanDinas::find($id); break;
        }
        if ($model) {
            $model->update(['status' => $request->status, 'catatan_admin' => $request->catatan_admin, 'admin_id' => Auth::id()]);
            return redirect()->route('persetujuan.index')->with('success', 'Status berhasil diperbarui.');
        }
        return back()->with('error', 'Gagal.');
    }

    // === FUNGSI BANTUAN PAGINATION MANUAL ===
    private function getGabunganPengajuan($statusArray, $pageName = 'page')
    {
        // 1. Ambil Semua Data
        $cuti = PengajuanCuti::with('user')->whereIn('status', $statusArray)->get()->map(function($item){ $item->jenis = 'Cuti'; return $item; });
        $sakit = PengajuanSakit::with('user')->whereIn('status', $statusArray)->get()->map(function($item){ $item->jenis = 'Sakit'; return $item; });
        $izin = PengajuanIzinHarian::with('user')->whereIn('status', $statusArray)->get()->map(function($item){ $item->jenis = 'Izin'; return $item; });
        $dinas = PengajuanDinas::with('user')->whereIn('status', $statusArray)->get()->map(function($item){ $item->jenis = 'Dinas'; return $item; });

        // 2. Gabung & Urutkan
        $merged = $cuti->concat($sakit)->concat($izin)->concat($dinas)->sortByDesc('created_at');

        // 3. Logika Pagination Manual
        $perPage = 10; // Jumlah data per halaman
        $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName); // Ambil halaman dari URL (misal ?page_pending=2)
        $currentItems = $merged->slice(($currentPage - 1) * $perPage, $perPage)->all();

        // 4. Buat Object Paginator
        $paginatedItems = new LengthAwarePaginator($currentItems, $merged->count(), $perPage);
        
        // Set URL agar link-nya benar
        $paginatedItems->setPath(request()->url());
        $paginatedItems->setPageName($pageName);

        return $paginatedItems;
    }
}