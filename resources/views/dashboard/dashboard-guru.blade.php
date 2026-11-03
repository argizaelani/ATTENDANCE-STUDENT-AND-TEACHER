@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">👨‍💼 Dashboard Kehadiran Pegawai</h4>
    <div class="text-secondary fw-bold">
        📅 {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
    </div>
</div>

<div class="row">
    <!-- KOLOM KIRI: GRAFIK -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 h-100 shadow-sm">
            <div class="card-header fw-bold text-center border-0 pt-4 pb-0 bg-transparent">
                📈 Persentase Kehadiran
            </div>
            <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 280px;">
                <canvas id="chartGuru"></canvas>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: TABEL -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 h-100 shadow-sm">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th class="text-start" width="25%">Nama Pegawai</th>
                                <th width="25%">Absen Masuk</th>
                                <th width="25%">Absen Pulang</th>
                                <th width="20%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($absensiGuru as $index => $absen)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-start fw-bold">
                                    {{ $absen->guru->nama ?? 'Tidak Diketahui' }}
                                    <div class="small text-muted fw-normal">{{ $absen->guru->mata_pelajaran ?? 'Pegawai' }}</div>
                                </td>
                                
                                <td>
                                    @if($absen->status == 'Tepat Waktu')
                                        <span class="badge bg-success shadow-sm mb-1">Tepat Waktu</span>
                                    @elseif($absen->status == 'Terlambat')
                                        <span class="badge bg-warning text-dark shadow-sm mb-1">Terlambat</span>
                                    @else
                                        <span class="badge bg-danger shadow-sm mb-1">{{ $absen->status }}</span>
                                    @endif
                                    <br>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        {{ $absen->created_at->translatedFormat('d F Y') }}
                                    </small>
                                    <small class="text-dark fw-bold">
                                        <i class="fas fa-sign-in-alt me-1 text-primary"></i> 
                                        {{ $absen->created_at->format('H:i:s') }} WIB
                                    </small>
                                </td>

                                <td>
                                    @if($absen->waktu_pulang)
                                        <span class="badge shadow-sm mb-1" style="background-color: #ea580c; color: white;">
                                            Sudah Pulang
                                        </span>
                                        <br>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                            {{ $absen->created_at->translatedFormat('d F Y') }}
                                        </small>
                                        <small class="text-dark fw-bold">
                                            <i class="fas fa-sign-out-alt me-1" style="color: #ea580c;"></i> 
                                            {{ $absen->waktu_pulang }} WIB
                                        </small>
                                    @else
                                        <span class="badge bg-secondary shadow-sm">Belum Pulang</span>
                                        <br><small class="text-muted">-</small>
                                    @endif
                                </td>

                                <td>
                                    @if($absen->photo_path === 'Lupa Kartu')
                                        <span class="badge bg-warning text-dark shadow-sm px-3 py-2">
                                            <i class="fas fa-id-card-clip me-1"></i> Lupa Kartu
                                        </span>
                                    @elseif($absen->photo_path === 'manual' || in_array($absen->status, ['Sakit', 'Izin', 'Alfa']))
                                        <span class="badge bg-secondary shadow-sm px-3 py-2">
                                            <i class="fas fa-keyboard me-1"></i> Manual
                                        </span>
                                    @elseif($absen->photo_path)
                                        <!-- FOTO DENGAN FUNGSI KLIK -->
                                        <img src="{{ asset('storage/' . $absen->photo_path) }}" alt="Foto" 
                                             class="img-thumbnail rounded-circle shadow-sm" 
                                             style="height: 55px; width: 55px; object-fit: cover; border: 2px solid #ea580c; cursor: pointer; transition: transform 0.2s;"
                                             onmouseover="this.style.transform='scale(1.1)'" 
                                             onmouseout="this.style.transform='scale(1)'"
                                             onclick="showPhotoModal(this.src, '{{ addslashes($absen->guru->nama ?? 'Pegawai') }}')">
                                    @else
                                        <span class="text-muted fst-italic small">Tidak Ada</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada pegawai yang melakukan absensi hari ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL POPUP FOTO BESAR -->
<!-- ========================================== -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-transparent border-0 position-relative">
            
            <!-- Tombol Close Custom (Dijamin 100% Muncul) -->
            <button type="button" class="shadow-lg" data-bs-dismiss="modal" aria-label="Close" 
                    style="position: absolute; right: 0px; top: 0px; z-index: 1060; width: 40px; height: 40px; border-radius: 50%; background-color: #ea580c; border: 3px solid white; color: white; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 0; transition: transform 0.2s;"
                    onmouseover="this.style.transform='scale(1.1)'" 
                    onmouseout="this.style.transform='scale(1)'">
                <!-- Menggunakan simbol silang murni bawaan browser -->
                <span style="font-size: 28px; font-weight: bold; line-height: 0; margin-top: -2px;">&times;</span>
            </button>

            <!-- Area Gambar -->
            <div class="modal-body text-center p-0">
                <div class="bg-white p-2 rounded-4 shadow-lg d-inline-block w-100">
                    <img id="modalImage" src="" alt="Foto Besar" class="img-fluid rounded-3" style="max-height: 75vh; width: 100%; object-fit: cover;">
                    
                    <div class="mt-3 mb-2 text-dark fw-bold fs-5 px-4 py-2 rounded-pill d-inline-block shadow-sm" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        📸 <span id="modalPhotoName"></span>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- SCRIPT JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fungsi Menampilkan Modal Foto
    function showPhotoModal(imageSrc, personName) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('modalPhotoName').innerText = personName;
        var myModal = new bootstrap.Modal(document.getElementById('photoModal'));
        myModal.show();
    }

    // Fungsi Grafik
    const ctx = document.getElementById('chartGuru').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Tepat Waktu', 'Terlambat', 'Sakit', 'Izin', 'Alfa'],
            datasets: [{
                data: {!! json_encode($statistikGuru ?? [0,0,0,0,0]) !!},
                backgroundColor: ['rgba(72, 187, 120, 0.8)', 'rgba(237, 137, 54, 0.8)', 'rgba(66, 153, 225, 0.8)', 'rgba(159, 122, 234, 0.8)', 'rgba(245, 101, 101, 0.8)'],
                borderColor: 'rgba(255, 255, 255, 0.5)', borderWidth: 2, hoverOffset: 6
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom', labels: { color: '#2d3748', font: { family: 'Poppins', size: 12 } } } } }
    });
</script>
@endsection