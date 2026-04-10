<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::orderBy('jam_masuk', 'asc')->get();
        return view('shift.index', compact('shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_shift' => 'required|string|max:50',
            'jam_masuk'  => 'required',
            'jam_pulang' => 'required',
        ], [
            'nama_shift.required' => 'Nama Shift wajib diisi.',
            'jam_masuk.required'  => 'Jam Masuk wajib diisi.',
            'jam_pulang.required' => 'Jam Pulang wajib diisi.',
        ]);

        Shift::create($request->all());

        return redirect()->back()->with('success', 'Data shift berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_shift' => 'required|string|max:50',
            'jam_masuk'  => 'required',
            'jam_pulang' => 'required',
        ]);

        $shift = Shift::findOrFail($id);
        $shift->update($request->all());

        return redirect()->back()->with('success', 'Data shift berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // Cek apakah shift sedang dipakai di jadwal?
        // Jika ingin aman, gunakan try-catch atau cek relasi dulu.
        try {
            $shift = Shift::findOrFail($id);
            $shift->delete();
            return redirect()->back()->with('success', 'Data shift berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus. Shift ini mungkin sedang digunakan di Jadwal Kerja.');
        }
    }
}