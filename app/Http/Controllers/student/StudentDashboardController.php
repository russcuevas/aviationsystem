<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    public function StudentDashboardPage()
    {
        $studentId = session('student_id');
        $flightId = session('flight_id');

        // Security check: Verify that the student exists and belongs to the designated flight_id (flying_id)
        $student = DB::table('students')
            ->where('id', $studentId)
            ->where('flying_id', $flightId)
            ->first();

        if (!$student) {
            abort(403, 'Unauthorized access to this flight school data.');
        }

        // 1. Stats
        $lessonsCompleted = DB::table('schedules')
            ->join('students', 'schedules.student_id', '=', 'students.id')
            ->where('schedules.student_id', $studentId)
            ->where('students.flying_id', $flightId)
            ->where('schedules.status', 'Completed')
            ->count();

        $upcomingFlightsCount = DB::table('schedules')
            ->join('students', 'schedules.student_id', '=', 'students.id')
            ->where('schedules.student_id', $studentId)
            ->where('students.flying_id', $flightId)
            ->where('schedules.date', '>=', today())
            ->where('schedules.status', 'Scheduled')
            ->count();

        $totalFlightHours = DB::table('flight_hours')
            ->join('students', 'flight_hours.student_id', '=', 'students.id')
            ->where('flight_hours.student_id', $studentId)
            ->where('students.flying_id', $flightId)
            ->sum('flight_hours.total_time');

        // Total required hours from staging or default standard (e.g. 60 hours)
        $requiredHours = DB::table('students_staging')
            ->join('students', 'students_staging.student_id', '=', 'students.id')
            ->where('students_staging.student_id', $studentId)
            ->where('students.flying_id', $flightId)
            ->sum('students_staging.required_hours');

        if ($requiredHours == 0) {
            $requiredHours = 60;
        }

        $hoursRemaining = max(0, $requiredHours - $totalFlightHours);

        // 2. Upcoming Schedules table (scoped to student and designated flight_id)
        $upcomingSchedules = DB::table('schedules')
            ->join('students', 'schedules.student_id', '=', 'students.id')
            ->leftJoin('instructors', 'schedules.instructor_id', '=', 'instructors.id')
            ->leftJoin('aircrafts', 'schedules.aircraft_id', '=', 'aircrafts.id')
            ->where('schedules.student_id', $studentId)
            ->where('students.flying_id', $flightId)
            ->where('schedules.date', '>=', today())
            ->select(
                'schedules.*',
                DB::raw("CONCAT(instructors.first_name, ' ', instructors.last_name) as instructor_name"),
                'aircrafts.registration as aircraft_registration'
            )
            ->orderBy('schedules.date', 'asc')
            ->get();

        // 3. Training Summary (scoped to student and designated flight_id)
        $trainingSummary = DB::table('students_staging')
            ->join('students', 'students_staging.student_id', '=', 'students.id')
            ->where('students_staging.student_id', $studentId)
            ->where('students.flying_id', $flightId)
            ->select('students_staging.*')
            ->get();

        return view('student.dashboard.index', compact(
            'student',
            'lessonsCompleted',
            'upcomingFlightsCount',
            'totalFlightHours',
            'hoursRemaining',
            'upcomingSchedules',
            'trainingSummary'
        ));
    }
}
