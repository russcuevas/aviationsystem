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

        // Calculate flight hours dynamically from schedules (start_time to end_time) + encoded flight_hours
        $allStudentSchedules = DB::table('schedules')
            ->join('students', 'schedules.student_id', '=', 'students.id')
            ->where('schedules.student_id', $studentId)
            ->where('students.flying_id', $flightId)
            ->select('schedules.*')
            ->get();

        $completedScheduleHours = 0;
        $scheduledFlightHours = 0;

        foreach ($allStudentSchedules as $sched) {
            $duration = 0;
            if (!empty($sched->start_time) && !empty($sched->end_time)) {
                try {
                    $start = \Carbon\Carbon::parse($sched->start_time);
                    $end = \Carbon\Carbon::parse($sched->end_time);
                    if ($end->lt($start)) {
                        $end->addDay();
                    }
                    $duration = round($start->diffInMinutes($end) / 60, 2);
                } catch (\Exception $e) {
                    $duration = 0;
                }
            }

            if (strtolower($sched->status) === 'completed') {
                $completedScheduleHours += $duration;
            } else {
                $scheduledFlightHours += $duration;
            }
        }

        $encodedFlightHours = DB::table('flight_hours')
            ->join('students', 'flight_hours.student_id', '=', 'students.id')
            ->where('flight_hours.student_id', $studentId)
            ->where('students.flying_id', $flightId)
            ->sum('flight_hours.total_time');

        $completedFlightHours = $completedScheduleHours + $encodedFlightHours;
        $totalFlightHours = $completedFlightHours + $scheduledFlightHours;

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

        // 3. Training Summary (scoped to student and designated flight_id) with Completion Percentage
        $gradeSheets = DB::table('grade_sheets')
            ->where('student_id', $studentId)
            ->where('status', 'Accepted')
            ->get();

        $acceptedLessons = [];
        foreach ($gradeSheets as $gs) {
            $lg = is_array($gs->lesson_grades) ? $gs->lesson_grades : json_decode($gs->lesson_grades, true);
            if ($lg) {
                foreach ($lg as $item) {
                    if (!empty($item['lesson'])) {
                        $acceptedLessons[] = trim($item['lesson']);
                    }
                }
            }
        }
        $acceptedLessons = array_unique($acceptedLessons);

        $schedules = DB::table('schedules')
            ->where('student_id', $studentId)
            ->get();

        $trainingSummary = DB::table('students_staging')
            ->join('students', 'students_staging.student_id', '=', 'students.id')
            ->where('students_staging.student_id', $studentId)
            ->where('students.flying_id', $flightId)
            ->select('students_staging.*')
            ->get();

        foreach ($trainingSummary as $stage) {
            $schedLessons = $schedules->where('stage_id', $stage->id)->pluck('lesson_type')->filter()->unique()->toArray();
            foreach ($gradeSheets as $gs) {
                if ($gs->stage_id == $stage->id || empty($gs->stage_id)) {
                    $lg = is_array($gs->lesson_grades) ? $gs->lesson_grades : json_decode($gs->lesson_grades, true);
                    if ($lg) {
                        foreach ($lg as $item) {
                            if (!empty($item['lesson'])) {
                                $schedLessons[] = trim($item['lesson']);
                            }
                        }
                    }
                }
            }
            $schedLessons = array_values(array_unique(array_filter($schedLessons)));
            $totalLessons = count($schedLessons);

            $completedCount = 0;
            foreach ($schedLessons as $lsn) {
                if (in_array(trim($lsn), $acceptedLessons)) {
                    $completedCount++;
                }
            }

            if (strtolower($stage->status) === 'completed') {
                $percentage = 100;
            } elseif ($totalLessons > 0) {
                $percentage = min(99, round(($completedCount / $totalLessons) * 100));
            } else {
                $percentage = 0;
            }

            $stage->total_lessons = $totalLessons;
            $stage->completed_lessons = $completedCount;
            $stage->completion_percentage = $percentage;
        }

        return view('student.dashboard.index', compact(
            'student',
            'lessonsCompleted',
            'upcomingFlightsCount',
            'totalFlightHours',
            'completedFlightHours',
            'scheduledFlightHours',
            'requiredHours',
            'hoursRemaining',
            'upcomingSchedules',
            'trainingSummary'
        ));
    }
}
