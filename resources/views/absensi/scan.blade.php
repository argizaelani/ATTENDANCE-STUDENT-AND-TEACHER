<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Layar Scan Absensi - SMK Fatahillah</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome (Ditambahkan untuk icon) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts (Poppins) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fffaf0; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        /* ANIMASI GELOMBANG AIR */
        .wave-container { position: absolute; bottom: 0; left: 0; width: 100%; height: 25vh; z-index: -1; overflow: hidden; }
        .waves { position: relative; width: 100%; height: 100%; min-height: 100px; max-height: 250px; }
        .parallax > use { animation: move-forever 25s cubic-bezier(.55,.5,.45,.5) infinite; }
        .parallax > use:nth-child(1) { animation-delay: -2s; animation-duration: 7s; }
        .parallax > use:nth-child(2) { animation-delay: -3s; animation-duration: 10s; }
        .parallax > use:nth-child(3) { animation-delay: -4s; animation-duration: 13s; }
        .parallax > use:nth-child(4) { animation-delay: -5s; animation-duration: 20s; }

        @keyframes move-forever { 0% { transform: translate3d(-90px,0,0); } 100% { transform: translate3d(85px,0,0); } }

        /* HEADER */
        .top-header { text-align: center; margin-bottom: 25px; z-index: 10; }
        .main-title { font-size: 3.2rem; font-weight: 800; color: #ea580c; margin: 0; letter-spacing: 2px; text-shadow: 0 4px 15px rgba(234, 88, 12, 0.2); }
        .sub-title { font-size: 1.15rem; font-weight: 600; color: #64748b; margin-top: 5px; letter-spacing: 0.5px; }

        /* PEMBUNGKUS PANEL KACA */
        .kiosk-wrapper { display: flex; gap: 30px; width: 100%; max-width: 1400px; padding: 0 20px; z-index: 10; height: 72vh; }
        .glass-panel { background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(249, 115, 22, 0.3); border-radius: 30px; box-shadow: 0 20px 40px rgba(234, 88, 12, 0.08); display: flex; flex-direction: column; }

        /* PANEL KIRI (Kamera & Jam) */
        .scan-panel { flex: 1.5; padding: 30px 40px; text-align: center; align-items: center; justify-content: center; position: relative; min-width: 0; }
        .clock { font-size: 5.5rem; font-weight: 800; color: #ea580c; text-shadow: 0 4px 15px rgba(234, 88, 12, 0.25); line-height: 1; margin-bottom: 5px; letter-spacing: -2px; }
        .date { font-size: 1.3rem; font-weight: 600; color: #64748b; margin-bottom: 25px; }
        .camera-wrapper { position: relative; width: 100%; max-width: 450px; aspect-ratio: 4/3; margin: 0 auto 15px auto; border-radius: 24px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.1); border: 6px solid #f97316; background-color: #000; }
        #webcam { width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); }
        #canvas { display: none; }
        .rfid-input { opacity: 0; position: absolute; z-index: -999; }
        
        #alert-box { border-radius: 20px; font-weight: 700; font-size: 1.4rem; padding: 18px 20px; display: none; width: 100%; max-width: 450px; margin: 15px auto 0 auto; animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        @keyframes popIn { 0% { opacity: 0; transform: scale(0.9); } 100% { opacity: 1; transform: scale(1); } }

        /* PANEL KANAN (Riwayat Absen) */
        .history-panel { flex: 1; min-width: 400px; padding: 30px; }
        .history-title { font-weight: 800; color: #ea580c; font-size: 1.5rem; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 3px solid rgba(249, 115, 22, 0.2); display: flex; justify-content: space-between; align-items: center; }
        .history-list { flex-grow: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; padding-right: 10px; }
        .history-list::-webkit-scrollbar { width: 8px; }
        .history-list::-webkit-scrollbar-thumb { background: rgba(249, 115, 22, 0.3); border-radius: 10px; }

        .history-item { background: #ffffff; border-radius: 18px; padding: 14px 18px; border-left: 8px solid #f97316; display: flex; justify-content: space-between; align-items: center; animation: slideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 10px rgba(0,0,0,0.04); border-top: 1px solid #fff5f5; border-right: 1px solid #fff5f5; border-bottom: 1px solid #fff5f5; }
        .history-item.tepat { border-left-color: #10b981; } 
        .history-item.terlambat { border-left-color: #ef4444; } 
        .history-item.pulang { border-left-color: #0d6efd; } 

        .history-name { font-weight: 800; color: #1e293b; font-size: 1.1rem; margin-bottom: 2px; }
        .history-time { color: #64748b; font-weight: 600; font-size: 0.85rem; }
        .history-badge { font-size: 0.85rem; padding: 6px 12px; border-radius: 10px; }
        .copyright { position: fixed; bottom: 10px; width: 100%; text-align: center; color: #f0efee; font-size: 0.85rem; font-weight: 600; z-index: 5; }

        /* STYLING TOMBOL BYPASS */
        .btn-bypass {
            background-color: #ffedd5;
            color: #ea580c;
            border: 2px solid #fdba74;
            border-radius: 15px;
            padding: 8px 25px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .btn-bypass:hover {
            background-color: #ea580c;
            color: white;
            border-color: #ea580c;
        }
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

    <div class="top-header">
        <h1 class="main-title">SI-SAKTI</h1>
        <div class="sub-title">(Sistem Absensi & Kawal Kehadiran Tinggi)</div>
    </div>

    <div class="kiosk-wrapper">
        <!-- PANEL KIRI (Scan) -->
        <div class="glass-panel scan-panel">
            <div class="clock" id="clock">00:00:00</div>
            <div class="date" id="date">Hari, Tanggal Bulan Tahun</div>

            <div class="camera-wrapper">
                <video id="webcam" autoplay playsinline></video>
            </div>
            <canvas id="canvas"></canvas>

            <h5 class="fw-bold mt-2 mb-3" style="color: #ea580c;">Silakan Hadap Kamera & Tap Kartu Anda</h5>

            <!-- TOMBOL BYPASS DITAMBAHKAN DI SINI -->
            <a href="{{ route('bypass.index') }}" class="btn btn-bypass fw-bold shadow-sm text-decoration-none">
                <i class="fas fa-keyboard me-2"></i> Lupa Bawa Kartu? Klik Di Sini
            </a>

            <div id="alert-box" class="alert shadow-sm border-0 mt-3"></div>
            <input type="text" id="rfid_uid" class="rfid-input" autofocus autocomplete="off">
        </div>

        <!-- PANEL KANAN (Riwayat) -->
        <div class="glass-panel history-panel">
            <div class="history-title">
                <span>🕒 Riwayat Hari Ini</span>
                <span class="badge rounded-pill fs-6 px-3" style="background-color: #ea0c0c; color: white;">Live</span>
            </div>
            
            <div class="history-list" id="history-list">
                <div class="text-center fw-bold mt-5" id="empty-history" style="color: #fdba74;">
                    <span style="font-size: 3rem;">💤</span><br><br>Menunggu antrean absen...
                </div>
            </div>
        </div>
    </div>

    <div class="copyright">
        &copy; {{ date('Y') }} SMK Fatahillah Cileungsi. All Rights Reserved.
    </div>

    <script>
        const alertBox = document.getElementById('alert-box');

        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;

            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            document.getElementById('date').textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        const video = document.getElementById('webcam');
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
                .then(stream => video.srcObject = stream)
                .catch(error => console.error("Kamera ditolak!", error));
        } else {
            alertBox.style.display = 'block';
            alertBox.className = 'alert alert-danger shadow-sm border-0 text-center';
            alertBox.innerHTML = '🔒 Kamera diblokir browser.';
        }

        const rfidInput = document.getElementById('rfid_uid');
        const historyList = document.getElementById('history-list');

        document.addEventListener('click', () => rfidInput.focus());
        rfidInput.addEventListener('blur', () => setTimeout(() => rfidInput.focus(), 10));

        rfidInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                let uid = this.value;
                this.value = ''; 

                if(uid.trim() === '') return;

                let photoBase64 = "";
                if(video.srcObject && video.videoWidth > 0) {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    photoBase64 = canvas.toDataURL('image/png'); 
                }

                fetch('/scan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ rfid_uid: uid, photo: photoBase64 })
                })
                .then(response => response.json())
                .then(data => {
                    alertBox.style.display = 'block';
                    alertBox.innerHTML = data.message;
                    
                    if(data.success) {
                        alertBox.className = 'alert alert-success shadow-lg border-0 text-center mt-3';
                        
                        const emptyText = document.getElementById('empty-history');
                        if(emptyText) emptyText.remove();

                        let itemClass = 'terlambat';
                        let badgeClass = 'bg-danger';

                        if (data.data.status === 'Tepat Waktu') {
                            itemClass = 'tepat';
                            badgeClass = 'bg-success';
                        } else if (data.data.status === 'Pulang') {
                            itemClass = 'pulang';
                            badgeClass = 'bg-primary';
                        }

                        const newItem = document.createElement('div');
                        newItem.className = `history-item ${itemClass}`;
                        
                        newItem.innerHTML = `
                            <div class="d-flex align-items-center w-100">
                                <img src="${data.data.foto}" alt="Foto" class="rounded-circle me-3 shadow-sm" style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #ea580c;">
                                <div class="flex-grow-1 text-start">
                                    <div class="history-name">${data.data.nama}</div>
                                    <div class="history-time">${data.data.role} • <b class="text-dark">${data.data.waktu} WIB</b></div>
                                </div>
                                <div>
                                    <span class="badge ${badgeClass} history-badge shadow-sm">${data.data.status}</span>
                                </div>
                            </div>
                        `;
                        
                        historyList.prepend(newItem);

                    } else {
                        alertBox.className = 'alert alert-danger shadow-lg border-0 text-center mt-3';
                    }

                    setTimeout(() => alertBox.style.display = 'none', 3500);
                })
                .catch(error => {
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert alert-danger shadow-lg border-0 text-center fw-bold text-white mt-3';
                    alertBox.style.backgroundColor = '#ef4444'; 
                    alertBox.innerHTML = '❌ Server Error: Cek koneksi atau database!';
                    console.error('System Crash:', error);
                    setTimeout(() => alertBox.style.display = 'none', 4000);
                });
            }
        });
    </script>
</body>
</html>