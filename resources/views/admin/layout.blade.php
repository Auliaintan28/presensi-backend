<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: white;
        }

        .sidebar a {
            color: #c2c7d0;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #007bff;
            color: white;
        }

        .content {
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <div class="sidebar d-flex flex-column flex-shrink-0 p-2" style="width: 250px;">
            <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white ps-2 mt-2">

                <img src="{{ asset('assets/logo-presensi.png') }}" alt="Logo" width="50" height="50"
                    class="me-2">

                <div class="d-flex flex-column justify-content-center">
                    <span class="fs-4 fw-bold lh-1">Panel Admin</span>
                </div>
            </div>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->is('admin/dashboard') ? 'active' : 'text-white' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pegawai.index') }}"
                        class="nav-link {{ request()->routeIs('pegawai.index') ? 'active' : 'text-white' }}">
                        <i class="bi bi-people me-2"></i> Data Pegawai
                    </a>
                </li>
                <li>
                    <a href="{{ route('jadwal.index') }}"
                        class="nav-link {{ request()->routeIs('jadwal.index') ? 'active' : 'text-white' }}">
                        <i class="bi bi-calendar-week me-2"></i> Kelola Jadwal
                    </a>
                </li>
                <li>
                    <a href="{{ route('shift.index') }}"
                        class="nav-link {{ request()->routeIs('shift.index') ? 'active' : 'text-white' }}">
                        <i class="bi bi-clock me-2"></i> Data Shift
                    </a>
                </li>
                <li>
                    <a href="{{ route('monitoring.index') }}"
                        class="nav-link {{ request()->routeIs('monitoring.index') ? 'active' : 'text-white' }}">
                        <i class="bi bi-person-check me-2"></i> Monitoring Presensi
                    </a>
                </li>
                <li>
                    <a href="{{ route('persetujuan.index') }}"
                        class="nav-link {{ request()->routeIs('persetujuan.index') ? 'active' : 'text-white' }}">
                        <i class="bi bi-check-circle me-2"></i> Data Pengajuan

                        {{-- Opsional: Tampilkan Badge Notifikasi jika ada yang pending --}}
                        @php
                            $countPending =
                                \App\Models\PengajuanCuti::where('status', 'diajukan')->count() +
                                \App\Models\PengajuanSakit::where('status', 'diajukan')->count() +
                                \App\Models\PengajuanIzinHarian::where('status', 'diajukan')->count() +
                                \App\Models\PengajuanDinas::where('status', 'diajukan')->count();
                        @endphp

                        @if ($countPending > 0)
                            <span class="badge bg-danger ms-auto">{{ $countPending }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('lokasi.index') }}"
                        class="nav-link {{ request()->routeIs('lokasi.index') ? 'active' : 'text-white' }}">
                        <i class="bi bi-gear me-2"></i> Pengaturan Lokasi
                    </a>
                </li>
                <li>
                    <a href="{{ route('laporan.index') }}"
                        class="nav-link {{ request()->routeIs('laporan.index') ? 'active' : 'text-white' }}">
                        <i class="bi bi-file-earmark-text me-2"></i> Laporan Presensi
                    </a>
                </li>
            </ul>
            {{-- <hr> --}}
            {{-- <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger w-100">Logout</button>
            </form> --}}
        </div>

        <div class="flex-grow-1 bg-light">

            <nav class="navbar navbar-expand-lg navbar-white bg-white shadow-sm px-4 py-3">
                <div class="container-fluid">

                    <div class="ms-auto d-flex align-items-center">

                        <div class="dropdown">
                            <a href="#"
                                class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                                id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">

                                @if (Auth::user()->foto_profil)
                                    @php
                                        // Logika pembersih path agar aman
                                        $cleanPath = str_replace(
                                            ['storage/', '/storage/'],
                                            '',
                                            Auth::user()->foto_profil,
                                        );
                                        $url = asset('storage/' . $cleanPath);
                                    @endphp
                                    <img src="{{ $url }}" alt="admin" width="40" height="40"
                                        class="rounded-circle me-2 border" style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-2 fw-bold"
                                        style="width: 40px; height: 40px;">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif

                                <div class="d-none d-sm-block text-start me-2">
                                    <div class="fw-bold small">{{ Auth::user()->name }}</div>
                                    <div class="text-muted small" style="font-size: 11px;">Administrator</div>
                                </div>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="dropdownUser1">
                                <li class="px-3 py-2 text-center d-sm-none">
                                    <strong>{{ Auth::user()->name }}</strong>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                {{-- <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person-gear me-2"></i> Edit Profil
                            </a>
                        </li> --}}

                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 1. Cek apakah ada Session 'success' dari Laravel?
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'BERHASIL!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000 // Popup hilang sendiri setelah 2 detik
            });
        @endif

        // 2. Cek apakah ada Session 'error' dari Laravel?
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'GAGAL!',
                text: '{{ session('error') }}',
            });
        @endif

        // 3. Script Konfirmasi Hapus (Bonus agar lebih aman)
        // Cari semua tombol yang punya class 'btn-hapus'
        document.addEventListener('click', function(e) {
            if (e.target && e.target.closest('.btn-hapus')) {
                e.preventDefault(); // Cegah form submit langsung

                let form = e.target.closest('form'); // Ambil form terdekat

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Jika user klik Ya, baru submit form
                    }
                });
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

    <script>
        // Inisialisasi Flatpickr pada semua input dengan class 'datepicker'
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d", // Format yang dikirim ke Server/Database (Tetap Y-m-d)
            altInput: true, // Aktifkan tampilan alternatif
            altFormat: "d/m/Y", // Format yang DILIHAT User (Tanggal/Bulan/Tahun)
            locale: "id", // Pakai Bahasa Indonesia (Senin, Selasa, dst)
            allowInput: true // User boleh ngetik manual
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr(".timepicker", {
                enableTime: true, // Aktifkan mode waktu
                noCalendar: true, // Matikan kalender
                dateFormat: "H:i", // Format 24 Jam (contoh: 14:00)
                time_24hr: true, // Paksa mode 24 jam (hilangkan AM/PM)
                allowInput: true // Bolehkah user mengetik manual? Ya.
            });
        });
    </script>
</body>

</html>
