<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightHour extends Model
{
    use HasFactory;

    protected $table = 'flight_hours';

    protected $fillable = [
        'log_id',
        'student_id',
        'aircraft_id',
        'dual_instruction_time',
        'pic_time',
        'solo_time',
        'instrument_flight_time',
        'total_time',
        'status',
        'remarks',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->log_id)) {
                $year = date('Y');
                $latest = static::whereYear('created_at', $year)->latest('id')->first();
                $nextSequence = 1;
                if ($latest && preg_match('/FH-\d{4}-(\d+)/', $latest->log_id, $matches)) {
                    $nextSequence = intval($matches[1]) + 1;
                }
                $model->log_id = sprintf('FH-%s-%02d', $year, $nextSequence);
            }

            if (empty($model->status)) {
                $model->status = 'pending review';
            }

            $dual = (float) ($model->dual_instruction_time ?? 0);
            $pic = (float) ($model->pic_time ?? 0);
            $solo = (float) ($model->solo_time ?? 0);
            $inst = (float) ($model->instrument_flight_time ?? 0);
            $model->total_time = $dual + $pic + $solo + $inst;
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class, 'aircraft_id');
    }
}
