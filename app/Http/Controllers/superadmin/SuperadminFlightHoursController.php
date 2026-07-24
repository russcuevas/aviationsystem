<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperadminFlightHoursController extends Controller
{
    public function SuperadminFlightHoursPage()
    {
        return view('superadmin.flight_hours.index');
    }
}
