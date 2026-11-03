<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = ['student_id', 'photo_path','waktu_pulang', 'photo_pulang_path', 'status'];

    public function student()
    {
        return $this->belongsTo(Student::class);
        
    }
}
