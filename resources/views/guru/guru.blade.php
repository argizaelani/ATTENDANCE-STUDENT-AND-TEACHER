@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">👨‍💼 Kelola Data Pegawai</h4>
</div>

<div class="row">
    <!-- KIRI: FORM TAMBAH -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="mb-0 fw-bold">➕ Tambah Pegawai Baru</h6>
            </div>
            <div class="card-body p-4">
                @if(session('success')) <div class="alert alert-success fw-bold">{{ session('success') }}</div> @endif
                @if($errors->any()) <div class="alert alert-danger fw-bold">Gagal! NIP atau UID mungkin sudah digunakan.</div> @endif
                
                <form action="/guru" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">NIP (Opsional)</label>
                        <input type="text" name="nip" class="form-control border-secondary" placeholder="Biarkan kosong jika tidak ada">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control border-secondary" placeholder="Contoh: Budi Santoso, S.Pd" required>
                    </div>
                    
                    <!-- MATA PELAJARAN DIUBAH MENJADI DROPDOWN JABATAN -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Jabatan / Posisi</label>
                        <select name="mata_pelajaran" class="form-select border-secondary" required>
                            <option value="" disabled selected>-- Pilih Jabatan --</option>
                            <option value="Kepala Sekolah">Kepala Sekolah</option>
                            <option value="Guru">Guru</option>
                            <option value="Tata Usaha">Tata Usaha</option>
                            <option value="Satpam">Satpam</option>
                            <option value="Petugas Kebersihan">Petugas Kebersihan</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">UID Kartu RFID</label>
                        <input type="text" name="rfid_uid" class="form-control border-secondary" placeholder="Tap kartu di sini..." required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Data Pegawai</button>
                </form>
            </div>
        </div>
    </div>

    <!-- KANAN: TABEL -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white py-3">
                <h6 class="mb-0 fw-bold">📋 Daftar Pegawai</h6>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-secondary">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">NIP</th>
                                <th class="text-start">Nama Pegawai</th>
                                <th width="25%">Jabatan</th>
                                <th width="15%">UID RFID</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gurus as $index => $guru)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $guru->nip ?? '-' }}</td>
                                <td class="text-start fw-bold">{{ $guru->nama }}</td>
                                <td>
                                    <!-- Memberikan warna badge untuk jabatan agar lebih rapi -->
                                    <span class="badge bg-info text-dark shadow-sm">{{ $guru->mata_pelajaran ?? '-' }}</span>
                                </td>
                                <td><code>{{ $guru->rfid_uid ?? 'Belum ada' }}</code></td>
                                <td>
                                    <!-- Tombol Edit -->
                                    <button class="btn btn-sm btn-warning fw-bold mb-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $guru->id }}">✏️</button>
                                    
                                    <!-- Tombol Hapus -->
                                    <form action="/guru/{{ $guru->id }}" method="POST" class="d-inline" onsubmit="return confirm('⚠️ Yakin hapus data pegawai ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger fw-bold mb-1">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data pegawai.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================================================= -->
<!-- MODAL EDIT DATA PEGAWAI -->
<!-- ================================================= -->
@foreach($gurus as $guru)
<div class="modal fade text-start" id="editModal{{ $guru->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold"> Edit Data Pegawai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/guru/{{ $guru->id }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ $guru->nip }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="{{ $guru->nama }}" required>
                    </div>
                    
                    <!-- DROPDOWN EDIT JABATAN (Otomatis memilih data yang lama) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jabatan / Posisi</label>
                        <select name="mata_pelajaran" class="form-select" required>
                            <option value="" disabled>-- Pilih Jabatan --</option>
                            <option value="Kepala Sekolah" {{ $guru->mata_pelajaran == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                            <option value="Guru" {{ $guru->mata_pelajaran == 'Guru' ? 'selected' : '' }}>Guru</option>
                            <option value="Tata Usaha" {{ $guru->mata_pelajaran == 'Tata Usaha' ? 'selected' : '' }}>Tata Usaha</option>
                            <option value="Satpam" {{ $guru->mata_pelajaran == 'Satpam' ? 'selected' : '' }}>Satpam</option>
                            <option value="Petugas Kebersihan" {{ $guru->mata_pelajaran == 'Petugas Kebersihan' ? 'selected' : '' }}>Petugas Kebersihan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">UID Kartu RFID</label>
                        <input type="text" name="rfid_uid" class="form-control" value="{{ $guru->rfid_uid }}" required>
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
@endforeach

@endsection