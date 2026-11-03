<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;

class ManualAttendanceController extends Controller
{
    // Menampilkan form input manual
    public function create()
    {
        $students = Student::orderBy('kelas')->orderBy('jurusan')->orderBy('name')->get();
        return view('manual-attendance', compact('students'));
    }

    // Menyimpan data Sakit / Izin / Alfa
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'status'     => 'required|in:Sakit,Izin,Alfa'
        ]);

        $tanggalHariIni = Carbon::today('Asia/Jakarta');

        // Cek apakah siswa ini sudah tercatat absen/status hari ini
        $sudahAda = Attendance::where('student_id', $request->student_id)
                        ->whereDate('created_at', $tanggalHariIni)
                        ->exists();

        if ($sudahAda) {
            return back()->with('error', 'Siswa ini sudah memiliki catatan kehadiran/keterangan hari ini!');
        }

        // Simpan ke database (photo_path diisi string kosong karena input manual tanpa webcam)
        Attendance::create([
            'student_id' => $request->student_id,
            'photo_path' => 'manual', 
            'status'     => $request->status
        ]);

        return back()->with('success', 'Keterangan berhasil disimpan!');
    }
}
