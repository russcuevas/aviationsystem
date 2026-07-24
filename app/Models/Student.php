<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'phone',
        'date_of_birth',
        'enrollment_date',
        'flying_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
    ];
}
