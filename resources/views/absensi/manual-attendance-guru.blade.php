@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">📝 Input Keterangan Absensi Pegawai</h4>
</div>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success fw-bold">{{ session('success') }}</div>
                @endif

                <form action="/manual-attendance-guru" method="POST" class="mt-3">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">Pilih Pegawai<span class="text-danger">*</span></label>
                        <select name="guru_id" class="form-select select2-guru border-secondary" required>
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nip ? $guru->nip.' - ' : '' }}{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-5">
                        <label class="form-label fw-bold text-secondary">Status Keterangan <span class="text-danger">*</span></label>
                        <select name="status" class="form-select border-secondary py-2" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Izin">Izin (Dinas/Pribadi)</option>
                            <option value="Alfa">Alfa</option>
                        </select>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4 fw-bold">💾 Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Mengaktifkan fitur pencarian pada dropdown guru agar mudah dicari
        $('.select2-guru').select2({
            theme: 'bootstrap-5',
            placeholder: "-- Ketik nama atau NIP guru --",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush