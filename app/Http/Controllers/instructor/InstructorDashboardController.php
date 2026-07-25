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

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. Calculate assigned students
        $assignedStudentsQuery = DB::table('schedules');
        if ($instructorId) {
            $assignedStudentsQuery->where('instructor_id', $instructorId);
        }
        $assignedStudentsCount = $assignedStudentsQuery->distinct('student_id')->count('student_id');

        // 2. Calculate today's flights
        $todaysFlightsQuery = DB::table('schedules');
        if ($instructorId) {
            $todaysFlightsQuery->where('instructor_id', $instructorId);
        }
        $todaysFlightsCount = $todaysFlightsQuery->whereDate('date', today())->count();

        // 3. Calculate Hours This Month from encoded flight_hours table
        $fhQuery = DB::table('flight_hours');
        if ($instructorId) {
            $fhQuery->where('instructor_id', $instructorId);
        }
        $encodedMonthlyHours = (float) $fhQuery->where(function ($q) use ($currentMonth, $currentYear) {
            $q->where(function ($q2) use ($currentMonth, $currentYear) {
                $q2->whereNotNull('date')
                   ->whereMonth('date', $currentMonth)
                   ->whereYear('date', $currentYear);
            })->orWhere(function ($q3) use ($currentMonth, $currentYear) {
                $q3->whereNull('date')
                   ->whereMonth('created_at', $currentMonth)
                   ->whereYear('created_at', $currentYear);
            });
        })->sum('total_time');

        // If no date-specific hours in current month, fallback to total encoded flight hours for this instructor
        if ($encodedMonthlyHours == 0) {
            $fhFallback = DB::table('flight_hours');
            if ($instructorId) {
                $fhFallback->where('instructor_id', $instructorId);
            }
            $encodedMonthlyHours = (float) $fhFallback->sum('total_time');
        }

        // Calculate from schedules table (completed / confirmed schedules)
        $schedQuery = DB::table('schedules')
            ->where(function ($q) {
                $q->where('status', 'Completed')->orWhere('status', 'Confirmed');
            });
        if ($instructorId) {
            $schedQuery->where('instructor_id', $instructorId);
        }

        $completedMonthSchedules = $schedQuery->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();

        $scheduleMinutes = 0;
        foreach ($completedMonthSchedules as $sched) {
            try {
                $start = Carbon::parse($sched->start_time);
                $end = Carbon::parse($sched->end_time);
                if ($end->lessThanOrEqualTo($start)) {
                    $end->addDay();
                }
                $scheduleMinutes += $end->diffInMinutes($start);
            } catch (\Exception $e) {
            }
        }
        $scheduledMonthlyHours = round($scheduleMinutes / 60, 1);

        $monthlyHours = max($encodedMonthlyHours, $scheduledMonthlyHours);

        // Fetch Today's Schedules
        $todaysSchedulesQuery = DB::table('schedules')
            ->join('students', 'schedules.student_id', '=', 'students.id')
            ->join('aircrafts', 'schedules.aircraft_id', '=', 'aircrafts.id')
            ->select(
                'schedules.*',
                DB::raw("CONCAT(students.first_name, ' ', COALESCE(students.middle_name, ''), ' ', students.last_name) as student_name"),
                'aircrafts.registration as aircraft_reg'
            )
            ->orderBy('schedules.start_time', 'asc');

        if ($instructorId) {
            $todaysSchedulesQuery->where('schedules.instructor_id', $instructorId)->whereDate('schedules.date', today());
        }

        $todaysSchedules = $todaysSchedulesQuery->get();

        // Fetch Assigned Students
        $assignedStudentsQuery = DB::table('schedules')
            ->join('students', 'schedules.student_id', '=', 'students.id')
            ->join('aircrafts', 'schedules.aircraft_id', '=', 'aircrafts.id')
            ->select(
                'students.id as student_id',
                DB::raw("CONCAT(students.first_name, ' ', COALESCE(students.middle_name, ''), ' ', students.last_name) as student_name"),
                'schedules.lesson_type as current_lesson',
                'schedules.date as next_flight_date',
                'schedules.start_time as next_flight_time',
                'aircrafts.registration as aircraft_reg'
            )
            ->orderBy('schedules.date', 'desc');

        if ($instructorId) {
            $assignedStudentsQuery->where('schedules.instructor_id', $instructorId);
        }

        $assignedStudents = $assignedStudentsQuery->get()->unique('student_id');

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
