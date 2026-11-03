<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            // Baris 1: Instruksi Penting
            ['PENTING: Isi kolom Kelas, Jurusan, dan Tahun Ajaran HARUS SAMA PERSIS dengan Master Data di Web (Contoh: 10, RPL, 2025/2026)'],
            
            // Baris 2: Nama Kolom Asli
            ['NIS', 'Nama Lengkap', 'Kelas', 'Jurusan', 'Tahun Ajaran', 'UID RFID']
        ];
    }

    public function array(): array
    {
        return [
            ['26001', 'Argi Zaelani', '11', 'RPL', '2025/2026', '04A1B2C3D4'],
            ['26002', 'Wahyuningtyas', '10', 'TKJ', '2025/2026', '05E6F7G8H9']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Gabungkan sel A1 sampai F1 untuk teks peringatan
        $sheet->mergeCells('A1:F1');

        return [
            // Style Baris 1 (Peringatan warna merah)
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFF0000']], 
            ],
            // Style Baris 2 (Header tabel)
            2 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}