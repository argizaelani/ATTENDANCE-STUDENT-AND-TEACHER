<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bypass Absensi (Lupa Kartu) - SI-SAKTI</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts (Poppins) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #fffaf0; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0; position: relative; overflow-x: hidden; }
        
        /* ANIMASI GELOMBANG AIR */
        .wave-container { position: absolute; bottom: 0; left: 0; width: 100%; height: 25vh; z-index: -1; overflow: hidden; }
        .waves { position: relative; width: 100%; height: 100%; min-height: 100px; max-height: 250px; }
        .parallax > use { animation: move-forever 25s cubic-bezier(.55,.5,.45,.5) infinite; }
        .parallax > use:nth-child(1) { animation-delay: -2s; animation-duration: 7s; }
        .parallax > use:nth-child(2) { animation-delay: -3s; animation-duration: 10s; }
        .parallax > use:nth-child(3) { animation-delay: -4s; animation-duration: 13s; }
        .parallax > use:nth-child(4) { animation-delay: -5s; animation-duration: 20s; }
        @keyframes move-forever { 0% { transform: translate3d(-90px,0,0); } 100% { transform: translate3d(85px,0,0); } }

        /* PANEL GLASSMORPHISM */
        .glass-panel { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(249, 115, 22, 0.3); border-radius: 25px; box-shadow: 0 20px 40px rgba(234, 88, 12, 0.15); width: 100%; max-width: 650px; padding: 40px; z-index: 10; margin: 20px 0; }
        .main-title { font-size: 2rem; font-weight: 800; color: #ea580c; margin-bottom: 5px; text-align: center; }
        .sub-title { font-size: 1rem; font-weight: 600; color: #64748b; text-align: center; margin-bottom: 30px; }

        /* TAB BUTTONS (Pilihan Siswa/Guru) */
        .tab-btn { flex: 1; padding: 12px; text-align: center; font-weight: 700; color: #64748b; background: #f1f5f9; border: 2px solid transparent; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; }
        .tab-input:checked + .tab-btn { background: #fff7ed; color: #ea580c; border-color: #ea580c; box-shadow: 0 4px 10px rgba(234, 88, 12, 0.15); }
        .tab-input { display: none; }

        /* SCROLLABLE LIST KARTU */
        .user-list-container { max-height: 280px; overflow-y: auto; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; padding: 10px; margin-bottom: 20px; }
        .user-list-container::-webkit-scrollbar { width: 8px; }
        .user-list-container::-webkit-scrollbar-thumb { background: rgba(249, 115, 22, 0.4); border-radius: 10px; }

        .user-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 15px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; }
        .user-card:hover { border-color: #fdba74; background: #fffaf0; }
        
        /* Indikator Kartu Terpilih */
        .user-card.selected { background: #ea580c; color: white; border-color: #ea580c; box-shadow: 0 4px 10px rgba(234, 88, 12, 0.3); }
        .user-card.selected .text-muted { color: #ffedd5 !important; }

        /* PREVIEW AREA */
        .selection-preview { background: #fff7ed; border-left: 5px solid #ea580c; border-radius: 10px; padding: 15px; margin-bottom: 20px; display: none; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

        .btn-orange { background-color: #ea580c; color: white; border: none; border-radius: 12px; }
        .btn-orange:hover { background-color: #c2410c; color: white; }
        .btn-orange:disabled { background-color: #fdba74; cursor: not-allowed; }
    </style>
</head>
<body>

    <div class="wave-container">
        <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
            <defs><path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" /></defs>
            <g class="parallax">
                <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(249, 115, 22, 0.7)" />
                <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(249, 115, 22, 0.5)" />
                <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(249, 115, 22, 0.3)" />
                <use xlink:href="#gentle-wave" x="48" y="7" fill="#fdba74" />
            </g>
        </svg>
    </div>

    <div class="glass-panel">
        <h1 class="main-title"><i class="fas fa-keyboard me-2"></i> Ketik Manual</h1>
        <div class="sub-title">Cari dan pilih nama untuk absen tanpa kartu RFID</div>

        @if(session('success'))
            <div class="alert alert-success fw-bold text-center border-0 shadow-sm" style="border-radius: 12px;">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger fw-bold text-center border-0 shadow-sm" style="border-radius: 12px;">
                <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
            </div>
        @endif

        <!-- URL hardcode yang 100% aman dari missing route error -->
        <form action="{{ url('/bypass-absensi') }}" method="POST" id="bypassForm">
            @csrf
            
            <!-- INPUT TERSEMBUNYI -->
            <input type="hidden" name="id_user" id="selectedUserId" required>

            <!-- TABS PILIHAN (Ganti Radio Button Lama) -->
            <div class="d-flex gap-3 mb-4">
                <label class="w-100">
                    <input type="radio" name="tipe" class="tab-input" value="siswa" checked onchange="switchTab('siswa')">
                    <div class="tab-btn"><i class="fas fa-user-graduate me-2"></i>Siswa</div>
                </label>
                <label class="w-100">
                    <input type="radio" name="tipe" class="tab-input" value="guru" onchange="switchTab('guru')">
                    <div class="tab-btn"><i class="fas fa-chalkboard-teacher me-2"></i>Pegawai</div>
                </label>
            </div>

            <!-- KOTAK PENCARIAN -->
            <div class="mb-3">
                <div class="input-group input-group-lg shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0 ps-0 fw-bold" placeholder="Cari Nama / Kelas Siswa..." onkeyup="filterList()">
                </div>
            </div>

            <!-- DAFTAR KARTU SISWA -->
            <div id="listSiswa" class="user-list-container">
                @forelse($students ?? [] as $siswa)
                    @php
                        $namaSiswa = $siswa->name ?? $siswa->nama ?? 'Tanpa Nama';
                        $infoSiswa = 'Kelas ' . ($siswa->kelas ?? '-') . ' • ' . ($siswa->jurusan ?? '');
                    @endphp
                    <!-- Penggunaan data-attribute murni agar bebas Error 500 -->
                    <div class="user-card item-siswa" 
                         data-id="{{ $siswa->id }}" 
                         data-nama="{{ $namaSiswa }}" 
                         data-info="{{ $infoSiswa }}"
                         onclick="selectUser(this)">
                        <div class="flex-grow-1">
                            <div class="fw-bold fs-5 nama-text">{{ $namaSiswa }}</div>
                            <div class="small text-muted fw-semibold info-text">{{ $infoSiswa }}</div>
                        </div>
                        <div class="text-end">
                            <i class="fas fa-check-circle fs-4 icon-check" style="opacity: 0;"></i>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted fw-bold">Belum ada data siswa.</div>
                @endforelse
                <div id="noDataSiswa" class="text-center py-4 text-muted fw-bold d-none">❌ Nama siswa tidak ditemukan</div>
            </div>

            <!-- DAFTAR KARTU GURU -->
            <div id="listGuru" class="user-list-container d-none">
                @forelse($gurus ?? [] as $guru)
                    @php
                        $namaGuru = $guru->nama ?? $guru->name ?? 'Tanpa Nama';
                        $infoGuru = ($guru->mata_pelajaran ?? $guru->jabatan ?? '-');
                    @endphp
                    <div class="user-card item-guru" 
                         data-id="{{ $guru->id }}" 
                         data-nama="{{ $namaGuru }}" 
                         data-info="{{ $infoGuru }}"
                         onclick="selectUser(this)">
                        <div class="flex-grow-1">
                            <div class="fw-bold fs-5 nama-text">{{ $namaGuru }}</div>
                            <div class="small text-muted fw-semibold info-text">{{ $infoGuru }}</div>
                        </div>
                        <div class="text-end">
                            <i class="fas fa-check-circle fs-4 icon-check" style="opacity: 0;"></i>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted fw-bold">Belum ada data Pegawai.</div>
                @endforelse
                <div id="noDataGuru" class="text-center py-4 text-muted fw-bold d-none">❌ Nama Pegawai tidak ditemukan</div>
            </div>

            <!-- PREVIEW YANG DIPILIH -->
            <div id="selectionPreview" class="selection-preview shadow-sm">
                <div class="text-muted small fw-bold mb-1">Terkonfirmasi Memilih:</div>
                <div class="fw-bold fs-4 text-dark" id="previewName">-</div>
                <div class="text-secondary fw-semibold small" id="previewInfo">-</div>
            </div>

            <div class="d-grid mt-2">
                <button type="submit" id="btnSubmit" class="btn btn-orange btn-lg fw-bold py-3 shadow-sm" disabled>
                    <i class="fas fa-check-double me-2"></i> Simpan Kehadiran
                </button>
            </div>
            
            <div class="text-center mt-4">
                <a href="/scan" class="text-muted text-decoration-none small fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Layar Scan
                </a>
            </div>
        </form>
    </div>

    <script>
        let currentType = 'siswa';

        // Fungsi mengganti Tab Siswa/Guru
        function switchTab(type) {
            currentType = type;
            document.getElementById('searchInput').value = '';
            document.getElementById('searchInput').placeholder = type === 'siswa' ? 'Cari Nama / Kelas Siswa...' : 'Cari Nama Pegawai...';
            
            document.getElementById('listSiswa').classList.toggle('d-none', type !== 'siswa');
            document.getElementById('listGuru').classList.toggle('d-none', type !== 'guru');
            
            resetSelection();
            filterList();
        }

        // Fungsi Pencarian (Live Search)
        function filterList() {
            let keyword = document.getElementById('searchInput').value.toLowerCase();
            let items = document.querySelectorAll('.item-' + currentType);
            let hasVisibleItem = false;

            items.forEach(item => {
                let nama = item.querySelector('.nama-text').innerText.toLowerCase();
                let info = item.querySelector('.info-text').innerText.toLowerCase();
                
                if (nama.includes(keyword) || info.includes(keyword)) {
                    item.style.display = "flex";
                    hasVisibleItem = true;
                } else {
                    item.style.display = "none";
                }
            });

            document.getElementById(currentType === 'siswa' ? 'noDataSiswa' : 'noDataGuru').classList.toggle('d-none', hasVisibleItem);
        }

        // Fungsi Memilih Kartu Nama
        function selectUser(element) {
            // Menarik data dari atribut HTML murni (Menghindari Error 500)
            let id = element.getAttribute('data-id');
            let nama = element.getAttribute('data-nama');
            let info = element.getAttribute('data-info');

            // Hapus seleksi sebelumnya
            let allCards = document.querySelectorAll('.user-card');
            allCards.forEach(card => {
                card.classList.remove('selected');
                card.querySelector('.icon-check').style.opacity = '0';
            });

            // Aktifkan kartu ini
            element.classList.add('selected');
            element.querySelector('.icon-check').style.opacity = '1';
            
            // Masukkan ke Form Input
            document.getElementById('selectedUserId').value = id;
            document.getElementById('selectionPreview').style.display = 'block';
            document.getElementById('previewName').innerText = nama;
            document.getElementById('previewInfo').innerText = info;

            // Hidupkan Tombol Submit
            let btn = document.getElementById('btnSubmit');
            btn.disabled = false;
            btn.innerHTML = `<i class="fas fa-check-double me-2"></i> Konfirmasi Hadir: ${nama}`;
        }

        // Fungsi Reset Pilihan
        function resetSelection() {
            document.getElementById('selectedUserId').value = '';
            document.getElementById('selectionPreview').style.display = 'none';
            
            let btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = `<i class="fas fa-check-double me-2"></i> Simpan Kehadiran`;

            let allCards = document.querySelectorAll('.user-card');
            allCards.forEach(card => {
                card.classList.remove('selected');
                card.querySelector('.icon-check').style.opacity = '0';
            });
        }
    </script>
</body>
</html>