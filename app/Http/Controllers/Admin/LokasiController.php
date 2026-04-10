<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LokasiKantor;

class LokasiController extends Controller
{
    public function index()
    {
        // Ambil data pertama, atau buat baru jika kosong
        $lokasi = LokasiKantor::first();

        if (!$lokasi) {
            $lokasi = new LokasiKantor();
            // Default koordinat (Contoh: Puskesmas Sarjo dari chat Anda)
            $lokasi->latitude = '-0.841867185790068'; 
            $lokasi->longitude = '119.89267195242888';
            //-0.8419100965007199, 119.89265049475782
            //-0.84192082 41783129, 119.89265049 475782
            $lokasi->radius_meter = 25;
        }

        return view('lokasi.index', compact('lokasi'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|numeric|min:10',
        ]);

        // Update atau Create data (ID 1)
        LokasiKantor::updateOrCreate(
            ['id' => 1], // Cari ID 1
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius_meter' => $request->radius_meter,
                'alamat' => $request->alamat
            ]
        );

        return redirect()->back()->with('success', 'Lokasi kantor berhasil diperbarui!');
    }
}