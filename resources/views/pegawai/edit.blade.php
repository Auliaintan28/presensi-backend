@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-warning fw-bold">Edit Data Pegawai</h5>
            </div>
            <div class="card-body">
                {{-- Ambil data jabatan dan ubah dari JSON ke Array PHP --}}
                @php
                    $currentJabatans = is_array($pegawai->jabatan) ? $pegawai->jabatan : (json_decode($pegawai->jabatan, true) ?? []);
                @endphp

                <form action="{{ route('pegawai.update', $pegawai->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $pegawai->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $pegawai->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 p-3 bg-light rounded border">
                                <label class="form-label fw-bold">Ubah Password (Opsional)</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Masukkan password baru jika ingin mengganti">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah password.</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nomor Induk Pegawai <small class="text-muted">(opsional)</small></label>
                                <input type="number" name="nip" class="form-control @error('nip') is-invalid @enderror"
                                    value="{{ old('nip', $pegawai->nip) }}" placeholder="Kosongkan jika tidak ada NIP">
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                {{-- Gunakan ID select-jabatan agar Select2 aktif --}}
                                <select name="jabatan[]" id="select-jabatan"
                                    class="form-select @error('jabatan') is-invalid @enderror" multiple="multiple">
                                    @php
                                        $listOptions = [
                                            'Kepala Puskesmas',
                                            'Kasubag Tata Usaha',
                                            'Dokter Umum',
                                            'Dokter Gigi Penugasan Khusus',
                                            'Apoteker',
                                            'Asisten Apoteker',
                                            'Perawat Pelaksana',
                                            'Perawat Kontrak BLUD',
                                            'Perawat Non ASN',
                                            'Bidan Pelaksana',
                                            'Bidan Pelaksana Jurim',
                                            'Bidan Desa Letawa',
                                            'Bidan P3K',
                                            'Bidan Non ASN',
                                            'Bidan Kontrak BLUD',
                                            'Sanitarian',
                                            'Promosi Kesehatan',
                                            'Surveylans',
                                            'Bendahara Pengeluaran',
                                            'Bendahara BOK',
                                            'PJ UKM',
                                            'PJ UKP',
                                            'PJ KB',
                                            'PJ Gizi UKM',
                                            'PJ Gizi UKP',
                                            'PJ Gudang Farmasi',
                                            'PJ Pelayanan Prolanis',
                                            'PJ Ruang Farmasi',
                                            'PJ Malaria',
                                            'PJ TB Baru',
                                            'PJ Diare',
                                            'PJ PKPR',
                                            'PJ Hepatitis',
                                            'PJ UKS dan Keswa',
                                            'PJ Poli Umum',
                                            'PJ Poli Gigi dan Mulut',
                                            'PJ Poli MTBS',
                                            'PJ UGD',
                                            'PJ Ruang Persalinan dan Nifas',
                                            'PJ Rekam Medik',
                                            'PJ Laboratorium',
                                            'Koordinator KIA',
                                            'Koordinator Rawat Inap',
                                            'Driver Ambulans',
                                            'Tenaga Front Office Kontrak BLUD',
                                            'Tenaga Administrasi Kontrak BLUD',
                                            'SP2TP',
                                        ];
                                    @endphp

                                    @foreach ($listOptions as $opt)
                                        <option value="{{ $opt }}"
                                            {{ in_array($opt, old('jabatan', $currentJabatans)) ? 'selected' : '' }}>
                                            {{ $opt }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                               <label class="form-label">Nomor HP (WhatsApp) <small class="text-muted">(opsional)</small></label>
                                <input type="number" name="no_hp"
                                    class="form-control @error('no_hp') is-invalid @enderror"
                                    value="{{ old('no_hp', $pegawai->no_hp) }}">
                                @error('no_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Foto Profil <small class="text-muted">(opsional)</small></label>
                                @if ($pegawai->foto_profil)
                                    <div class="mb-2">
                                        @php
                                            $cleanPath = str_replace(
                                                ['storage/', '/storage/'],
                                                '',
                                                $pegawai->foto_profil,
                                            );
                                            $cleanPath = ltrim($cleanPath, '/');
                                            $finalUrl = asset('storage/' . $cleanPath);
                                        @endphp
                                        <img src="{{ $finalUrl }}" alt="Foto Lama" class="img-thumbnail rounded-circle"
                                            width="80" style="height: 80px; object-fit: cover;">
                                        <small class="d-block text-muted mt-1">Foto saat ini</small>
                                    </div>
                                @endif
                                <input type="file" name="foto_profil"
                                    class="form-control @error('foto_profil') is-invalid @enderror" accept="image/*">
                                <small class="text-muted">Upload foto baru untuk mengganti.</small>
                                @error('foto_profil')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('pegawai.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-warning text-white">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script inisialisasi Select2 khusus untuk halaman Edit --}}
    <script>
        $(document).ready(function() {
            $('#select-jabatan').select2({
                theme: 'bootstrap-5',
                placeholder: "-- Pilih Satu atau Lebih Jabatan --",
                allowClear: true,
                closeOnSelect: false
            });
        });
    </script>
@endsection
