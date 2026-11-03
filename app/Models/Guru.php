<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;
    protected $fillable = ['nip', 'nama', 'mata_pelajaran', 'rfid_uid'];

    // Relasi untuk mengambil riwayat absensi guru
    public function absensi()
    {
        return $this->hasMany(AbsensiGuru::class, 'guru_id');
    }
}