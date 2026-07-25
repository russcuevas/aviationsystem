<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentStaging extends Model
{
    use HasFactory;

    protected $table = 'students_staging';

    protected $fillable = [
        'student_id',
        'stage',
        'required_hours',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
