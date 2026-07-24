<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperadminAircraftController extends Controller
{
    public function SuperadminAircraftPage()
    {
        return view('superadmin.aircraft.index');
    }
}
