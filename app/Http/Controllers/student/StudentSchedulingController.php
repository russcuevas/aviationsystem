<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentSchedulingController extends Controller
{
    public function StudentSchedulingPage()
    {
        $studentId = session('student_id');
        $flightId = session('flight_id');

        $providerName = 'Aviation Academy';
        if ($flightId) {
            $provider = DB::table('training_providers')->where('id', $flightId)->first();
            if ($provider) {
                $providerName = $provider->name;
            }
        }

        $schedules = DB::table('schedules')
            ->join('students', 'schedules.student_id', '=', 'students.id')
            ->join('students_staging', 'schedules.stage_id', '=', 'students_staging.id')
            ->leftJoin('instructors', 'schedules.instructor_id', '=', 'instructors.id')
            ->leftJoin('aircrafts', 'schedules.aircraft_id', '=', 'aircrafts.id')
            ->where('schedules.student_id', $studentId)
            ->select(
                'schedules.*',
                'students_staging.stage as stage_name',
                DB::raw("CONCAT(instructors.first_name, ' ', COALESCE(instructors.middle_name, ''), ' ', instructors.last_name) as instructor_name"),
                'aircrafts.registration as aircraft_registration',
                'aircrafts.model as aircraft_model'
            )
            ->orderBy('schedules.date', 'desc')
            ->orderBy('schedules.start_time', 'asc')
            ->get();

        return view('student.scheduling.index', compact('providerName', 'schedules'));
    }
}
