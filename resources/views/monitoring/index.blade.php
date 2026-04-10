@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <h3 class="mb-4 fw-bold">Monitoring Presensi Harian</h3>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="{{ route('monitoring.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-auto">
                        <label class="form-label fw-bold">Pilih Tanggal</label>
                        <input type="text" name="tanggal" class="form-control datepicker" value="{{ $tanggal }}"
                            placeholder="Pilih Tanggal">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-2"></i>Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 30%" class="text-start">Nama Pegawai</th>
                                <th style="width: 25%">Detail Masuk (Datang)</th>
                                <th style="width: 25%">Detail Pulang</th>
                                <th style="width: 15%" class="text-start">Durasi Kerja</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($presensi as $p)
                                <tr>
                                    <td class="text-center">
                                        {{ $presensi->firstItem() + $loop->index }}
                                    </td>
                                    <td>
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
                                                <img src="{{ $url }}" class="rounded-circle me-2" width="40"
                                                    height="40" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-2 fw-bold"
                                                    style="width: 40px; height: 40px; font-size: 14px;">
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
                                        <div class="d-flex align-items-center justify-content-center">
                                            @if ($p->latitude_datang && $p->longitude_datang)
                                                <button
                                                    onclick="showMap('{{ $p->latitude_datang }}', '{{ $p->longitude_datang }}', 'Masuk')"
                                                    class="btn p-0 text-decoration-none me-3 text-center border-0 bg-transparent"
                                                    data-bs-toggle="tooltip" title="Lihat Lokasi Datang">
                                                    <i class="bi bi-geo-alt-fill text-danger fs-3 d-block"></i>
                                                    <small class="text-muted" style="font-size: 10px;">Peta</small>
                                                </button>
                                            @else
                                                <div class="me-3 text-center text-muted opacity-50">
                                                    <i class="bi bi-geo-alt-fill fs-3 d-block"></i>
                                                    <small style="font-size: 10px;">No Loc</small>
                                                </div>
                                            @endif

                                            <div class="d-flex align-items-center">
                                                <span class="fw-bold fs-5 text-dark me-2">
                                                    {{ \Carbon\Carbon::parse($p->masuk)->format('H:i') }}
                                                </span>
                                                @if ($p->is_terlambat)
                                                    <span class="badge bg-danger rounded-pill"
                                                        style="font-size: 11px;">Terlambat</span>
                                                @else
                                                    <span class="badge bg-success rounded-pill"
                                                        style="font-size: 11px;">Tepat Waktu</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        @if ($p->pulang)
                                            <div class="d-flex align-items-center justify-content-center">
                                                @if ($p->latitude_pulang && $p->longitude_pulang)
                                                    <button
                                                        onclick="showMap('{{ $p->latitude_pulang }}', '{{ $p->longitude_pulang }}', 'Pulang')"
                                                        class="btn p-0 text-decoration-none me-3 text-center border-0 bg-transparent"
                                                        data-bs-toggle="tooltip" title="Lihat Lokasi Pulang">
                                                        <i class="bi bi-geo-alt-fill text-primary fs-3 d-block"></i>
                                                        <small class="text-muted" style="font-size: 10px;">Peta</small>
                                                    </button>
                                                @else
                                                    <div class="me-3 text-center text-muted opacity-50">
                                                        <i class="bi bi-geo-alt-fill fs-3 d-block"></i>
                                                        <small style="font-size: 10px;">No Loc</small>
                                                    </div>
                                                @endif

                                                <div class="d-flex align-items-center">
                                                    <span class="fw-bold fs-5 text-dark me-2">
                                                        {{ \Carbon\Carbon::parse($p->pulang)->format('H:i') }}
                                                    </span>
                                                    @if ($p->is_pulang_cepat)
                                                        <span class="badge bg-warning text-dark rounded-pill"
                                                            style="font-size: 11px;">Pulang Cepat</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center text-muted fst-italic opacity-50 py-2">
                                                - Belum Absen -
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-start">
                                        @if ($p->pulang)
                                            @php
                                                $waktuMasuk = \Carbon\Carbon::parse($p->masuk);
                                                $waktuPulang = \Carbon\Carbon::parse($p->pulang);

                                                // Hitung selisih
                                                $durasi = $waktuMasuk->diff($waktuPulang);
                                            @endphp

                                            <div class="fw-bold text-dark">
                                                {{ $durasi->format('%H Jam') }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $durasi->format('%I Menit') }}
                                            </small>
                                        @else
                                            <span class="badge bg-light text-muted border">
                                                Running...
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-calendar-x display-6 d-block mb-3"></i>
                                        Tidak ada data presensi pada tanggal ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
                        <small class="text-muted">
                            Menampilkan <strong>{{ $presensi->firstItem() }}</strong>
                            sampai <strong>{{ $presensi->lastItem() }}</strong>
                            dari <strong>{{ $presensi->total() }}</strong> data.
                        </small>

                        <div>
                            {{ $presensi->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalLokasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-body p-0 text-center bg-light">
                    <div class="p-3 pb-2">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                            Lokasi Presensi <span id="tipePresensi"></span>
                        </h5>
                    </div>

                    <div class="position-relative bg-white">
                        <img id="mapImage" src="" alt="Loading Map..." class="img-fluid"
                            style="width: 100%; height: 300px; object-fit: cover;">

                        <div class="position-absolute top-50 start-50 translate-middle text-muted" style="z-index: -1;">
                            <div class="spinner-border spinner-border-sm"></div> Memuat Peta...
                        </div>
                    </div>

                    <div class="p-4">
                        <h5 class="fw-bold text-dark mb-1" id="teksJarak">... meter dari kantor</h5>
                        <small class="text-muted">Titik Merah: Pegawai | Titik Biru: Kantor</small>
                    </div>

                    <div class="p-3 pt-0">
                        <button type="button" class="btn btn-link text-decoration-none text-muted fw-bold"
                            data-bs-dismiss="modal">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 1. KONFIGURASI
        const API_KEY = "{{ config('services.google.maps_key') }}";

        // AMBIL DARI DATABASE (Dinamis)
        // Jika data kosong, pakai default (0,0) agar tidak error
        const KANTOR_LAT = {{ $lokasiKantor->latitude ?? -0.8984754 }};
        const KANTOR_LON = {{ $lokasiKantor->longitude ?? 119.5424918 }};

        function showMap(latUser, lonUser, tipe) {
            document.getElementById('tipePresensi').innerText = tipe;

            let jarak = hitungJarak(latUser, lonUser, KANTOR_LAT, KANTOR_LON);
            let teksJarak = "";

            if (jarak < 1000) {
                teksJarak = Math.round(jarak) + " meter dari kantor";
            } else {
                teksJarak = (jarak / 1000).toFixed(2) + " km dari kantor";
            }
            document.getElementById('teksJarak').innerText = teksJarak;

            let url = `https://maps.googleapis.com/maps/api/staticmap?size=600x400&maptype=roadmap` +
                `&markers=color:red%7Clabel:P%7C${latUser},${lonUser}` +
                `&markers=color:blue%7Clabel:K%7C${KANTOR_LAT},${KANTOR_LON}` +
                `&key=${API_KEY}`;

            document.getElementById('mapImage').src = url;

            var myModal = new bootstrap.Modal(document.getElementById('modalLokasi'));
            myModal.show();
        }

        function hitungJarak(lat1, lon1, lat2, lon2) {
            const R = 6371e3;
            const phi1 = lat1 * Math.PI / 180;
            const phi2 = lat2 * Math.PI / 180;
            const deltaPhi = (lat2 - lat1) * Math.PI / 180;
            const deltaLambda = (lon2 - lon1) * Math.PI / 180;

            const a = Math.sin(deltaPhi / 2) * Math.sin(deltaPhi / 2) +
                Math.cos(phi1) * Math.cos(phi2) *
                Math.sin(deltaLambda / 2) * Math.sin(deltaLambda / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return R * c;
        }
    </script>
@endsection
