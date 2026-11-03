<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Student extends Model
{
    protected $fillable = ['nis', 'name', 'kelas', 'jurusan', 'tahun_ajaran', 'rfid_uid'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }
}
