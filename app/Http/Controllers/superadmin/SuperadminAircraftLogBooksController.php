<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperadminAircraftLogBooksController extends Controller
{
    public function SuperadminAircraftLogBooksPage()
    {
        $logbooks = DB::table('aircrafts_logbook')
            ->join('students', 'aircrafts_logbook.student_id', '=', 'students.id')
            ->join('instructors', 'aircrafts_logbook.instructor_id', '=', 'instructors.id')
            ->leftJoin('training_providers', 'students.flying_id', '=', 'training_providers.id')
            ->select(
                'aircrafts_logbook.*',
                DB::raw("CONCAT(students.first_name, ' ', COALESCE(students.middle_name, ''), ' ', students.last_name) as student_name"),
                DB::raw("CONCAT(instructors.first_name, ' ', COALESCE(instructors.middle_name, ''), ' ', instructors.last_name) as instructor_name"),
                'training_providers.name as provider_name'
            )
            ->orderBy('aircrafts_logbook.date_time', 'desc')
            ->get();

        return view('superadmin.aircraft_logbooks.index', compact('logbooks'));
    }
}
