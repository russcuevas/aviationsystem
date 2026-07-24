<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperadminStudentController extends Controller
{
    public function SuperadminStudentPage()
    {
        return view('superadmin.students.index');
    }
}
