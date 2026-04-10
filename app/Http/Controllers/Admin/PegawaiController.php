<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalKerja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $allJabatanRaw = User::where('role', 'pegawai')
            ->whereNotNull('jabatan')
            ->pluck('jabatan');

        $listJabatan = [];
        foreach ($allJabatanRaw as $j) {
            if (is_array($j)) {
                $listJabatan = array_merge($listJabatan, $j);
            }
        }
        $listJabatan = array_unique($listJabatan);
        sort($listJabatan);

        $query = User::where('role', 'pegawai');

        if ($request->has('jabatan') && $request->jabatan != '') {
            $query->whereJsonContains('jabatan', $request->jabatan);
        }

        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('nip', 'LIKE', "%{$keyword}%");
            });
        }

        $pegawai = $query->orderBy('name', 'asc')->paginate(10);
        $pegawai->appends($request->all());

        return view('pegawai.index', compact('pegawai', 'listJabatan'));
    }

    public function create()
    {
        return view('pegawai.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|min:6',
            'nip'         => 'nullable|numeric|digits_between:10,20|unique:users,nip',
            'jabatan'     => 'required|array',
            'no_hp'       => 'nullable|regex:/^08[0-9]{8,11}$/',
            'foto_profil' => 'nullable|image|max:2048',
        ], [
            'name.required'    => 'Nama lengkap tidak boleh kosong.',
            'email.required'   => 'Email tidak boleh kosong.',
            'password.required'=> 'Password tidak boleh kosong.',
            'email.unique'     => 'Email ini sudah terdaftar.',
            'password.min'     => 'Password minimal 6 karakter.',
            'nip.numeric'      => 'NIP harus berupa angka.',
            'nip.unique'       => 'NIP ini sudah terdaftar',
            'jabatan.required' => 'Silakan pilih minimal satu jabatan.',
        ]);

        $pathFoto = null;
        if ($request->hasFile('foto_profil')) {
            $pathFoto = $request->file('foto_profil')->store('foto_profil', 'public');
        }

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => 'pegawai',
            'nip'         => $request->nip,
            'jabatan'     => $request->jabatan,
            'no_hp'       => $request->no_hp,
            'foto_profil' => $pathFoto,
        ]);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $pegawai = User::findOrFail($id);
        return view('pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, $id)
    {
        $pegawai = User::findOrFail($id);

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $id,
            'nip'     => 'nullable|numeric|unique:users,nip,' . $id,
            'jabatan' => 'required|array',
            'no_hp'   => 'nullable|regex:/^08[0-9]{8,11}$/',
            'foto_profil' => 'nullable|image|max:2048',
        ],[
            'name.required'    => 'Nama lengkap tidak boleh kosong.',
            'email.required'   => 'Email tidak boleh kosong.',
            'email.unique'     => 'Email ini sudah terdaftar.',
            'password.min'     => 'Password minimal 6 karakter.',
            'nip.numeric'      => 'NIP harus berupa angka.',
            'nip.unique'       => 'NIP ini sudah terdaftar',
            'jabatan.required' => 'Silakan pilih minimal satu jabatan.',
        ]);

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'nip'     => $request->nip,
            'jabatan' => $request->jabatan,
            'no_hp'   => $request->no_hp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto_profil')) {
            if ($pegawai->foto_profil) {
                Storage::disk('public')->delete($pegawai->foto_profil);
            }
            $data['foto_profil'] = $request->file('foto_profil')->store('foto_profil', 'public');
        }

        $pegawai->update($data);

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pegawai = User::findOrFail($id);

        // Cek apakah pegawai sudah punya jadwal kerja
        $punyaJadwal = JadwalKerja::where('user_id', $pegawai->id)->exists();

        if ($punyaJadwal) {
            return redirect()->route('pegawai.index')
                ->with('error', 'Pegawai tidak dapat dihapus karena sudah memiliki jadwal kerja.');
        }

        // Hapus foto jika ada
        if ($pegawai->foto_profil) {
            Storage::disk('public')->delete($pegawai->foto_profil);
        }

        // Hapus pegawai
        $pegawai->delete();

        return redirect()->route('pegawai.index')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }
}
