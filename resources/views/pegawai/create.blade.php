@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary fw-bold">Tambah Pegawai Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('pegawai.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">

                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="Contoh: Budi Santoso">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                    placeholder="nama@gmail.com">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password Awal <span class="text-danger">*</span></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Minimal 6 karakter">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="mb-3">
                                <label class="form-label">NIP (Nomor Induk Pegawai) <small class="text-muted">(opsional)</small></label>
                                <input type="number" name="nip" class="form-control @error('nip') is-invalid @enderror"
                                    value="{{ old('nip') }}" placeholder="Kosongkan jika tidak ada NIP">
                                @error('nip')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <select name="jabatan[]" id="select-jabatan"
                                    class="form-select @error('jabatan') is-invalid @enderror" multiple="multiple">

                                    @php
                                        $listJabatan = [
                                            'Kepala Puskesmas',
                                            'Kasubag Tata Usaha',
                                            'Dokter Umum',
                                            'Dokter Gigi Penugasan Khusus',
                                            'Apoteker',
                                            'Asisten Apoteker',
                                            'Perawat Pelaksana',
                                            'Perawat Non ASN',
                                            'Perawat Kontrak BLUD',
                                            'Bidan Pelaksana',
                                            'Bidan Pelaksana Jurim',
                                            'Bidan Desa Letawa',
                                            'Bidan Kontrak BLUD',
                                            'Bidan P3K',
                                            'Bidan Non ASN',
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
                                            'Tenaga Administrasi Kontrak BLUD',
                                            'Tenaga Front Office Kontrak BLUD',
                                            'SP2TP',
                                        ];
                                        sort($listJabatan);
                                    @endphp
                                    @foreach ($listJabatan as $jabatan)
                                        <option value="{{ $jabatan }}"
                                            {{ is_array(old('jabatan')) && in_array($jabatan, old('jabatan')) ? 'selected' : '' }}>
                                            {{ $jabatan }}
                                        </option>
                                    @endforeach

                                </select>
                                @error('jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <script>
                                $(document).ready(function() {
                                    $('#select-jabatan').select2({
                                        theme: 'bootstrap-5',
                                        placeholder: "-- Pilih Satu atau Lebih Jabatan --",
                                        allowClear: true,
                                        closeOnSelect: false // Menjaga dropdown tetap terbuka saat memilih banyak
                                    });
                                });
                            </script>

                            <div class="mb-3">
                                <label class="form-label">Nomor HP (WhatsApp) <small class="text-muted">(opsional)</small></label>
                                <input type="text" name="no_hp"
                                class="form-control @error('no_hp') is-invalid @enderror"
                                value="{{ old('no_hp') }}"
                                placeholder="Contoh: 08123456789"
                                pattern="08[0-9]{8,11}"
                                inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">

                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Foto Profil <small class="text-muted">(opsional)</small></label>
                                <input type="file" name="foto_profil"
                                    class="form-control @error('foto_profil') is-invalid @enderror" accept="image/*">
                                @error('foto_profil')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('pegawai.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
