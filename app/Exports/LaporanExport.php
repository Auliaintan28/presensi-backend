<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // Menggunakan view yang sama dengan cetak
        return view('laporan.cetak', $this->data);
    }

    public function title(): string
    {
        // Gunakan nama yang sangat singkat, misalnya 15 karakter
        return 'Rekap Presensi';
        
        // Atau jika ingin spesifik, Anda bisa ambil bulan dan tahun dari data:
        // return 'Presensi ' . substr($this->data['namaBulan'], 0, 3) . ' ' . $this->data['tahun'];
        // Contoh Output: Presensi Nov 2025
    }

    // Opsional: Style tambahan agar tabel Excel ada bordernya
    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}