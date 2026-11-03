@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">⚙️ Pengaturan Jam Operasional Absensi</h4>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success fw-bold">{{ session('success') }}</div>
                @endif

                <form action="/settings" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Jam Masuk (Mulai Absen)</label>
                        <input type="time" name="jam_masuk" class="form-control" value="{{ $settings['jam_masuk'] ?? '06:00' }}" required>
                        <small class="text-muted">Waktu di mana gerbang/scanner mulai dibuka untuk absen masuk.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Batas Jam Tepat Waktu / Terlambat</label>
                        <input type="time" name="batas_terlambat" class="form-control" value="{{ $settings['batas_terlambat'] ?? '07:15' }}" required>
                        <small class="text-muted">Jika tap setelah jam ini, status otomatis menjadi <b>Terlambat</b>.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">Jam Pulang</label>
                        <input type="time" name="jam_pulang" class="form-control" value="{{ $settings['jam_pulang'] ?? '15:00' }}" required>
                        <small class="text-muted">Waktu standar kepulangan sekolah/instansi.</small>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4 fw-bold">💾 Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection