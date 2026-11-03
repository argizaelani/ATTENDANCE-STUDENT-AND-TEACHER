<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use App\Exports\TemplateExport;

class StudentController extends Controller
{

    // 1. Fungsi menampilkan halaman register beserta data siswa
    public function index(Request $request)
    {
        $search  = $request->search;
        $kelas   = $request->kelas;
        $jurusan = $request->jurusan;

        $query = Student::orderBy('kelas')->orderBy('jurusan')->orderBy('name');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('rfid_uid', 'like', "%{$search}%");
            });
        }
        if ($kelas) {
            $query->where('kelas', $kelas);
        }
        if ($jurusan) {
            $query->where('jurusan', $jurusan);
        }

        $students = $query->paginate(20)->withQueryString();

        // PANGGIL SEMUA DATA MASTER
        $masterKelas = \App\Models\Kelas::orderBy('nama_kelas')->get();
        $masterJurusan = \App\Models\Jurusan::orderBy('kode_jurusan')->get();
        $masterTahun = \App\Models\TahunAjaran::orderBy('nama_tahun', 'desc')->get();

        // KIRIMKAN JUGA KE VIEW
        return view('siswa.register', compact('students', 'masterKelas', 'masterJurusan', 'masterTahun'));
    }
    
    // 3. Fungsi menghapus siswa
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        
        return back()->with('success', 'Data siswa berhasil dihapus secara permanen!');
    }

    // Fungsi Input Manual (jika masih digunakan)
    public function store(Request $request)
    {
        $request->validate([
            'nis'      => 'required|unique:students,nis',
            'name'     => 'required',
            'kelas'    => 'required', 
            'jurusan'  => 'required',
            'tahun_ajaran' => 'required',
            'rfid_uid' => 'required|unique:students,rfid_uid'
        ]);

        Student::create([
            'nis'      => $request->nis,
            'name'     => $request->name,
            'kelas'    => $request->kelas, 
            'jurusan'  => $request->jurusan,
            'tahun_ajaran' => $request->tahun_ajaran,
            'rfid_uid' => $request->rfid_uid
        ]);

        return back()->with('success', 'Data siswa berhasil didaftarkan!');
    }
    
    // Fungsi Import Data Excel Massal
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file', // Nama input disesuaikan dengan form di web
        ]);

        Excel::import(new StudentsImport, $request->file('file'));

        return back()->with('success', 'Data siswa berhasil diimpor dari file Excel!');
    }

    // Fungsi Menautkan Kartu RFID ke Siswa
    public function syncRfid(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'rfid_uid'   => 'required|unique:students,rfid_uid'
        ]);

        $student = Student::find($request->student_id);
        $student->update(['rfid_uid' => $request->rfid_uid]);

        return back()->with('success', 'Kartu RFID berhasil dihubungkan ke siswa: ' . $student->name);
    }

    // Fungsi Download Template Excel (.xlsx)
    public function downloadTemplate()
    {
        return Excel::download(new TemplateExport, 'Template_Data_Siswa.xlsx');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nis'          => 'required|unique:students,nis,' . $id,
            'name'         => 'required',
            'kelas'        => 'required',
            'jurusan'      => 'required',
            'tahun_ajaran' => 'required', // Tambahan baru
            'rfid_uid'     => 'required|unique:students,rfid_uid,' . $id
        ]);

        $student = Student::findOrFail($id);
        $student->update($request->all());

        return back()->with('success', 'Data siswa ' . $student->name . ' berhasil diperbarui!');
    }
}