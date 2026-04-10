@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="mb-0 fw-bold">Data Pegawai</h3>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">

            <form action="{{ route('pegawai.index') }}" method="GET" class="d-flex gap-2">

                <select name="jabatan" class="form-select border-secondary" style="width: 200px;"
                    onchange="this.form.submit()">
                    <option value="">- Semua Jabatan -</option>
                    @foreach ($listJabatan as $jbt)
                        <option value="{{ $jbt }}" {{ request('jabatan') == $jbt ? 'selected' : '' }}>
                            {{ $jbt }}
                        </option>
                    @endforeach
                </select>

                <div class="input-group" style="width: 250px;">
                    <input type="text" name="keyword" class="form-control border-secondary"
                        placeholder="Cari Nama / NIP..." value="{{ request('keyword') }}">
                    <button class="btn btn-secondary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <a href="{{ route('pegawai.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Tambah Pegawai
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="text-center">No</th>
                                <th>Profil</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Jabatan</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pegawai as $index => $p)
                                <tr>
                                    <td class="text-center">
                                        {{ $pegawai->firstItem() + $loop->index }}
                                    </td>
                                    <td>
                                        @if ($p->foto_profil)
                                            @php
                                                // 1. Bersihkan path dari kata 'storage/' atau '/storage/' jika ada
                                                $cleanPath = str_replace(
                                                    ['storage/', '/storage/'],
                                                    '',
                                                    $p->foto_profil,
                                                );

                                                // 2. Hapus juga slash di paling depan (jika ada sisa) agar rapi
                                                $cleanPath = ltrim($cleanPath, '/');

                                                // 3. Gabungkan kembali dengan format yang benar
                                                $finalUrl = asset('storage/' . $cleanPath);
                                            @endphp

                                            <img src="{{ $finalUrl }}" class="rounded-circle" width="40"
                                                height="40" style="object-fit: cover;" alt="Foto">
                                        @else
                                            <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center"
                                                style="width: 40px; height: 40px; font-size: 14px;">
                                                {{ substr($p->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $p->nip ?? '-' }}</td>
                                    <td class="fw-bold">{{ $p->name }}</td>
                                    <td>
                                        @if (is_array($p->jabatan))
                                            {{ implode(', ', $p->jabatan) }}
                                        @else
                                            {{-- Cadangan jika ada data lama yang belum ter-cast --}}
                                            @php $jab = json_decode($p->jabatan, true); @endphp
                                            {{ is_array($jab) ? implode(', ', $jab) : $p->jabatan }}
                                        @endif
                                    </td>
                                    <td>{{ $p->email }}</td>
                                    <td>
                                        @if ($p->no_hp)
                                            <a href="https://wa.me/{{ $p->no_hp }}" target="_blank"
                                                class="text-decoration-none text-success fw-bold">
                                                <i class="bi bi-whatsapp me-1"></i> {{ $p->no_hp }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('pegawai.edit', $p->id) }}"
                                            class="btn btn-sm btn-warning text-white" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('pegawai.destroy', $p->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger btn-hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-people display-6 d-block mb-2"></i>
                                        Belum ada data pegawai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
                        <small class="text-muted">
                            Menampilkan <strong>{{ $pegawai->firstItem() }}</strong>
                            sampai <strong>{{ $pegawai->lastItem() }}</strong>
                            dari <strong>{{ $pegawai->total() }}</strong> data pegawai.
                        </small>

                        <div>
                            {{ $pegawai->withQueryString()->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
