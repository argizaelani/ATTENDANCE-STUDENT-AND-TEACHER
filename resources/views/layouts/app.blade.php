<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SI-SAKTI')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts (Poppins) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        /* -------------------------------------------
           1. PENGATURAN DASAR & LATAR BELAKANG
           ------------------------------------------- */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);
            background-attachment: fixed;
            color: #2d3748;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }

        /* -------------------------------------------
           2. TATA LETAK UTAMA: SIDEBAR & CONTENT AREA
           ------------------------------------------- */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 240px;
            z-index: 1000;
            transition: all 0.3s ease-in-out;
            overflow-y: auto;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        }

        .content-area {
            margin-left: 240px; 
            width: calc(100% - 240px); 
            padding: 24px 24px 24px 20px; 
            min-height: 100vh;
            transition: all 0.3s ease-in-out;
            /* Flexbox agar footer copyright selalu di bawah */
            display: flex;
            flex-direction: column;
        }

        /* Navigasi Sidebar */
        .sidebar .nav-link {
            color: #4a5568 !important;
            border-radius: 12px;
            margin: 4px 16px;
            padding: 10px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.3);
            color: #1a202c !important;
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.5);
            color: #2b6cb0 !important;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        /* -------------------------------------------
           3. TIPOGRAFI & KARTU KONTEN (GLASSMORPHISM)
           ------------------------------------------- */
        h1, h2, h3, h4, h5, h6 { font-weight: 700; color: #1a202c; letter-spacing: -0.5px; }
        .text-secondary, .text-muted { color: #4a5568 !important; font-weight: 500; }
        .fw-bold { font-weight: 600 !important; }

        .card {
            background: rgba(255, 255, 255, 0.35) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07) !important;
            overflow: hidden;
        }

        .card-header {
            background: rgba(255, 255, 255, 0.25) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
            padding: 20px 24px;
        }

        /* -------------------------------------------
           4. TEMA TABEL SUPER PREMIUM (Floating Rows)
           ------------------------------------------- */
        .table {
            --bs-table-bg: transparent;
            --bs-table-color: #2d3748;
            border-collapse: separate; 
            border-spacing: 0 10px; 
            margin-top: -10px; 
        }
        
        .table > thead > tr > th {
            background: rgba(255, 255, 255, 0.2) !important;
            color: #4a5568 !important;
            border: none !important;
            font-weight: 700;
            padding: 16px;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
        }

        .table > thead > tr > th:first-child { border-top-left-radius: 14px; border-bottom-left-radius: 14px; }
        .table > thead > tr > th:last-child { border-top-right-radius: 14px; border-bottom-right-radius: 14px; }

        .table > tbody > tr {
            background: rgba(255, 255, 255, 0.4) !important;
            box-shadow: 0 2px 10px rgba(31, 38, 135, 0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .table > tbody > tr > td {
            vertical-align: middle;
            padding: 16px;
            border: none !important; 
            color: #2d3748;
            font-size: 0.9rem;
        }

        .table > tbody > tr > td:first-child { border-top-left-radius: 14px; border-bottom-left-radius: 14px; }
        .table > tbody > tr > td:last-child { border-top-right-radius: 14px; border-bottom-right-radius: 14px; }

        .table-hover > tbody > tr:hover {
            background: rgba(255, 255, 255, 0.65) !important;
            transform: translateY(-3px) scale(1.005);
            box-shadow: 0 8px 20px rgba(31, 38, 135, 0.08);
        }

        /* -------------------------------------------
           5. TOMBOL, INPUT, MODAL
           ------------------------------------------- */
        .btn {
            border-radius: 12px; font-weight: 600; transition: all 0.3s;
            backdrop-filter: blur(5px); border: 1px solid rgba(255, 255, 255, 0.5) !important;
        }
        .btn:active { transform: scale(0.95); }
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            border-radius: 12px; padding: 12px 16px; color: #2d3748 !important; font-weight: 500;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.7) !important;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.4) !important;
            border-color: rgba(255, 255, 255, 0.9) !important;
        }
        
        /* Modal Fix */
        .modal {
            position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important;
            z-index: 99999 !important; background-color: rgba(15, 23, 42, 0.5) !important; backdrop-filter: blur(8px);
        }
        .modal-dialog { z-index: 100000 !important; margin: 1.75rem auto; }
        .modal-content {
            background: rgba(255, 255, 255, 0.95) !important; backdrop-filter: blur(20px); border-radius: 20px !important;
            border: 1px solid rgba(255, 255, 255, 0.8) !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }
        .modal-body { max-height: 70vh; overflow-y: auto; }

        /* -------------------------------------------
           6. RESPONSIVITAS LAYAR (MOBILE & TABLET)
           ------------------------------------------- */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .content-area { margin-left: 0 !important; width: 100% !important; padding: 15px; }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar py-4 shadow-lg d-flex flex-column">
        
        <!-- LOGO & BRANDING DI SIDEBAR -->
        <div class="text-center mb-4 px-3">
            <!-- Ganti ukuran width sesuai kebutuhan, pastikan file ada di public/images/logo.png -->
            <img src="{{ asset('images/logo.png') }}" alt="Logo Sekolah" style="width: 100px; height: auto; filter: drop-shadow(0px 3px 5px rgba(0,0,0,0.15));" onerror="this.style.display='none'">
        </div>
        
        <ul class="nav flex-column mb-auto gap-1">
            <li class="nav-item">
                <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                    📊 Dashboard Siswa
                </a>
            </li>
            <li class="nav-item">
                <a href="/dashboard-guru" class="nav-link {{ request()->is('dashboard-guru') ? 'active' : '' }}">
                    👨‍🏫 Dashboard Pegawai
                </a>
            </li>

            <!-- REKAP ABSENSI -->
            <li class="nav-item mt-1">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#menuRekap" role="button" aria-expanded="{{ request()->is('report-guru', 'report') ? 'true' : 'false' }}">
                    <span class="fw-bold" style="letter-spacing: 1px; font-size: 0.85rem;">📅 Rekap Absensi</span>
                    <span style="font-size: 10px;">▼</span>
                </a>
                <div class="collapse {{ request()->is('report-guru', 'report') ? 'show' : '' }}" id="menuRekap">
                    <ul class="nav flex-column ms-3 border-start border-light ps-2 mb-2">
                        <li class="nav-item">
                            <a href="/report" class="nav-link {{ request()->is('report') ? 'active' : '' }}"> Absen Siswa</a>
                        </li>
                        <li class="nav-item">
                            <a href="/report-guru" class="nav-link {{ request()->is('report-guru') ? 'active' : '' }}"> Absen Pegawai</a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- MASTER DATA -->
            <li class="nav-item mt-2">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#menuMasterData" role="button" aria-expanded="{{ request()->is('guru', 'register', 'jurusan', 'kelas', 'tahun-ajaran') ? 'true' : 'false' }}">
                    <span class="fw-bold" style="letter-spacing: 1px; font-size: 0.85rem;">🗂️ Master Data</span>
                    <span style="font-size: 10px;">▼</span>
                </a>
                <div class="collapse {{ request()->is('guru', 'register', 'jurusan', 'kelas', 'tahun-ajaran') ? 'show' : '' }}" id="menuMasterData">
                    <ul class="nav flex-column ms-3 border-start border-light ps-2 mb-2">
                        <li class="nav-item">
                            <a href="/guru" class="nav-link {{ request()->is('guru') ? 'active' : '' }}">Data Pegawai</a>
                        </li>
                        <li class="nav-item">
                            <a href="/register" class="nav-link {{ request()->is('register') ? 'active' : '' }}"> Data Siswa</a>
                        </li>
                        <li class="nav-item">
                            <a href="/jurusan" class="nav-link {{ request()->is('jurusan') ? 'active' : '' }}"> Data Jurusan</a>
                        </li>
                        <li class="nav-item">
                            <a href="/kelas" class="nav-link {{ request()->is('kelas') ? 'active' : '' }}"> Data Kelas</a>
                        </li>
                        <li class="nav-item">
                            <a href="/tahun-ajaran" class="nav-link {{ request()->is('tahun-ajaran') ? 'active' : '' }}"> Tahun Ajaran</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- OPERASIONAL -->
            <li class="nav-item mt-2">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#menuOperasional" role="button" aria-expanded="{{ request()->is('manual-attendance-guru', 'manual-attendance', 'settings') ? 'true' : 'false' }}">
                    <span class="fw-bold" style="letter-spacing: 1px; font-size: 0.85rem;">⚙️ Operasional</span>
                    <span style="font-size: 10px;">▼</span>
                </a>
                <div class="collapse {{ request()->is('manual-attendance-guru', 'manual-attendance', 'settings') ? 'show' : '' }}" id="menuOperasional">
                    <ul class="nav flex-column ms-3 border-start border-light ps-2 mb-2">
                        <li class="nav-item">
                            <a href="/manual-attendance-guru" class="nav-link {{ request()->is('manual-attendance-guru') ? 'active' : '' }}"> Input Pegawai</a>
                        </li>
                        <li class="nav-item">
                            <a href="/manual-attendance" class="nav-link {{ request()->is('manual-attendance') ? 'active' : '' }}"> Input Siswa</a>
                        </li>
                        <li class="nav-item">
                            <a href="/settings" class="nav-link {{ request()->is('settings') ? 'active' : '' }}"> Pengaturan Jam</a>
                        </li>
                      
                    </ul>
                    
                </div>
                  <li class="nav-item">
                            <a href="/scan" target="_blank" class="nav-link">📷 Layar Absensi</a>
                        </li>
            </li>
            
            <!-- TOMBOL LOGOUT -->
            <li class="nav-item mt-4 px-3 mb-4">
                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar?');">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100 fw-bold shadow-sm" style="border-radius: 12px; background: rgba(245, 101, 101, 0.85); border: none;">
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="content-area">
        <!-- Tombol Menu Mobile -->
        <div class="d-lg-none mb-3">
            <button class="btn btn-primary shadow-sm" id="sidebarToggle" style="border-radius: 10px;">
                ☰ Menu Navigasi
            </button>
        </div>

        <!-- AREA KONTEN DINAMIS -->
        <div class="flex-grow-1">
            @yield('content')
        </div>

        <!-- FOOTER COPYRIGHT -->
        <footer class="mt-auto pt-5 pb-2 text-center text-secondary fw-bold" style="font-size: 0.85rem;">
            &copy; {{ date('Y') }} SMK Fatahillah Cileungsi. All Rights Reserved.
        </footer>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>