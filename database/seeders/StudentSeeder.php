<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'nis'      => '1001',
                'name'     => 'Ahmad Fikri',
                'rfid_uid' => '0001234567', // Sesuaikan dengan UID kartu asli Anda
            ],
            [
                'nis'      => '1002',
                'name'     => 'Siti Aminah',
                'rfid_uid' => '0009876543',
            ],
            [
                'nis'      => '1003',
                'name'     => 'Budi Santoso',
                'rfid_uid' => '0001122334',
            ]
        ];

        foreach ($students as $student) {
            Student::create($student);
        }
    }
}
