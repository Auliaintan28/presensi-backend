<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>LP {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        /* CSS yang Anda berikan */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-left {
            text-align: left;
        }

        .tanda-tangan {
            float: right;
            text-align: center;
            width: 200px;
            margin-top: 50px;
        }

        /* Hapus atau nonaktifkan style khusus print untuk DomPDF */
        /* body onload="window.print()" harus dihapus */
    </style>
</head>

<body>
    {{-- KOP SURAT --}}
    <table style="width: 100%; border-bottom: 3px double #000; padding-bottom: 10px; border: none !important;">
        <tr>
            <td style="width: 15%; text-align: center; vertical-align: middle; border: none !important;">
                {{-- Pastikan path gambar dapat diakses, gunakan asset() atau base64 jika perlu --}}
                <img src="{{ public_path($logoKabupaten) }}" style="height: 70px; width: auto; border: none !important;">
            </td>

            <td style="width: 70%; text-align: center; vertical-align: middle; border: none !important;">
                <span style="font-size: 14px; font-weight: bold;">PEMERINTAH KABUPATEN PASANGKAYU</span><br>
                <span style="font-size: 16px; font-weight: bold;">DINAS KESEHATAN</span><br>
                <span style="font-size: 20px; font-weight: bold;">UPTD PUSKESMAS SARJO</span><br>
                <span style="font-size: 12px; font-style: italic;">Alamat: Jln.Trans Sulawesi Desa Sarjo Kec.Sarjo
                    Kab.Pasangkayu </span>
            </td>

            <td style="width: 15%; text-align: center; vertical-align: middle; border: none !important;">
                <img src="{{ public_path($logoPuskesmas) }}"
                    style="height: 80px; width: auto; border: none !important;">
            </td>
        </tr>
    </table>

    {{-- JUDUL LAPORAN --}}
    <div style="text-align: center; margin-bottom: 20px; margin-top: 20px;">
        <h3 style="text-decoration: underline; margin: 0;">LAPORAN REKAPITULASI PRESENSI</h3>
    </div>

    <div style="margin-bottom: 20px;">
        <strong>Periode:</strong> {{ $namaBulan }} {{ $tahun }}
    </div>

    {{-- TABEL DATA --}}
    <table>
        <thead>
            <tr>
                <th rowspan="2" width="5%">No</th>
                <th rowspan="2" width="10%">NIP</th>
                <th rowspan="2" width="25%">Nama Pegawai</th>
                <th rowspan="2" width="15%">Jabatan</th>
                <th colspan="2">Kehadiran</th>
                <th colspan="4">Keterangan (Kali)</th>
            </tr>
            <tr>
                <th>Hadir</th>
                <th>Telat</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Cuti</th>
                <th>Dinas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pegawai as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p->nip ?? '-' }}</td>
                    <td class="text-left">{{ $p->name }}</td>
                    <td>
                        {{ is_array($p->jabatan) ? implode(', ', $p->jabatan) : $p->jabatan ?? '-' }}
                    </td>

                    {{-- Ambil Data Agregat dari Controller --}}
                    <td>{{ $p->total_hadir ?? 0 }}</td>
                    <td>{{ $p->total_terlambat ?? 0 }}</td>
                    <td>{{ $p->total_sakit ?? 0 }}</td>
                    <td>{{ $p->total_izin ?? 0 }}</td>
                    <td>{{ $p->total_cuti ?? 0 }}</td>
                    <td>{{ $p->total_dinas ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">Tidak ada data pegawai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="font-size: 10px; margin-bottom: 30px;">
        * Data di atas adalah rekapitulasi berdasarkan sistem E-Presensi.
    </div>

    {{-- TANDA TANGAN --}}
    <div class="tanda-tangan">
        <p>Sarjo, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <br>
        <p>Kepala Puskesmas</p>
        <br><br><br>
        <p><strong>( ...................................... )</strong></p>
        <p>NIP. ...........................</p>
    </div>

</body>

</html>
