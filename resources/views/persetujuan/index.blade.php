@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <h3 class="mb-4 fw-bold">Data Pengajuan</h3>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white p-0">
                <ul class="nav nav-tabs card-header-tabs mx-0" id="myTab" role="tablist">
                    <li class="nav-item ps-3" role="presentation">
                        <button class="nav-link active fw-bold text-primary py-3" id="pending-tab" data-bs-toggle="tab"
                            data-bs-target="#pending" type="button">
                            <i class="bi bi-inbox-fill me-2"></i> Pengajuan Masuk
                            @if ($pending->count() > 0)
                                <span class="badge bg-danger ms-1">{{ $pending->total() }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-secondary py-3" id="riwayat-tab" data-bs-toggle="tab"
                            data-bs-target="#riwayat" type="button">
                            <i class="bi bi-clock-history me-2"></i> Riwayat Proses
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="myTabContent">

                    <div class="tab-pane fade show active" id="pending" role="tabpanel">
                        @if ($pending->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-check-circle-fill text-success display-1 mb-3"></i>
                                <h5 class="text-muted">Tidak ada permintaan baru.</h5>
                                <p class="text-muted small">Semua pengajuan pegawai sudah Anda proses.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%" class="text-center">No</th>
                                            <th style="width: 30%">Pegawai</th>
                                            <th style="width: 15%">Jenis</th>
                                            <th style="width: 20%">Tanggal Izin</th>
                                            <th style="width: 20%">Status</th>
                                            <th style="width: 15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pending as $item)
                                            <tr>
                                                <td class="text-center">{{ $pending->firstItem() + $loop->index }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if ($item->user->foto_profil)
                                                            @php
                                                                $cleanPath = str_replace(
                                                                    ['storage/', '/storage/'],
                                                                    '',
                                                                    $item->user->foto_profil,
                                                                );
                                                                $url = asset('storage/' . $cleanPath);
                                                            @endphp
                                                            <img src="{{ $url }}" class="rounded-circle me-3"
                                                                width="40" height="40" style="object-fit: cover;">
                                                        @else
                                                            <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-3 fw-bold"
                                                                style="width: 40px; height: 40px;">
                                                                {{ substr($item->user->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <div class="text-start">
                                                            <div class="fw-bold text-dark">{{ $item->user->name }}</div>
                                                            <small class="text-muted">
                                                                {{ implode(', ', $item->user->jabatan) }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-start">
                                                    @php
                                                        $badgeColor = match ($item->jenis) {
                                                            'Cuti' => 'primary',
                                                            'Sakit' => 'danger',
                                                            'Izin' => 'warning',
                                                            'Dinas' => 'info',
                                                            default => 'secondary',
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $badgeColor }}">{{ $item->jenis }}</span>
                                                </td>
                                                <td class="text-start">
                                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</td>
                                                <td class="text-start"><span class="badge bg-warning text-dark">Menunggu
                                                        Persetujuan</span></td>
                                                <td class="text-start">
                                                    <a href="{{ route('persetujuan.show', ['jenis' => $item->jenis, 'id' => $item->id]) }}"
                                                        class="btn btn-sm btn-primary fw-bold px-3">
                                                        Proses <i class="bi bi-arrow-right ms-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">Menampilkan {{ $pending->firstItem() }} -
                                    {{ $pending->lastItem() }} dari {{ $pending->total() }} data.</small>
                                <div>{{ $pending->appends(request()->except('page_pending'))->links() }}</div>
                            </div>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="riwayat" role="tabpanel">
                        @if ($riwayat->isEmpty())
                            <div class="text-center py-5">
                                <h5 class="text-muted">Belum ada riwayat data.</h5>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%" class="text-center">No</th>
                                            <th style="width: 30%">Pegawai</th>
                                            <th style="width: 15%">Jenis</th>
                                            <th style="width: 20%">Tanggal</th>
                                            <th style="width: 20%">Status</th>
                                            <th style="width: 15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($riwayat as $item)
                                            <tr>
                                                <td class="text-center">{{ $riwayat->firstItem() + $loop->index }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if ($item->user->foto_profil)
                                                            @php
                                                                $cleanPath = str_replace(
                                                                    ['storage/', '/storage/'],
                                                                    '',
                                                                    $item->user->foto_profil,
                                                                );
                                                                $url = asset('storage/' . $cleanPath);
                                                            @endphp
                                                            <img src="{{ $url }}" class="rounded-circle me-3"
                                                                width="40" height="40" style="object-fit: cover;">
                                                        @else
                                                            <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-3 fw-bold"
                                                                style="width: 40px; height: 40px;">
                                                                {{ substr($item->user->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <div class="text-start">
                                                            <div class="fw-bold text-dark">{{ $item->user->name }}</div>
                                                            <small class="text-muted">Diproses:
                                                                {{ \Carbon\Carbon::parse($item->updated_at)->format('d M H:i') }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-start"><span
                                                        class="badge bg-secondary">{{ $item->jenis }}</span></td>
                                                <td class="text-start">
                                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</td>
                                                <td class="text-start">
                                                    @if ($item->status == 'disetujui')
                                                        <span class="badge bg-success"><i
                                                                class="bi bi-check-circle me-1"></i> Disetujui</span>
                                                    @else
                                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>
                                                            Ditolak</span>
                                                    @endif
                                                </td>
                                                <td class="text-start">
                                                    <a href="{{ route('persetujuan.show', ['jenis' => $item->jenis, 'id' => $item->id]) }}"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        Lihat Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">Menampilkan {{ $riwayat->firstItem() }} -
                                    {{ $riwayat->lastItem() }} dari {{ $riwayat->total() }} data.</small>
                                <div>{{ $riwayat->appends(request()->except('page_riwayat'))->links() }}</div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
