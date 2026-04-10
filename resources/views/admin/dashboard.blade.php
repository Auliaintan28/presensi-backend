@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark">Dashboard Overview</h3>
            <span class="text-muted">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary shadow-sm h-100 border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 opacity-75">Total Pegawai</h6>
                            <h2 class="my-1 fw-bold">{{ $totalPegawai }}</h2>
                        </div>
                        <i class="bi bi-people fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success shadow-sm h-100 border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 opacity-75">Hadir Hari Ini</h6>
                            <h2 class="my-1 fw-bold">{{ $hadirHariIni }}</h2>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning shadow-sm h-100 border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 opacity-75">Izin / Sakit</h6>
                            <h2 class="my-1 fw-bold">{{ $izinSakitHariIni }}</h2>
                        </div>
                        <i class="bi bi-hospital fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-danger shadow-sm h-100 border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 opacity-75">Perlu Persetujuan</h6>
                            <h2 class="my-1 fw-bold">{{ $totalPending }}</h2>
                        </div>
                        <i class="bi bi-exclamation-circle fs-1 opacity-50"></i>
                    </div>
                    <a href="{{ route('persetujuan.index') }}"
                        class="card-footer text-white text-decoration-none small bg-black bg-opacity-10 d-flex justify-content-between align-items-center">
                        Lihat Detail <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-danger">
                            <i class="bi bi-clock-history me-2"></i>Pegawai Terlambat Hari Ini
                        </h6>
                        <a href="{{ route('monitoring.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Pegawai</th>
                                        <th>Jam Masuk</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pegawaiTerlambat as $p)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    @if ($p->user->foto_profil)
                                                        @php
                                                            $cleanPath = str_replace(
                                                                ['storage/', '/storage/'],
                                                                '',
                                                                $p->user->foto_profil,
                                                            );
                                                            $url = asset('storage/' . $cleanPath);
                                                        @endphp
                                                        <img src="{{ $url }}" class="rounded-circle me-2"
                                                            width="35" height="35" style="object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-2"
                                                            style="width: 35px; height: 35px; font-size: 12px;">
                                                            {{ substr($p->user->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $p->user->name }}</div>
                                                        <small class="text-muted">
                                                            {{ is_array($p->user->jabatan) ? implode(', ', $p->user->jabatan) : $p->user->jabatan }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-danger fw-bold">{{ \Carbon\Carbon::parse($p->masuk)->format('H:i') }}</span>
                                            </td>
                                            {{-- <td>
                                                <span class="badge bg-light text-dark border">Cek Monitoring</span>
                                            </td> --}}
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">
                                                <i class="bi bi-emoji-smile display-6 d-block mb-2"></i>
                                                Tidak ada yang terlambat hari ini. Bagus!
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
                    <div class="card-body">
                        <h5 class="fw-bold">Halo, {{ Auth::user()->name }}!</h5>
                        <p class="small opacity-75 mb-0">
                            Selamat bekerja. Jangan lupa cek menu persetujuan secara berkala.
                        </p>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold">Aksi Cepat</div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('jadwal.index') }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div><i class="bi bi-calendar-plus me-2 text-primary"></i> Buat Jadwal Bulan Ini</div>
                            <i class="bi bi-chevron-right small"></i>
                        </a>
                        <a href="{{ route('pegawai.create') }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div><i class="bi bi-person-plus me-2 text-success"></i> Tambah Pegawai Baru</div>
                            <i class="bi bi-chevron-right small"></i>
                        </a>
                        <a href="{{ route('lokasi.index') }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div><i class="bi bi-geo-alt me-2 text-danger"></i> Update Lokasi Kantor</div>
                            <i class="bi bi-chevron-right small"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
