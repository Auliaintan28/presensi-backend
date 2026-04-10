@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 fw-bold">Data Shift</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg me-2"></i>Tambah Shift
            </button>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 10%">No</th>
                                <th style="width: 20%">Nama Shift</th>
                                <th style="width: 20%">Jam Masuk</th>
                                <th style="width: 20%">Jam Pulang</th>
                                <th style="width: 20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shifts as $s)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $s->nama_shift }}</td>
                                    <td><span class="badge bg-success">{{ substr($s->jam_masuk, 0, 5) }}</span></td>
                                    <td><span class="badge bg-danger">{{ substr($s->jam_pulang, 0, 5) }}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning text-white me-1" data-bs-toggle="modal"
                                            data-bs-target="#modalEdit{{ $s->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('shift.destroy', $s->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger btn-hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalEdit{{ $s->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit Shift</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('shift.update', $s->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Shift</label>
                                                        <input type="text" name="nama_shift"
                                                            class="form-control @error('nama_shift') is-invalid @enderror"
                                                            value="{{ old('nama_shift', $s->nama_shift) }}">
                                                        @error('nama_shift')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-6 mb-3">
                                                            <label class="form-label">Jam Masuk</label>
                                                            <input type="text" name="jam_masuk"
                                                                class="form-control timepicker @error('jam_masuk') is-invalid @enderror"
                                                                value="{{ old('jam_masuk', $s->jam_masuk) }}">
                                                            @error('jam_masuk')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-6 mb-3">
                                                            <label class="form-label">Jam Pulang</label>
                                                            <input type="text" name="jam_pulang"
                                                                class="form-control timepicker @error('jam_pulang') is-invalid @enderror"
                                                                value="{{ old('jam_pulang', $s->jam_pulang) }}">
                                                            @error('jam_pulang')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit"
                                                        class="btn btn-warning text-white">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data shift.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Shift Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('shift.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Shift</label>
                            <input type="text" name="nama_shift"
                                class="form-control @error('nama_shift') is-invalid @enderror"
                                placeholder="Contoh: Shift Pagi" value="{{ old('nama_shift') }}">
                            @error('nama_shift')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Jam Masuk</label>
                                <input type="text" name="jam_masuk"
                                    class="form-control timepicker @error('jam_masuk') is-invalid @enderror"
                                    placeholder="07:00" value="{{ old('jam_masuk') }}">
                                @error('jam_masuk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-6 mb-3">
                                <label class="form-label">Jam Pulang</label>
                                <input type="text" name="jam_pulang"
                                    class="form-control timepicker @error('jam_pulang') is-invalid @enderror"
                                    placeholder="14:00" value="{{ old('jam_pulang') }}">
                                @error('jam_pulang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // LOGIKA BUKA MODAL OTOMATIS JIKA ADA ERROR
    @if($errors->any())
        document.addEventListener("DOMContentLoaded", function() {
            // Cek apakah ini error dari Tambah atau Edit?
            // Sederhananya: Buka modal tambah jika inputnya kosong, tapi untuk edit agak tricky.
            // Solusi aman: Kita buka modal tambah saja secara default jika error.
            
            var myModal = new bootstrap.Modal(document.getElementById('modalTambah'));
            myModal.show();
        });
    @endif
</script>
@endsection
