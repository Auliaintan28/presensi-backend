@extends('admin.layout')

@section('content')
    <div class="container-fluid">

        <a href="{{ route('persetujuan.index') }}" class="btn btn-link text-decoration-none mb-3 ps-0 text-muted">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>

        <h3 class="mb-4 fw-bold">Proses Pengajuan {{ $pengajuan->jenis }}</h3>

        <div class="row">
            <div class="col-md-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary">Informasi Pengajuan</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%" class="text-muted">Nama Pegawai</td>
                                <td class="fw-bold">{{ $pengajuan->user->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIP / Jabatan</td>
                                <td>
                                    {{ $pengajuan->user->nip }} <br>
                                    @foreach ($pengajuan->user->jabatan as $jabatan)
                                        <span class="badge bg-secondary me-1">{{ $jabatan }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Izin</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->translatedFormat('d F Y') }}
                                    s/d
                                    {{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->translatedFormat('d F Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Hari</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($pengajuan->tanggal_selesai)) + 1 }}
                                    Hari
                                </td>
                            </tr>
                        </table>

                        <div class="mb-3">
                            <label class="text-muted small fw-bold">KETERANGAN / ALASAN:</label>
                            <div class="p-3 bg-light rounded border mt-1">
                                {{ $pengajuan->keterangan ?? ($pengajuan->jenis_izin ?? ($pengajuan->tujuan ?? '-')) }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small fw-bold">FILE LAMPIRAN:</label>
                            <div class="mt-1">
                                @if ($pengajuan->file_lampiran)
                                    @php
                                        $cleanPath = str_replace(
                                            ['storage/', '/storage/'],
                                            '',
                                            $pengajuan->file_lampiran,
                                        );
                                        $url = asset('storage/' . $cleanPath);
                                    @endphp
                                    <a href="{{ $url }}" target="_blank" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-file-earmark-pdf me-2"></i> Lihat Lampiran
                                    </a>
                                @else
                                    <div class="alert alert-secondary mb-0 py-2">
                                        <i class="bi bi-info-circle me-1"></i> Tidak ada lampiran file.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="mb-0 fw-bold">Keputusan Admin</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('persetujuan.update', $pengajuan->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="jenis" value="{{ $pengajuan->jenis }}">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Status Persetujuan <span
                                        class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih Aksi --</option>
                                    <option value="disetujui">✅ Setujui Pengajuan</option>
                                    <option value="ditolak">❌ Tolak Pengajuan</option> 
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Catatan Admin (Opsional)</label>
                                <textarea name="catatan_admin" class="form-control" rows="5"
                                    placeholder="Tulis pesan untuk pegawai... Contoh: 'Disetujui, harap masuk kembali tanggal 5'"></textarea>
                                <div class="form-text text-muted">
                                    Pesan ini akan muncul di notifikasi aplikasi pegawai.
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save me-2"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
