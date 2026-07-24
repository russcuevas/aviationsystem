<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAircraftLogbooksController extends Controller
{
    public function AdminAirCraftLogbooksPage()
    {
        $flightId = session('flight_id');
        $providerName = 'Aviation Academy';
        if ($flightId) {
            $provider = DB::table('training_providers')->where('id', $flightId)->first();
            if ($provider) {
                $providerName = $provider->name;
            }
        }

        $logbooks = collect();
        if ($flightId) {
            $logbooks = DB::table('aircrafts_logbook')
                ->join('students', 'aircrafts_logbook.student_id', '=', 'students.id')
                ->join('instructors', 'aircrafts_logbook.instructor_id', '=', 'instructors.id')
                ->where('students.flying_id', $flightId)
                ->select(
                    'aircrafts_logbook.*',
                    DB::raw("CONCAT(students.first_name, ' ', COALESCE(students.middle_name, ''), ' ', students.last_name) as student_name"),
                    DB::raw("CONCAT(instructors.first_name, ' ', COALESCE(instructors.middle_name, ''), ' ', instructors.last_name) as instructor_name")
                )
                ->orderBy('aircrafts_logbook.date_time', 'desc')
                ->get();
        }

        return view('admin.aircraft_logbooks.index', compact('providerName', 'logbooks'));
    }
}
