<?php

namespace App\Http\Controllers\instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InstructorDashboardController extends Controller
{
    public function InstructorDashboardPage()
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

        $assignedStudentsCount = 0;
        $todaysFlightsCount = 0;
        $monthlyHours = 0;
        $todaysSchedules = [];
        $assignedStudents = [];

        if ($instructorId) {
            $assignedStudentsCount = DB::table('schedules')
                ->where('instructor_id', $instructorId)
                ->distinct('student_id')
                ->count('student_id');

            $todaysFlightsCount = DB::table('schedules')
                ->where('instructor_id', $instructorId)
                ->whereDate('date', today())
                ->count();

            $completedMonthSchedules = DB::table('schedules')
                ->where('instructor_id', $instructorId)
                ->where('status', 'Completed')
                ->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year)
                ->get();

            $totalMinutes = 0;
            foreach ($completedMonthSchedules as $sched) {
                try {
                    $start = Carbon::parse($sched->start_time);
                    $end = Carbon::parse($sched->end_time);
                    if ($end->lessThanOrEqualTo($start)) {
                        $end->addDay();
                    }
                    $totalMinutes += $end->diffInMinutes($start);
                } catch (\Exception $e) {
                }
            }
            $monthlyHours = round($totalMinutes / 60, 1);

            $todaysSchedules = DB::table('schedules')
                ->join('students', 'schedules.student_id', '=', 'students.id')
                ->join('aircrafts', 'schedules.aircraft_id', '=', 'aircrafts.id')
                ->where('schedules.instructor_id', $instructorId)
                ->whereDate('schedules.date', today())
                ->select(
                    'schedules.*',
                    DB::raw("CONCAT(students.first_name, ' ', COALESCE(students.middle_name, ''), ' ', students.last_name) as student_name"),
                    'aircrafts.registration as aircraft_reg'
                )
                ->orderBy('schedules.start_time', 'asc')
                ->get();

            $assignedStudents = DB::table('schedules')
                ->join('students', 'schedules.student_id', '=', 'students.id')
                ->join('aircrafts', 'schedules.aircraft_id', '=', 'aircrafts.id')
                ->where('schedules.instructor_id', $instructorId)
                ->select(
                    'students.id as student_id',
                    DB::raw("CONCAT(students.first_name, ' ', COALESCE(students.middle_name, ''), ' ', students.last_name) as student_name"),
                    'schedules.lesson_type as current_lesson',
                    'schedules.date as next_flight_date',
                    'schedules.start_time as next_flight_time',
                    'aircrafts.registration as aircraft_reg'
                )
                ->orderBy('schedules.date', 'desc')
                ->get()
                ->unique('student_id');
        }

        return view('instructor.dashboard.index', compact(
            'providerName',
            'assignedStudentsCount',
            'todaysFlightsCount',
            'monthlyHours',
            'todaysSchedules',
            'assignedStudents'
        ));
    }
}
