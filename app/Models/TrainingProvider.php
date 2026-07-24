<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'email',
        'phone',
        'accreditation_course',
        'atoc_attachment'
    ];

    protected $casts = [
        'atoc_attachment' => 'array'
    ];
}
