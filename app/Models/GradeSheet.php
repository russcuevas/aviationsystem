<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeSheet extends Model
{
    use HasFactory;

    protected $table = 'grade_sheets';

    protected $fillable = [
        'sheet_id',
        'date',
        'time_out',
        'student_id',
        'instructor_id',
        'stage_id',
        'lesson_grades',
        'total_score',
        'overall_grade',
        'status',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'lesson_grades' => 'array',
        'total_score' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->sheet_id)) {
                $year = date('Y');
                $latest = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();

                if ($latest && preg_match('/GS-' . $year . '-(\d+)/', $latest->sheet_id, $matches)) {
                    $nextNumber = intval($matches[1]) + 1;
                } else {
                    $nextNumber = 1;
                }

                $model->sheet_id = sprintf('GS-%s-%02d', $year, $nextNumber);
            }
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function stage()
    {
        return $this->belongsTo(StudentStaging::class, 'stage_id');
    }
}
