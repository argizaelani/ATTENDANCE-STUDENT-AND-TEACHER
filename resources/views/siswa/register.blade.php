@extends('layouts.app')

@section('content')
<div class="row">
    
    <!-- KIRI: PENDAFTARAN & IMPORT -->
    <div class="col-md-7 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="fw-bold mb-3">Registrasi Data Siswa</h5>
                <ul class="nav nav-tabs" id="registerTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button">Daftar Manual</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" id="import-tab" data-bs-toggle="tab" data-bs-target="#import" type="button">Import Excel</button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body tab-content p-4">
                @if(session('success'))
                    <div class="alert alert-success fw-bold">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger fw-bold">Pastikan data terisi dengan benar atau NIS/UID belum digunakan.</div>
                @endif

                <!-- TAB MANUAL -->
                <div class="tab-pane fade show active" id="manual">
                    <form action="/register" method="POST"> 
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">NIS</label>
                                <input type="text" name="nis" class="form-control border-secondary" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control border-secondary" required>
                            </div>
                            
                            <!-- TERINTEGRASI: Master Kelas -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary">Kelas</label>
                                <select name="kelas" class="form-select border-secondary" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach($masterKelas as $kls)
                                        <option value="{{ $kls->nama_kelas }}">{{ $kls->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- TERINTEGRASI: Master Jurusan -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary">Jurusan</label>
                                <select name="jurusan" class="form-select border-secondary" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach($masterJurusan as $jrs)
                                        <option value="{{ $jrs->kode_jurusan }}">{{ $jrs->kode_jurusan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- TERINTEGRASI: Master Tahun Ajaran -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary">Tahun Ajaran</label>
                                <select name="tahun_ajaran" class="form-select border-secondary" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach($masterTahun as $thn)
                                        <option value="{{ $thn->nama_tahun }}">{{ $thn->nama_tahun }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary">UID Kartu RFID</label>
                                <input type="text" name="rfid_uid" class="form-control border-secondary" placeholder="Scan kartu di sini..." required>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Data Siswa</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TAB IMPORT -->
                <div class="tab-pane fade" id="import">
                    <div class="alert alert-info">💡 <strong>Petunjuk:</strong> Unduh template Excel terlebih dahulu, isi data siswa, lalu unggah file tersebut di sini.</div>
                    <a href="/students/template" class="btn btn-outline-success fw-bold mb-4">⬇️ Download Template Excel</a>
                    <form action="/students/import" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Unggah File Excel (.xlsx / .csv)</label>
                            <input type="file" name="file" class="form-control border-secondary" required>
                        </div>
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success px-4 fw-bold">Mulai Import Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- KANAN: SINKRONISASI KARTU -->
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-dark text-white pt-3 pb-2">
                <h5 class="fw-bold text-center">🔗 Hubungkan Kartu RFID</h5>
            </div>
            <div class="card-body p-4 bg-white">
                <p class="text-muted small mb-4">Gunakan fitur ini untuk menautkan kartu fisik baru kepada siswa yang sudah terdaftar di sistem.</p>
                <form action="/students/sync" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">Cari Siswa</label>
                        <select name="student_id" class="form-select select2-siswa" required>
                            <option value=""></option>
                            @foreach(\App\Models\Student::orderBy('name')->get() as $siswa)
                                <option value="{{ $siswa->id }}">{{ $siswa->nis }} - {{ $siswa->name }} (Kelas {{ $siswa->kelas }} {{ $siswa->jurusan }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">Tap Kartu RFID Baru</label>
                        <input type="text" name="rfid_uid" class="form-control border-secondary form-control-lg text-center" placeholder="UID akan muncul di sini" required autofocus>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-dark fw-bold">Tautkan Kartu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- FORM PENCARIAN & FILTER DAFTAR SISWA -->
    <!-- ============================================== -->
    <div class="col-12">
        <form action="/register" method="GET" class="row g-2 mb-4 align-items-center">
            <div class="col-12 col-md-4">
                <input type="text" name="search" class="form-control border-secondary" placeholder="🔍 Cari Nama, NIS, atau UID..." value="{{ request('search') }}">
            </div>
            
            <!-- TERINTEGRASI: Filter Kelas -->
            <div class="col-6 col-md-3">
                <select name="kelas" class="form-select border-secondary" onchange="this.form.submit()">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($masterKelas as $kls)
                        <option value="{{ $kls->nama_kelas }}" {{ request('kelas') == $kls->nama_kelas ? 'selected' : '' }}>
                            Kelas {{ $kls->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- TERINTEGRASI: Filter Jurusan -->
            <div class="col-6 col-md-3">
                <select name="jurusan" class="form-select border-secondary" onchange="this.form.submit()">
                    <option value="">-- Semua Jurusan --</option>
                    @foreach($masterJurusan as $jrs)
                        <option value="{{ $jrs->kode_jurusan }}" {{ request('jurusan') == $jrs->kode_jurusan ? 'selected' : '' }}>
                            {{ $jrs->kode_jurusan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100 fw-bold">Cari</button>
                <a href="/register" class="btn btn-outline-secondary" title="Reset Pencarian">🔄</a>
            </div>
        </form>
    </div>

    <!-- ============================================== -->
    <!-- TABEL DAFTAR SISWA -->
    <!-- ============================================== -->
    <div class="col-12 mt-2">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white pt-3 pb-2">
                <h5 class="fw-bold">📋 Daftar Data Siswa</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-secondary">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">NIS</th>
                                <th class="text-start">Nama Siswa</th>
                                <th width="8%">Kelas</th>
                                <th width="10%">Jurusan</th>
                                <th width="12%">Tahun Ajaran</th>
                                <th width="15%">UID RFID</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $siswa)
                            <tr>
                                <td>{{ $students->firstItem() + $index }}</td>
                                <td>{{ $siswa->nis }}</td>
                                <td class="text-start fw-bold">{{ $siswa->name }}</td>
                                <td><span class="badge bg-secondary">{{ $siswa->kelas }}</span></td>
                                <td class="fw-bold text-primary">{{ $siswa->jurusan }}</td>
                                <td class="text-muted">{{ $siswa->tahun_ajaran }}</td>
                                <td><code>{{ $siswa->rfid_uid }}</code></td>
                                <td>
                                    <button class="btn btn-sm btn-warning fw-bold mb-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $siswa->id }}">
                                        Edit
                                    </button>
                                    <form action="/students/{{ $siswa->id }}" method="POST" class="d-inline" onsubmit="return confirm('⚠️ YAKIN INGIN MENGHAPUS {{ $siswa->name }}?\nSeluruh data absensinya juga mungkin akan terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger fw-bold mb-1">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada data siswa yang terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-end mt-3">
                    {{ $students->links('pagination::bootstrap-5') }}
                </div>
                
            </div>
        </div>
    </div>

</div>

<!-- ============================================== -->
<!-- MODAL EDIT SISWA DIPINDAHKAN KELUAR KE SINI -->
<!-- ============================================== -->
@foreach($students as $siswa)
<div class="modal fade text-start" id="editModal{{ $siswa->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold">✏️ Edit Data: {{ $siswa->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/students/{{ $siswa->id }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">NIS</label>
                        <input type="text" name="nis" class="form-control" value="{{ $siswa->nis }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ $siswa->name }}" required>
                    </div>
                    
                    <div class="row mb-3">
                        <!-- TERINTEGRASI: Edit Master Kelas -->
                        <div class="col-4">
                            <label class="form-label fw-bold">Kelas</label>
                            <select name="kelas" class="form-select" required>
                                @foreach($masterKelas as $kls)
                                    <option value="{{ $kls->nama_kelas }}" {{ $siswa->kelas == $kls->nama_kelas ? 'selected' : '' }}>
                                        {{ $kls->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- TERINTEGRASI: Edit Master Jurusan -->
                        <div class="col-8">
                            <label class="form-label fw-bold">Jurusan</label>
                            <select name="jurusan" class="form-select" required>
                                @foreach($masterJurusan as $jrs)
                                    <option value="{{ $jrs->kode_jurusan }}" {{ $siswa->jurusan == $jrs->kode_jurusan ? 'selected' : '' }}>
                                        {{ $jrs->kode_jurusan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- TERINTEGRASI: Edit Master Tahun Ajaran -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tahun Ajaran</label>
                        <select name="tahun_ajaran" class="form-select" required>
                            @foreach($masterTahun as $thn)
                                <option value="{{ $thn->nama_tahun }}" {{ $siswa->tahun_ajaran == $thn->nama_tahun ? 'selected' : '' }}>
                                    {{ $thn->nama_tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">UID Kartu RFID</label>
                        <input type="text" name="rfid_uid" class="form-control" value="{{ $siswa->rfid_uid }}" required>
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-siswa').select2({
            theme: 'bootstrap-5',
            placeholder: "-- Ketik Nama atau NIS --",
            allowClear: true,
            width: '100%'
        });
        $('#manual-tab').on('shown.bs.tab', function () {
            $('input[name="rfid_uid"]').first().focus();
        });
    });
</script>
@endpush