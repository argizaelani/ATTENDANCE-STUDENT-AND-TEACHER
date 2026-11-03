<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Mencegah sistem eror jika staf tidak sengaja membiarkan baris kosong di Excel
        if (!isset($row['nis']) || $row['nis'] == null) {
            return null;
        }

        return new Student([
            'nis'          => $row['nis'],
            'name'         => $row['nama_lengkap'], 
            'kelas'        => $row['kelas'],
            'jurusan'      => $row['jurusan'],
            'tahun_ajaran' => $row['tahun_ajaran'],
            'rfid_uid'     => $row['uid_rfid'],
        ]);
    }

    // Beri tahu sistem bahwa header/judul kolom berada di baris ke-2
    public function headingRow(): int
    {
        return 2;
    }
}