<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use App\Models\FlightHour;
use Illuminate\Http\Request;

class SuperadminFlightHoursController extends Controller
{
    public function SuperadminFlightHoursPage()
    {
        $flightHours = FlightHour::with(['student', 'aircraft'])
            ->whereIn('status', ['approved', 'cancelled'])
            ->orderBy('id', 'desc')
            ->get();

        return view('superadmin.flight_hours.index', compact('flightHours'));
    }
}
