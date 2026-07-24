<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminFlightHoursController extends Controller
{
    public function AdminFlightHoursPage()
    {
        return view('admin.flight_hours.index');
    }
}
