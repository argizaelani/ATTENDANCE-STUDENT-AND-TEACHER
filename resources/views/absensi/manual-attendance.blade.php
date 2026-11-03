@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">📝 Input Keterangan Absensi</h4>
</div>

@if(session('success'))
    <div class="alert alert-success fw-bold">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger fw-bold">{{ session('error') }}</div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <ul class="nav nav-tabs" id="attendanceTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link {{ request('tab') != 'massal' ? 'active fw-bold' : '' }}" id="satuan-tab" data-bs-toggle="tab" data-bs-target="#satuan" type="button">👤 Input Satuan</button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ request('tab') == 'massal' ? 'active fw-bold' : '' }}" id="massal-tab" data-bs-toggle="tab" data-bs-target="#massal" type="button">👥 Input Massal (Per Kelas)</button>
            </li>
        </ul>
    </div>
    
    <div class="card-body tab-content p-4">
        
        <!-- ========================================== -->
        <!-- TAB 1: INPUT SATUAN                        -->
        <!-- ========================================== -->
        <div class="tab-pane fade {{ request('tab') != 'massal' ? 'show active' : '' }}" id="satuan">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <form action="/manual-attendance" method="POST" class="mt-3">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Pilih Siswa <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select select2-siswa" required>
                                <option value=""></option> 
                                @foreach(\App\Models\Student::orderBy('name')->get() as $siswa)
                                    <option value="{{ $siswa->id }}">{{ $siswa->nis }} - {{ $siswa->name }} (Kelas {{ $siswa->kelas }} {{ $siswa->jurusan }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-2 d-block">💡 <i>Ketik Nama atau NIS untuk mencari.</i></small>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-bold text-secondary">Status Keterangan <span class="text-danger">*</span></label>
                            <select name="status" class="form-select border-secondary py-2" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Izin">Izin</option>
                                <option value="Alfa">Alfa</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Keterangan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: INPUT MASSAL (BULK)                 -->
        <!-- ========================================== -->
        <div class="tab-pane fade {{ request('tab') == 'massal' ? 'show active' : '' }}" id="massal">
            <!-- Form Filter Pencarian Kelas -->
            <form action="/manual-attendance" method="GET" class="row g-2 mb-4 align-items-end bg-light p-3 rounded">
                <input type="hidden" name="tab" value="massal">
                
                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold small text-secondary">Filter Kelas</label>
                    <select name="kelas" class="form-select border-secondary">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($masterKelas as $kls)
                            <option value="{{ $kls->nama_kelas }}" {{ request('kelas') == $kls->nama_kelas ? 'selected' : '' }}>Kelas {{ $kls->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold small text-secondary">Filter Jurusan</label>
                    <select name="jurusan" class="form-select border-secondary">
                        <option value="">-- Semua Jurusan --</option>
                        @foreach($masterJurusan as $jrs)
                            <option value="{{ $jrs->kode_jurusan }}" {{ request('jurusan') == $jrs->kode_jurusan ? 'selected' : '' }}>{{ $jrs->kode_jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-dark w-100 fw-bold">Tampilkan Data</button>
                </div>
                <div class="col-12 col-md-2">
                    <a href="/manual-attendance?tab=massal" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            <!-- Tabel Centang & Form Simpan Massal -->
            @if(request('kelas') || request('jurusan'))
                <form action="/manual-attendance" method="POST">
                    @csrf
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 border border-primary rounded bg-white shadow-sm">
                        <div class="w-50">
                            <label class="form-label fw-bold text-primary mb-1">Pilih Status Untuk Siswa Yang Dicentang:</label>
                            <select name="status" class="form-select border-primary fw-bold text-primary" required>
                                <option value="">-- Tetapkan Status --</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Izin">Izin</option>
                                <option value="Alfa">Alfa</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary btn-lg fw-bold px-5" onclick="return confirm('Yakin ingin menyimpan status absensi untuk siswa yang dicentang?')">
                                💾 Simpan Massal
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%" class="text-center">
                                        <input class="form-check-input" type="checkbox" id="checkAll" style="transform: scale(1.3);">
                                    </th>
                                    <th width="10%">NIS</th>
                                    <th width="45%">Nama Lengkap Siswa</th>
                                    <th width="20%">Kelas</th>
                                    <th width="20%">Jurusan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($studentsBulk as $siswa)
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="{{ $siswa->id }}" style="transform: scale(1.3);">
                                    </td>
                                    <td>{{ $siswa->nis }}</td>
                                    <td class="fw-bold">{{ $siswa->name }}</td>
                                    <td>{{ $siswa->kelas }}</td>
                                    <td>{{ $siswa->jurusan }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Tidak ada data siswa pada kelas/jurusan tersebut.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            @else
                <div class="text-center py-5 text-muted">
                    <h5>👆 Silakan pilih Kelas dan Jurusan terlebih dahulu untuk menampilkan daftar siswa.</h5>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Aktifkan select2 di tab satuan
        $('.select2-siswa').select2({
            theme: 'bootstrap-5',
            placeholder: "-- Ketik nama atau NIS siswa di sini --",
            allowClear: true,
            width: '100%'
        });

        // Script untuk tombol "Centang Semua"
        $('#checkAll').on('click', function() {
            $('.student-checkbox').prop('checked', this.checked);
        });

        // Pastikan checkAll otomatis mati jika salah satu checkbox dimatikan manual
        $('.student-checkbox').on('change', function() {
            if ($('.student-checkbox:checked').length == $('.student-checkbox').length) {
                $('#checkAll').prop('checked', true);
            } else {
                $('#checkAll').prop('checked', false);
            }
        });
    });
</script>
@endpush