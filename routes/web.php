<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SettingController;


// --- RUTE UMUM (BISA DIAKSES OLEH MESIN SCAN TANPA LOGIN) ---
Route::get('/scan', function () { return view('absensi.scan'); });
Route::post('/scan', [AttendanceController::class, 'store']);

// --- RUTE LOGIN ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- RUTE ADMIN (DIKUNCI, WAJIB LOGIN) ---
Route::middleware('auth')->group(function () {


    
    // Pendaftaran & Manajemen Siswa
    Route::get('/register', [StudentController::class, 'index']);
    Route::post('/register', [StudentController::class, 'store']);
    Route::put('/students/{id}', [StudentController::class, 'update']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);
    Route::post('/students/import', [StudentController::class, 'importCsv']);
    Route::post('/students/sync', [StudentController::class, 'syncRfid']);
    Route::get('/students/template', [StudentController::class, 'downloadTemplate']);
    
    // Dashboard & Hapus Absensi
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy']);
    
    // Laporan
    Route::get('/report', [ReportController::class, 'index']);
    Route::get('/report/excel', [ReportController::class, 'exportExcel']);

    // Input Manual Keterangan Absensi
    Route::get('/manual-attendance', [AttendanceController::class, 'manualInput']);
    Route::post('/manual-attendance', [AttendanceController::class, 'storeManual']);

    // Master Data Jurusan
    Route::get('/jurusan', [JurusanController::class, 'index']);
    Route::post('/jurusan', [JurusanController::class, 'store']);
    Route::put('/jurusan/{id}', [JurusanController::class, 'update']);
    Route::delete('/jurusan/{id}', [JurusanController::class, 'destroy']);

    // Master Data Kelas
    Route::get('/kelas', [KelasController::class, 'index']);
    Route::post('/kelas', [KelasController::class, 'store']);
    Route::put('/kelas/{id}', [KelasController::class, 'update']);
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy']);

    // Master Data Tahun Ajaran
    Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index']);
    Route::post('/tahun-ajaran', [TahunAjaranController::class, 'store']);
    Route::put('/tahun-ajaran/{id}', [TahunAjaranController::class, 'update']);
    Route::delete('/tahun-ajaran/{id}', [TahunAjaranController::class, 'destroy']);

    // Rute Data Guru
Route::get('/guru', [GuruController::class, 'index']);
Route::post('/guru', [GuruController::class, 'store']);
Route::put('/guru/{id}', [GuruController::class, 'update']);
Route::delete('/guru/{id}', [GuruController::class, 'destroy']);

// Dashboard Absensi Guru
    Route::get('/dashboard-guru', [\App\Http\Controllers\DashboardController::class, 'guru']);

    // Input Manual Keterangan Absensi Guru
    Route::get('/manual-attendance-guru', [\App\Http\Controllers\AttendanceController::class, 'manualInputGuru']);
    Route::post('/manual-attendance-guru', [\App\Http\Controllers\AttendanceController::class, 'storeManualGuru']);

    // Laporan Guru
    Route::get('/report-guru', [\App\Http\Controllers\ReportController::class, 'guru']);
    Route::get('/report-guru/pdf', [\App\Http\Controllers\ReportController::class, 'exportPdfGuru']);

    // Pengaturan Jam Absensi
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index']);
    Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'update']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/settings', [SettingController::class, 'index']);

   
    
});

Route::get('/bypass-absensi', [App\Http\Controllers\AttendanceController::class, 'formBypass'])->name('bypass.index');
Route::post('/bypass-absensi', [App\Http\Controllers\AttendanceController::class, 'storeBypass'])->name('bypass.store');