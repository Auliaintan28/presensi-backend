@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <h3 class="mb-4 fw-bold">Kelola Jadwal Kerja</h3>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ old('mode', 'reguler') == 'reguler' ? 'active' : '' }} text-primary fw-bold"
                            id="reguler-tab" data-bs-toggle="tab" data-bs-target="#reguler" type="button" role="tab">
                            <i class="bi bi-calendar-check me-2"></i>Jadwal Bulanan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ old('mode') == 'custom' ? 'active' : '' }} text-danger fw-bold"
                            id="custom-tab" data-bs-toggle="tab" data-bs-target="#custom" type="button" role="tab">
                            <i class="bi bi-calendar-range me-2"></i>Jadwal Khusus
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="myTabContent">

                    {{-- TAB REGULER --}}
                    <div class="tab-pane fade {{ old('mode', 'reguler') == 'reguler' ? 'show active' : '' }}" id="reguler"
                        role="tabpanel">

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Fitur ini akan mengisi jadwal <strong>Senin - Sabtu</strong> secara otomatis selama sebulan
                            penuh. Hari Minggu akan dikosongkan (Libur).
                        </div>

                        <form action="{{ route('jadwal.generate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="mode" value="reguler">

                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Pilih Bulan</label>
                                    <select name="bulan" class="form-select">
                                        @foreach (range(1, 12) as $m)
                                            <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pilih Tahun</label>
                                    <select name="tahun" class="form-select">
                                        <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                        <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pilih Shift</label>
                                    <select name="shift_id" class="form-select @error('shift_id') is-invalid @enderror">
                                        <option value="">-- Pilih Shift --</option>
                                        @foreach ($shifts as $s)
                                            <option value="{{ $s->id }}"
                                                {{ old('shift_id') == $s->id ? 'selected' : '' }}>
                                                {{ $s->nama_shift }}
                                                ({{ substr($s->jam_masuk, 0, 5) }}-{{ substr($s->jam_pulang, 0, 5) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shift_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <label class="form-label fw-bold">Pilih Pegawai (Checklist)</label>
                            <div class="card bg-light border @error('user_ids') border-danger @enderror">
                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                    <div class="form-check mb-2 border-bottom pb-2">
                                        <input class="form-check-input" type="checkbox" id="checkAllReguler">
                                        <label class="form-check-label fw-bold" for="checkAllReguler">Pilih Semua Pegawai</label>
                                    </div>
                                    <div class="row">
                                        @foreach ($pegawai as $p)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input user-checkbox-reguler" type="checkbox"
                                                        name="user_ids[]" value="{{ $p->id }}" id="reg_user_{{ $p->id }}"
                                                        {{ is_array(old('user_ids')) && in_array($p->id, old('user_ids')) ? 'checked' : '' }}>

                                                    <label class="form-check-label" for="reg_user_{{ $p->id }}">
                                                        {{ $p->name }} 
                                                        <small class="text-muted">
                                                            ({{ is_array($p->jabatan) ? implode(', ', $p->jabatan) : implode(', ', (array)json_decode($p->jabatan, true)) }})
                                                        </small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>Simpan Jadwal Bulanan
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- TAB CUSTOM --}}
                    <div class="tab-pane fade {{ old('mode') == 'custom' ? 'show active' : '' }}" id="custom"
                        role="tabpanel">

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            Gunakan fitur ini untuk mengatur jadwal Shift (Pagi/Siang/Malam) atau jadwal khusus pada tanggal tertentu.
                        </div>

                        <form action="{{ route('jadwal.generate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="mode" value="custom">

                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" class="form-control" required
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" class="form-control" required
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pilih Shift</label>
                                    <select name="shift_id" class="form-select @error('shift_id') is-invalid @enderror">
                                        <option value="">-- Pilih Shift --</option>
                                        @foreach ($shifts as $s)
                                            <option value="{{ $s->id }}"
                                                {{ old('shift_id') == $s->id ? 'selected' : '' }}>
                                                {{ $s->nama_shift }}
                                                ({{ substr($s->jam_masuk, 0, 5) }}-{{ substr($s->jam_pulang, 0, 5) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr class="my-4">

                            <label class="form-label fw-bold">Pilih Pegawai (Checklist)</label>
                            <div class="card bg-light border @error('user_ids') border-danger @enderror">
                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                    <div class="form-check mb-2 border-bottom pb-2">
                                        <input class="form-check-input" type="checkbox" id="checkAllCustom">
                                        <label class="form-check-label fw-bold" for="checkAllCustom">Pilih Semua Pegawai</label>
                                    </div>
                                    <div class="row">
                                        @foreach ($pegawai as $p)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input user-checkbox-custom" type="checkbox"
                                                        name="user_ids[]" value="{{ $p->id }}" id="cus_user_{{ $p->id }}">

                                                    <label class="form-check-label" for="cus_user_{{ $p->id }}">
                                                        {{ $p->name }} 
                                                        <small class="text-muted">
                                                            ({{ is_array($p->jabatan) ? implode(', ', $p->jabatan) : implode(', ', (array)json_decode($p->jabatan, true)) }})
                                                        </small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 text-end">
                                <button type="submit" class="btn btn-danger text-white">
                                    <i class="bi bi-save me-2"></i>Simpan Jadwal Khusus
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('checkAllReguler').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.user-checkbox-reguler');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        document.getElementById('checkAllCustom').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.user-checkbox-custom');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
@endsection
