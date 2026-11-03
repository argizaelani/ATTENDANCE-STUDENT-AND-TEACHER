@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">🏢 Kelola Data Jurusan</h4>
</div>

<div class="row">
    <!-- KIRI: FORM TAMBAH JURUSAN -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="mb-0 fw-bold">➕ Tambah Jurusan Baru</h6>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success fw-bold">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger fw-bold">Gagal! Pastikan Kode Jurusan belum pernah digunakan.</div>
                @endif

                <form action="/jurusan" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Kode Jurusan</label>
                        <input type="text" name="kode_jurusan" class="form-control border-secondary" placeholder="Contoh: RPL" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">Nama Jurusan</label>
                        <input type="text" name="nama_jurusan" class="form-control border-secondary" placeholder="Contoh: Rekayasa Perangkat Lunak" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Jurusan</button>
                </form>
            </div>
        </div>
    </div>

    <!-- KANAN: TABEL DAFTAR JURUSAN -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white py-3">
                <h6 class="mb-0 fw-bold">📋 Daftar Jurusan</h6>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-secondary">
                            <tr>
                                <th width="10%">No</th>
                                <th width="20%">Kode</th>
                                <th class="text-start">Nama Jurusan</th>
                                <th width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jurusans as $index => $jurusan)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge bg-primary fs-6">{{ $jurusan->kode_jurusan }}</span></td>
                                <td class="text-start fw-bold">{{ $jurusan->nama_jurusan }}</td>
                                <td>
                                    <!-- Tombol Edit -->
                                    <button class="btn btn-sm btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#editModal{{ $jurusan->id }}">✏️ Edit</button>
                                    
                                    <!-- Tombol Hapus -->
                                    <form action="/jurusan/{{ $jurusan->id }}" method="POST" class="d-inline" onsubmit="return confirm('⚠️ Yakin ingin menghapus jurusan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger fw-bold">🗑️ Hapus</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade text-start" id="editModal{{ $jurusan->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title fw-bold">✏️ Edit Jurusan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="/jurusan/{{ $jurusan->id }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Kode Jurusan</label>
                                                    <input type="text" name="kode_jurusan" class="form-control" value="{{ $jurusan->kode_jurusan }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Nama Jurusan</label>
                                                    <input type="text" name="nama_jurusan" class="form-control" value="{{ $jurusan->nama_jurusan }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary fw-bold">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada data jurusan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection