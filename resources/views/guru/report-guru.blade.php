@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">📅 {{ $judul ?? 'Laporan Absensi Pegawai' }}</h4>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        
        <!-- Form Filter (Pencarian & Rentang Tanggal) -->
        <form action="" method="GET" class="row g-2 mb-4 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold small text-secondary">Cari Pegawai</label>
                <input type="text" name="search" class="form-control border-secondary" placeholder="Nama atau NIP..." value="{{ request('search') }}">
            </div>
            
            <!-- Default value diatur ke hari ini jika belum ada input -->
            <div class="col-6 col-md-3">
                <label class="form-label fw-bold small text-secondary">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control border-secondary" value="{{ request('start_date', \Carbon\Carbon::today()->toDateString()) }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-bold small text-secondary">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control border-secondary" value="{{ request('end_date', \Carbon\Carbon::today()->toDateString()) }}">
            </div>
            
            <div class="col-12 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100 fw-bold" title="Cari / Filter">🔍 Filter</button>
                <a href="/report-guru" class="btn btn-outline-secondary" title="Reset Filter">🔄</a>
            </div>
        </form>

        <!-- Tombol Export PDF -->
        <div class="d-flex justify-content-end mb-4 border-top pt-3">
            <!-- Tombol Cetak PDF yang otomatis membawa filter aktif -->
            <a href="/report-guru/pdf?search={{ request('search') }}&start_date={{ request('start_date', \Carbon\Carbon::today()->toDateString()) }}&end_date={{ request('end_date', \Carbon\Carbon::today()->toDateString()) }}" class="btn btn-danger px-4 fw-bold shadow-sm">
                📄 Unduh PDF
            </a>
        </div>

        <!-- Tabel Laporan -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">NIP</th>
                        <th class="text-start" width="23%">Nama Guru</th>
                        <th width="15%">Jabatan</th>
                        <th class="text-success" width="9%">Tepat</th>
                        <th class="text-warning" width="9%">Terlambat</th>
                        <th class="text-info" width="9%">Sakit</th>
                        <th class="text-primary" width="9%">Izin</th>
                        <th class="text-danger" width="9%">Alfa</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @forelse ($gurus as $index => $guru)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $guru->nip ?? '-' }}</td>
                        <td class="text-start fw-bold">{{ $guru->nama }}</td>
                        <td class="small">{{ $guru->mata_pelajaran ?? '-' }}</td>
                        <td class="fs-5 fw-bold text-success">{{ $guru->tepat_waktu ?? 0 }}</td>
                        <td class="fs-5 fw-bold text-warning">{{ $guru->terlambat ?? 0 }}</td>
                        <td class="fs-5 fw-bold text-info">{{ $guru->sakit ?? 0 }}</td>
                        <td class="fs-5 fw-bold text-primary">{{ $guru->izin ?? 0 }}</td>
                        <td class="fs-5 fw-bold text-danger">{{ $guru->alfa ?? 0 }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-muted py-4">Tidak ada data guru yang sesuai dengan filter pencarian atau rentang tanggal.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection