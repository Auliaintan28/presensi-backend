@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4 fw-bold">Laporan Presensi</h3>

    <div class="card shadow-sm border-0" style="max-width: 600px;">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-primary">
                <i class="bi bi-printer me-2"></i>Cetak Rekapitulasi
            </h6>
        </div>

        <div class="card-body p-4">

            {{-- FORM UTAMA --}}
            <form action="{{ route('laporan.cetak') }}" method="POST" target="_blank">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">Bulan</label>
                    <select name="bulan" class="form-select" required>
                        <option value="">-- Pilih Bulan --</option>
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Tahun</label>
                    <select name="tahun" class="form-select" required>
                        @for ($y = date('Y'); $y >= date('Y') - 2; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="row g-2">
                    <div class="col-md-6">
                        <button type="submit" name="action" value="cetak" class="btn btn-primary w-100">
                            <i class="bi bi-printer"></i> Cetak
                        </button>
                    </div>

                    <div class="col-md-6">
                        {{-- BUTTON PDF TANPA FORM DALAM FORM --}}
                        <button type="button"
                                onclick="unduhPDF()"
                                class="btn btn-danger w-100">
                            <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
function unduhPDF() {
    const bulan = document.querySelector('select[name="bulan"]').value;
    const tahun = document.querySelector('select[name="tahun"]').value;

    if (!bulan || !tahun) {
        alert('Silakan pilih bulan dan tahun terlebih dahulu.');
        return;
    }

    // Ganti 'laporan.print' menjadi 'laporan.unduh.pdf'
    const url = "{{ route('laporan.unduh.pdf') }}" + "?bulan=" + bulan + "&tahun=" + tahun;

    window.open(url, '_blank');
}
</script>

@endsection
