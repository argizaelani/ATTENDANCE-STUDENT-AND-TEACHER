<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi Pegawai</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background-color: #e9ecef; }
        .text-start { text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SMK FATAHILLAH CILEUNGSI</h2>
        <h2>REKAPITULASI KEHADIRAN PEGAWAI</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s.d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NIP</th>
                <th class="text-start" width="25%">Nama Guru</th>
                <th width="15%">Mata Pelajaran</th>
                <th>Tepat Waktu</th>
                <th>Terlambat</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Alfa</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($gurus as $index => $guru)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $guru->nip ?? '-' }}</td>
                <td class="text-start">{{ $guru->nama }}</td>
                <td>{{ $guru->mata_pelajaran ?? '-' }}</td>
                <td>{{ $guru->tepat_waktu ?? 0 }}</td>
                <td>{{ $guru->terlambat ?? 0 }}</td>
                <td>{{ $guru->sakit ?? 0 }}</td>
                <td>{{ $guru->izin ?? 0 }}</td>
                <td>{{ $guru->alfa ?? 0 }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9">Tidak ada data kehadiran guru pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>