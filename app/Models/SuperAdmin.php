<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    use HasFactory;

    protected $table = 'super_admins';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password'
    ];
}
