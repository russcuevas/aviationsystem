<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aircraft extends Model
{
    use HasFactory;

    protected $table = 'aircrafts';

    protected $fillable = [
        'registration',
        'type',
        'model',
        'total_hours',
        'hours_to_overhaul',
        'flying_id',
        'remarks',
        'status',
    ];

    public function trainingProvider()
    {
        return $this->belongsTo(TrainingProvider::class, 'flying_id');
    }

    public function flightHours()
    {
        return $this->hasMany(FlightHour::class, 'aircraft_id');
    }
}
