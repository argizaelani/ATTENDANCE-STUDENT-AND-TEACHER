@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">📅 {{ $judul ?? 'Laporan Absensi' }}</h4>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        
        <!-- Form Filter Lengkap (Pencarian, Kelas, Jurusan, Tanggal) -->
        <form action="" method="GET" class="row g-2 mb-4 align-items-end">
            
            <!-- Cari Nama / NIS -->
            <div class="col-12 col-md-3">
                <label class="form-label fw-bold small text-secondary">Cari Siswa</label>
                <input type="text" name="search" class="form-control border-secondary" placeholder="Nama atau NIS..." value="{{ request('search') }}">
            </div>

            <!-- Filter Kelas (Terintegrasi Master Data) -->
            <div class="col-6 col-md-2">
                <label class="form-label fw-bold small text-secondary">Kelas</label>
                <select name="kelas" class="form-select border-secondary">
                    <option value="">-- Semua --</option>
                    @if(isset($masterKelas))
                        @foreach($masterKelas as $kls)
                            <option value="{{ $kls->nama_kelas }}" {{ request('kelas') == $kls->nama_kelas ? 'selected' : '' }}>
                                Kelas {{ $kls->nama_kelas }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Filter Jurusan (Terintegrasi Master Data) -->
            <div class="col-6 col-md-2">
                <label class="form-label fw-bold small text-secondary">Jurusan</label>
                <select name="jurusan" class="form-select border-secondary">
                    <option value="">-- Semua --</option>
                    @if(isset($masterJurusan))
                        @foreach($masterJurusan as $jrs)
                            <option value="{{ $jrs->kode_jurusan }}" {{ request('jurusan') == $jrs->kode_jurusan ? 'selected' : '' }}>
                                {{ $jrs->kode_jurusan }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Tanggal Mulai -->
            <div class="col-6 col-md-2">
                <label class="form-label fw-bold small text-secondary">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control border-secondary" value="{{ request('start_date') }}">
            </div>

            <!-- Tanggal Selesai -->
            <div class="col-6 col-md-2">
                <label class="form-label fw-bold small text-secondary">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control border-secondary" value="{{ request('end_date') }}">
            </div>

            <!-- Tombol Aksi Filter -->
            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100 fw-bold" title="Cari / Filter">🔍</button>
                <a href="/report" class="btn btn-outline-secondary" title="Reset Filter">🔄</a>
            </div>
        </form>

        <!-- Tombol Pintasan & Export Excel -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 border-top pt-3">
            <div class="btn-group">
                <a href="?periode=minggu" class="btn btn-sm {{ request('periode') == 'minggu' && !request('start_date') ? 'btn-primary' : 'btn-outline-primary' }}">Periode Minggu Ini</a>
                <a href="?periode=bulan" class="btn btn-sm {{ (request('periode', 'bulan') == 'bulan') && !request('start_date') ? 'btn-primary' : 'btn-outline-primary' }}">Periode Bulan Ini</a>
            </div>

            <a href="/report/excel?periode={{ request('periode', 'bulan') }}&kelas={{ request('kelas') }}&jurusan={{ request('jurusan') }}&search={{ request('search') }}&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}" class="btn btn-success btn-sm fw-bold">
                📥 Unduh Excel
            </a>
        </div>

        <!-- Tabel Laporan Absensi -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">NIS</th>
                        <th class="text-start" width="20%">Nama Siswa</th>
                        <th width="10%">Kelas</th>
                        <th width="10%">Jurusan</th>
                        <th class="text-success" width="9%">Tepat</th>
                        <th class="text-warning" width="9%">Terlambat</th>
                        <th class="text-info" width="9%">Sakit</th>
                        <th class="text-primary" width="9%">Izin</th>
                        <th class="text-danger" width="9%">Alfa</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @forelse ($students as $index => $siswa)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $siswa->nis }}</td>
                        <td class="text-start fw-bold">{{ $siswa->name }}</td>
                        <td><span class="badge bg-secondary">{{ $siswa->kelas }}</span></td>
                        <td class="fw-bold">{{ $siswa->jurusan }}</td>
                        <td class="fs-5">{{ $siswa->tepat_waktu ?? 0 }}</td>
                        <td class="fs-5">{{ $siswa->terlambat ?? 0 }}</td>
                        <td class="fs-5">{{ $siswa->sakit ?? 0 }}</td>
                        <td class="fs-5">{{ $siswa->izin ?? 0 }}</td>
                        <td class="fs-5">{{ $siswa->alfa ?? 0 }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-muted py-4">Tidak ada data siswa yang sesuai dengan filter pencarian.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection