<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\AbsensiGuru;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tanggalHariIni = Carbon::now('Asia/Jakarta')->toDateString();

        // Ambil data absen siswa hari ini
        $absensi = Attendance::with('student')
                        ->whereDate('created_at', $tanggalHariIni)
                        ->orderBy('created_at', 'desc')
                        ->get();

        // Hitung statistik untuk grafik
        $statistikSiswa = [
            $absensi->where('status', 'Tepat Waktu')->count(),
            $absensi->where('status', 'Terlambat')->count(),
            $absensi->where('status', 'Sakit')->count(),
            $absensi->where('status', 'Izin')->count(),
            $absensi->where('status', 'Alfa')->count(),
        ];

        return view('dashboard.dashboard', compact('absensi', 'statistikSiswa'));
    }

    public function guru(Request $request)
    {
        $tanggalHariIni = Carbon::now('Asia/Jakarta')->toDateString();

        // Ambil data absen guru hari ini
        $absensiGuru = AbsensiGuru::with('guru')
                        ->whereDate('created_at', $tanggalHariIni)
                        ->orderBy('created_at', 'desc')
                        ->get();

        // Hitung statistik untuk grafik
        $statistikGuru = [
            $absensiGuru->where('status', 'Tepat Waktu')->count(),
            $absensiGuru->where('status', 'Terlambat')->count(),
            $absensiGuru->where('status', 'Sakit')->count(),
            $absensiGuru->where('status', 'Izin')->count(),
            $absensiGuru->where('status', 'Alfa')->count(),
        ];

        return view('dashboard.dashboard-guru', compact('absensiGuru', 'statistikGuru'));
    }
}