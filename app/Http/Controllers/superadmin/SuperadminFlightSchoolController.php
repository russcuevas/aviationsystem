<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperadminFlightSchoolController extends Controller
{
    public function SuperadminFlightSchoolPage()
    {
        return view('superadmin.flight_school.index');
    }
}
