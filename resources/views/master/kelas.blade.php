@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">🚪 Kelola Data Kelas</h4>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3"><h6 class="mb-0 fw-bold">➕ Tambah Kelas Baru</h6></div>
            <div class="card-body p-4">
                @if(session('success')) <div class="alert alert-success fw-bold">{{ session('success') }}</div> @endif
                @if($errors->any()) <div class="alert alert-danger fw-bold">Gagal! Nama kelas sudah ada.</div> @endif
                <form action="/kelas" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: 10" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Kelas</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white py-3"><h6 class="mb-0 fw-bold">📋 Daftar Kelas</h6></div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-secondary">
                            <tr><th width="10%">No</th><th>Nama Kelas</th><th width="25%">Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($kelases as $index => $kls)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-bold"><span class="badge bg-secondary fs-6">{{ $kls->nama_kelas }}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#editModal{{ $kls->id }}">✏️ Edit</button>
                                    <form action="/kelas/{{ $kls->id }}" method="POST" class="d-inline" onsubmit="return confirm('⚠️ Yakin hapus kelas ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger fw-bold">🗑️ Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <!-- Modal Edit -->
                            <div class="modal fade text-start" id="editModal{{ $kls->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning"><h5 class="modal-title fw-bold">✏️ Edit Kelas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                        <form action="/kelas/{{ $kls->id }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-body">
                                                <label class="form-label fw-bold">Nama Kelas</label>
                                                <input type="text" name="nama_kelas" class="form-control" value="{{ $kls->nama_kelas }}" required>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary fw-bold">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr><td colspan="3" class="text-muted py-4">Belum ada data kelas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection