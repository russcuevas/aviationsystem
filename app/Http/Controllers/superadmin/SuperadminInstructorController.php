<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperadminInstructorController extends Controller
{
    public function SuperadminInstructorPage()
    {
        return view('superadmin.instructors.index');
    }
}
