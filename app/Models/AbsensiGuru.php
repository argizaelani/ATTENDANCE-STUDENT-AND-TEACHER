<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsensiGuru extends Model
{
    use HasFactory;
    protected $fillable = ['guru_id', 'status', 'photo_path', 'waktu_pulang', 'photo_pulang_path'];

    // Relasi ke tabel guru
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
