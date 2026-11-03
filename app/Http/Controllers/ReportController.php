<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Student;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Pastikan semua variabel ini dideklarasikan di awal
        $kelas     = $request->kelas; 
        $jurusan   = $request->jurusan; 
        $search    = $request->search;      // Variabel yang hilang
        $startDate = $request->start_date;  // Variabel untuk filter tanggal
        $endDate   = $request->end_date;    // Variabel untuk filter tanggal
        
        $waktuSekarang = Carbon::now('Asia/Jakarta');

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate   = Carbon::parse($endDate)->endOfDay();
            $judul     = "Laporan Absensi (" . $startDate->format('d M Y') . " s/d " . $endDate->format('d M Y') . ")";
        } else {
            $periode = $request->periode ?? 'bulan';
            if ($periode == 'minggu') {
                $startDate = $waktuSekarang->copy()->startOfWeek();
                $endDate   = $waktuSekarang->copy()->endOfWeek();
                $judul     = "Laporan Absensi Minggu Ini (" . $startDate->format('d M') . " - " . $endDate->format('d M Y') . ")";
            } else {
                $startDate = $waktuSekarang->copy()->startOfMonth();
                $endDate   = $waktuSekarang->copy()->endOfMonth();
                $judul     = "Laporan Absensi Bulan " . $waktuSekarang->translatedFormat('F Y');
            }
        }

        if ($kelas) {
            $judul .= " - KELAS " . $kelas;
        }
        if ($jurusan) {
            $judul .= " " . $jurusan; 
        }

        $query = Student::orderBy('kelas')->orderBy('name')->withCount([
            'attendances as tepat_waktu' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->where('status', 'Tepat Waktu');
            },
            'attendances as terlambat' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->where('status', 'Terlambat');
            },
            'attendances as sakit' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->where('status', 'Sakit');
            },
            'attendances as izin' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->where('status', 'Izin');
            },
            'attendances as alfa' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->where('status', 'Alfa');
            },
            'attendances as total_hadir' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->whereIn('status', ['Tepat Waktu', 'Terlambat']);
            }
        ]);

        if ($kelas) {
            $query->where('kelas', $kelas);
        }
        if ($jurusan) {
            $query->where('jurusan', $jurusan);
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // ... (kode query pencarian sebelumnya) ...
        $students = $query->paginate(20)->withQueryString();

        // 1. Panggil semua data Master
        $masterKelas   = \App\Models\Kelas::orderBy('nama_kelas')->get();
        $masterJurusan = \App\Models\Jurusan::orderBy('kode_jurusan')->get();
        $masterTahun   = \App\Models\TahunAjaran::orderBy('nama_tahun', 'desc')->get();

        // 2. Kirim ke view register
        return view('siswa.report', compact('students', 'masterKelas', 'masterJurusan', 'masterTahun'));
    
    }

   public function exportExcel(Request $request)
    {
        $kelas     = $request->kelas;
        $jurusan   = $request->jurusan;
        $search    = $request->search;
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        $waktuSekarang = Carbon::now('Asia/Jakarta');

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate   = Carbon::parse($endDate)->endOfDay();
            $fileNamePeriode = $startDate->format('Ymd') . "_s_d_" . $endDate->format('Ymd');
        } else {
            $periode = $request->periode ?? 'bulan';
            $fileNamePeriode = $periode;
            if ($periode == 'minggu') {
                $startDate = $waktuSekarang->copy()->startOfWeek();
                $endDate   = $waktuSekarang->copy()->endOfWeek();
            } else {
                $startDate = $waktuSekarang->copy()->startOfMonth();
                $endDate   = $waktuSekarang->copy()->endOfMonth();
            }
        }

        $query = Student::orderBy('kelas')->orderBy('jurusan')->orderBy('name')->withCount([
            'attendances as tepat_waktu' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->where('status', 'Tepat Waktu');
            },
            'attendances as terlambat' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->where('status', 'Terlambat');
            },
            'attendances as sakit' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->where('status', 'Sakit');
            },
            'attendances as izin' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->where('status', 'Izin');
            },
            'attendances as alfa' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->where('status', 'Alfa');
            },
            'attendances as total_hadir' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->whereIn('status', ['Tepat Waktu', 'Terlambat']);
            },
        ]);

        if ($kelas) {
            $query->where('kelas', $kelas);
        }
        if ($jurusan) {
            $query->where('jurusan', $jurusan);
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $students = $query->get();

        $fileName = "rekap_absensi_{$fileNamePeriode}";
        if ($kelas) { $fileName .= "_kelas_{$kelas}"; }
        if ($jurusan) { $fileName .= "_jurusan_{$jurusan}"; }
        $fileName .= ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // WAJIB MENYERTAKAN VARIABEL $startDate, $endDate, $kelas, $jurusan KE DALAM CALLBACK
        $callback = function() use($students, $startDate, $endDate, $kelas, $jurusan) {
            $file = fopen('php://output', 'w');
            
            // --- TAMBAHAN BARU: INFORMASI JUDUL & TANGGAL DI DALAM EXCEL ---
            fputcsv($file, ['LAPORAN REKAPITULASI ABSENSI SISWA'], ';');
            fputcsv($file, ['Periode Tanggal', ':', $startDate->translatedFormat('d F Y') . ' s/d ' . $endDate->translatedFormat('d F Y')], ';');
            
            if ($kelas) {
                fputcsv($file, ['Filter Kelas', ':', $kelas], ';');
            }
            if ($jurusan) {
                fputcsv($file, ['Filter Jurusan', ':', $jurusan], ';');
            }
            
            fputcsv($file, [], ';'); // Baris kosong sebagai jarak pemisah

            // --- HEADER TABEL UTAMA ---
            fputcsv($file, ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Jurusan', 'Tepat Waktu', 'Terlambat', 'Sakit', 'Izin', 'Alfa', 'Total Hadir'], ';');

            foreach ($students as $index => $siswa) {
                fputcsv($file, [
                    $index + 1, 
                    $siswa->nis, 
                    $siswa->name, 
                    $siswa->kelas, 
                    $siswa->jurusan, 
                    $siswa->tepat_waktu, 
                    $siswa->terlambat, 
                    $siswa->sakit,      
                    $siswa->izin,       
                    $siswa->alfa,       
                    $siswa->total_hadir
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function guru(Request $request)
    {
        $judul = 'Laporan Absensi Guru';
        $search = $request->search;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $periode = $request->periode ?? 'bulan'; // Default bulan ini

        $query = \App\Models\Guru::query();

        // 1. Filter Pencarian Nama / NIP
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // 2. Filter Tanggal & Periode
        if (!$startDate && !$endDate) {
            if ($periode == 'minggu') {
                $startDate = \Carbon\Carbon::now('Asia/Jakarta')->startOfWeek()->format('Y-m-d');
                $endDate = \Carbon\Carbon::now('Asia/Jakarta')->endOfWeek()->format('Y-m-d');
            } else {
                $startDate = \Carbon\Carbon::now('Asia/Jakarta')->startOfMonth()->format('Y-m-d');
                $endDate = \Carbon\Carbon::now('Asia/Jakarta')->endOfMonth()->format('Y-m-d');
            }
        }

        // 3. Ambil data guru dan hitung otomatis setiap status absensinya
        $gurus = $query->withCount([
            'absensi as tepat_waktu' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'Tepat Waktu')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            },
            'absensi as terlambat' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'Terlambat')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            },
            'absensi as sakit' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'Sakit')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            },
            'absensi as izin' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'Izin')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            },
            'absensi as alfa' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'Alfa')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }
        ])->orderBy('nama')->get();

        return view('guru.report-guru', compact('gurus', 'judul', 'search', 'startDate', 'endDate'));
    }

    public function exportPdfGuru(Request $request)
    {
        $search = $request->search;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $periode = $request->periode ?? 'bulan'; 

        $query = \App\Models\Guru::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if (!$startDate && !$endDate) {
            if ($periode == 'minggu') {
                $startDate = \Carbon\Carbon::now('Asia/Jakarta')->startOfWeek()->format('Y-m-d');
                $endDate = \Carbon\Carbon::now('Asia/Jakarta')->endOfWeek()->format('Y-m-d');
            } else {
                $startDate = \Carbon\Carbon::now('Asia/Jakarta')->startOfMonth()->format('Y-m-d');
                $endDate = \Carbon\Carbon::now('Asia/Jakarta')->endOfMonth()->format('Y-m-d');
            }
        }

        $gurus = $query->withCount([
            'absensi as tepat_waktu' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'Tepat Waktu')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            },
            'absensi as terlambat' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'Terlambat')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            },
            'absensi as sakit' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'Sakit')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            },
            'absensi as izin' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'Izin')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            },
            'absensi as alfa' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'Alfa')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }
        ])->orderBy('nama')->get();

        // Proses mengubah tampilan menjadi file PDF berukuran A4 Landscape
        $pdf = Pdf::loadView('guru.report-guru-pdf', compact('gurus', 'startDate', 'endDate'))
                  ->setPaper('a4', 'landscape');
                  
        return $pdf->download('Laporan_Absensi_Guru_' . date('Ymd') . '.pdf');
    }
}