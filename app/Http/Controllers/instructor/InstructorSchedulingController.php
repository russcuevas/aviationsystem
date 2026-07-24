<?php

namespace App\Http\Controllers\instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstructorSchedulingController extends Controller
{
    public function InstructorSchedulingPage()
    {
        $flightId = session('flight_id');
        $instructorId = session('instructor_id');

        $providerName = 'Aviation Academy';
        if ($flightId) {
            $provider = DB::table('training_providers')->where('id', $flightId)->first();
            if ($provider) {
                $providerName = $provider->name;
            }
        }

        $schedules = collect();
        if ($instructorId) {
            $schedules = DB::table('schedules')
                ->join('students', 'schedules.student_id', '=', 'students.id')
                ->join('students_staging', 'schedules.stage_id', '=', 'students_staging.id')
                ->join('aircrafts', 'schedules.aircraft_id', '=', 'aircrafts.id')
                ->where('schedules.instructor_id', $instructorId)
                ->select(
                    'schedules.*',
                    DB::raw("CONCAT(students.first_name, ' ', COALESCE(students.middle_name, ''), ' ', students.last_name) as student_name"),
                    'students_staging.stage as stage_name',
                    'aircrafts.registration as aircraft_reg'
                )
                ->orderBy('schedules.date', 'desc')
                ->orderBy('schedules.start_time', 'asc')
                ->get();
        }

        return view('instructor.scheduling.index', compact('providerName', 'schedules'));
    }
}
