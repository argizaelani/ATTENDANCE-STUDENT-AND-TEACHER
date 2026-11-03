<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Guru;
use App\Models\AbsensiGuru;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    private function prosesFoto($base64Data)
    {
        if ($base64Data) {
            $image_parts = explode(";base64,", $base64Data);
            if(count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = 'attendance/' . uniqid() . '.png';
                Storage::disk('public')->put($fileName, $image_base64);
                return $fileName;
            }
        }
        return "";
    }

    public function manualInput(Request $request)
    {
        $masterKelas   = Kelas::orderBy('nama_kelas')->get();
        $masterJurusan = Jurusan::orderBy('kode_jurusan')->get();
        $kelas   = $request->kelas;
        $jurusan = $request->jurusan;
        $studentsBulk = [];

        if ($kelas || $jurusan) {
            $query = Student::orderBy('name');
            if ($kelas) $query->where('kelas', $kelas);
            if ($jurusan) $query->where('jurusan', $jurusan);
            $studentsBulk = $query->get();
        }

        return view('absensi.manual-attendance', compact('masterKelas', 'masterJurusan', 'studentsBulk', 'kelas', 'jurusan'));
    }

    public function storeManual(Request $request)
    {
        $request->validate(['status' => 'required']);

        if ($request->has('student_ids') && is_array($request->student_ids)) {
            $jumlah = 0;
            foreach ($request->student_ids as $id) {
                Attendance::create(['student_id' => $id, 'status' => $request->status, 'photo_path' => 'manual']);
                $jumlah++;
            }
            return redirect('/manual-attendance?tab=massal')->with('success', $jumlah . ' data absensi massal berhasil disimpan!');
        } 
        else if ($request->has('student_id') && $request->student_id != null) {
            Attendance::create(['student_id' => $request->student_id, 'status' => $request->status, 'photo_path' => 'manual']);
            return redirect('/manual-attendance')->with('success', 'Keterangan absensi berhasil disimpan!');
        }

        return back()->with('error', 'Gagal! Anda belum memilih/mencentang data siswa.');
    }

    public function store(Request $request)
    {
        $uid = $request->rfid_uid;
        $waktuSekarang = Carbon::now('Asia/Jakarta');
        $jamSekarang = $waktuSekarang->format('H:i:s');

        $settingBatas = Setting::where('key', 'batas_terlambat')->value('value') ?? '07:15:00';
        $batasMasuk = Carbon::parse($settingBatas, 'Asia/Jakarta')->setDate($waktuSekarang->year, $waktuSekarang->month, $waktuSekarang->day);

        $settingPulang = Setting::where('key', 'jam_pulang')->value('value') ?? '15:00:00';
        $batasPulang = Carbon::parse($settingPulang, 'Asia/Jakarta')->setDate($waktuSekarang->year, $waktuSekarang->month, $waktuSekarang->day);

        // --- SKENARIO 1: SISWA ---
        $student = Student::where('rfid_uid', $uid)->first();
        if ($student) {
            $alreadyScanned = Attendance::where('student_id', $student->id)->whereDate('created_at', $waktuSekarang->toDateString())->first();

            // LOGIKA PULANG
            if ($alreadyScanned) {
                if ($alreadyScanned->waktu_pulang !== null) {
                    return response()->json(['success' => false, 'message' => '⚠️ ' . $student->name . ' sudah absen pulang hari ini!']);
                }

                if ($waktuSekarang->greaterThanOrEqualTo($batasPulang)) {
                    $photoPath = $this->prosesFoto($request->photo);
                    $alreadyScanned->update(['waktu_pulang' => $jamSekarang, 'photo_pulang_path' => $photoPath]);

                    return response()->json([
                        'success' => true,
                        'message' => '✅ Hati-hati di jalan! Berhasil Absen Pulang.',
                        'data'    => [
                            'nama'   => $student->name,
                            'waktu'  => $jamSekarang,
                            'status' => 'Pulang',
                            'role'   => 'Siswa (Kelas '.$student->kelas.')',
                            'foto'   => asset('storage/' . $photoPath)
                        ]
                    ]);
                } else {
                    return response()->json(['success' => false, 'message' => '⚠️ ' . $student->name . ' sudah absen masuk. Belum waktunya pulang!']);
                }
            }

            // LOGIKA MASUK
            $status = $waktuSekarang->greaterThan($batasMasuk) ? 'Terlambat' : 'Tepat Waktu';
            $photoPath = $this->prosesFoto($request->photo);
            Attendance::create(['student_id' => $student->id, 'status' => $status, 'photo_path' => $photoPath]);

            return response()->json([
                'success' => true,
                'message' => '✅ Absensi Masuk Berhasil!',
                'data'    => [
                    'nama'   => $student->name,
                    'waktu'  => $jamSekarang,
                    'status' => $status,
                    'role'   => 'Siswa (Kelas '.$student->kelas.')',
                    'foto'   => asset('storage/' . $photoPath)
                ]
            ]);
        }

        // --- SKENARIO 2: PEGAWAI (GURU, SATPAM, TU, DLL) ---
        $guru = Guru::where('rfid_uid', $uid)->first();
        if ($guru) {
            $alreadyScannedGuru = AbsensiGuru::where('guru_id', $guru->id)->whereDate('created_at', $waktuSekarang->toDateString())->first();

            // MENARIK JABATAN DARI DATABASE
            $jabatan = $guru->mata_pelajaran ?? 'Pegawai';

            // LOGIKA PULANG
            if ($alreadyScannedGuru) {
                if ($alreadyScannedGuru->waktu_pulang !== null) {
                    return response()->json(['success' => false, 'message' => '⚠️ Bapak/Ibu ' . $guru->nama . ' sudah absen pulang hari ini!']);
                }

                if ($waktuSekarang->greaterThanOrEqualTo($batasPulang)) {
                    $photoPath = $this->prosesFoto($request->photo);
                    $alreadyScannedGuru->update(['waktu_pulang' => $jamSekarang, 'photo_pulang_path' => $photoPath]);

                    return response()->json([
                        'success' => true,
                        'message' => '✅ Terima kasih! Berhasil Absen Pulang.',
                        'data'    => [
                            'nama'   => $guru->nama,
                            'waktu'  => $jamSekarang,
                            'status' => 'Pulang',
                            'role'   => $jabatan, // <-- Mengirim data spesifik (Satpam, TU, dll) ke layar
                            'foto'   => asset('storage/' . $photoPath)
                        ]
                    ]);
                } else {
                    return response()->json(['success' => false, 'message' => '⚠️ Bapak/Ibu ' . $guru->nama . ' sudah absen masuk. Belum waktunya pulang!']);
                }
            }

            // LOGIKA MASUK
            $statusGuru = $waktuSekarang->greaterThan($batasMasuk) ? 'Terlambat' : 'Tepat Waktu';
            $photoPath = $this->prosesFoto($request->photo);
            AbsensiGuru::create(['guru_id' => $guru->id, 'status' => $statusGuru, 'photo_path' => $photoPath]);

            return response()->json([
                'success' => true,
                'message' => '✅ Absensi Masuk ' . $jabatan . ' Berhasil!', // <-- Menyebut jabatan di Pop-Up
                'data'    => [
                    'nama'   => $guru->nama,
                    'waktu'  => $jamSekarang,
                    'status' => $statusGuru,
                    'role'   => $jabatan, // <-- Mengirim data spesifik (Satpam, TU, dll) ke layar
                    'foto'   => asset('storage/' . $photoPath)
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => '❌ Kartu RFID tidak terdaftar!'], 404);
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        if ($attendance->photo_path && $attendance->photo_path != 'manual') Storage::disk('public')->delete($attendance->photo_path);
        $attendance->delete();
        return back()->with('success', 'Data absensi berhasil dihapus!');
    }

    public function manualInputGuru()
    {
        $gurus = Guru::orderBy('nama')->get();
        return view('absensi.manual-attendance-guru', compact('gurus'));
    }

    public function storeManualGuru(Request $request)
    {
        $request->validate(['guru_id' => 'required', 'status'  => 'required']);
        AbsensiGuru::create(['guru_id' => $request->guru_id, 'status' => $request->status, 'photo_path' => 'manual']);
        return back()->with('success', 'Keterangan absensi pegawai berhasil disimpan!');
    }

    // ==========================================
    // 6. BYPASS ABSENSI (LUPA BAWA KARTU)
    // ==========================================
    public function formBypass()
    {
        $students = Student::orderBy('name')->get();
        $gurus = Guru::orderBy('nama')->get();
        return view('absensi.bypass', compact('students', 'gurus'));
    }

    public function storeBypass(Request $request)
    {
        $request->validate([
            'tipe' => 'required',
            'id_user' => 'required'
        ]);

        $waktuSekarang = Carbon::now('Asia/Jakarta');
        $jamSekarang = $waktuSekarang->format('H:i:s');

        // Ambil pengaturan batas waktu
        $settingBatas = Setting::where('key', 'batas_terlambat')->value('value') ?? '07:15:00';
        $batasMasuk = Carbon::parse($settingBatas, 'Asia/Jakarta')->setDate($waktuSekarang->year, $waktuSekarang->month, $waktuSekarang->day);

        $settingPulang = Setting::where('key', 'jam_pulang')->value('value') ?? '15:00:00';
        $batasPulang = Carbon::parse($settingPulang, 'Asia/Jakarta')->setDate($waktuSekarang->year, $waktuSekarang->month, $waktuSekarang->day);

        // --- BYPASS SISWA ---
        if ($request->tipe == 'siswa') {
            $absen = Attendance::where('student_id', $request->id_user)
                        ->whereDate('created_at', $waktuSekarang->toDateString())->first();
            
            if ($absen) {
                if ($absen->waktu_pulang !== null) return back()->with('error', 'Siswa tersebut sudah absen pulang hari ini!');
                if ($waktuSekarang->greaterThanOrEqualTo($batasPulang)) {
                    $absen->update(['waktu_pulang' => $jamSekarang, 'photo_pulang_path' => 'Lupa Kartu']);
                    return back()->with('success', 'Berhasil mencatat Absen Pulang (Lupa Kartu).');
                }
                return back()->with('error', 'Siswa sudah absen masuk, tetapi belum waktunya pulang!');
            }
            
            $status = $waktuSekarang->greaterThan($batasMasuk) ? 'Terlambat' : 'Tepat Waktu';
            Attendance::create(['student_id' => $request->id_user, 'status' => $status, 'photo_path' => 'Lupa Kartu']);
            return back()->with('success', 'Berhasil mencatat Absen Masuk (Lupa Kartu).');
        } 
        
        // --- BYPASS PEGAWAI ---
        else if ($request->tipe == 'guru') {
            $absenGuru = AbsensiGuru::where('guru_id', $request->id_user)
                            ->whereDate('created_at', $waktuSekarang->toDateString())->first();
            
            if ($absenGuru) {
                if ($absenGuru->waktu_pulang !== null) return back()->with('error', 'Pegawai tersebut sudah absen pulang hari ini!');
                if ($waktuSekarang->greaterThanOrEqualTo($batasPulang)) {
                    $absenGuru->update(['waktu_pulang' => $jamSekarang, 'photo_pulang_path' => 'Lupa Kartu']);
                    return back()->with('success', 'Berhasil mencatat Absen Pulang Pegawai (Lupa Kartu).');
                }
                return back()->with('error', 'Pegawai sudah absen masuk, tetapi belum waktunya pulang!');
            }
            
            $status = $waktuSekarang->greaterThan($batasMasuk) ? 'Terlambat' : 'Tepat Waktu';
            AbsensiGuru::create(['guru_id' => $request->id_user, 'status' => $status, 'photo_path' => 'Lupa Kartu']);
            return back()->with('success', 'Berhasil mencatat Absen Masuk Pegawai (Lupa Kartu).');
        }
    }
}