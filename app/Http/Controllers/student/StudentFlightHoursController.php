<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentFlightHoursController extends Controller
{
    public function StudentFlightHoursPage()
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

        // Calculate hours from schedules
        $allStudentSchedules = DB::table('schedules')
            ->join('students', 'schedules.student_id', '=', 'students.id')
            ->leftJoin('students_staging', 'schedules.stage_id', '=', 'students_staging.id')
            ->leftJoin('instructors', 'schedules.instructor_id', '=', 'instructors.id')
            ->leftJoin('aircrafts', 'schedules.aircraft_id', '=', 'aircrafts.id')
            ->where('schedules.student_id', $studentId)
            ->select(
                'schedules.*',
                'students_staging.stage as stage_name',
                DB::raw("CONCAT(instructors.first_name, ' ', COALESCE(instructors.middle_name, ''), ' ', instructors.last_name) as instructor_name"),
                'aircrafts.registration as aircraft_registration'
            )
            ->get();

        $completedScheduleHours = 0;
        $scheduleRecords = collect();

        foreach ($allStudentSchedules as $sched) {
            $duration = 0;
            if (!empty($sched->start_time) && !empty($sched->end_time)) {
                try {
                    $start = Carbon::parse($sched->start_time);
                    $end = Carbon::parse($sched->end_time);
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
            }

            $scheduleRecords->push([
                'date' => $sched->date,
                'instructor' => $sched->instructor_name ?? 'N/A',
                'aircraft' => $sched->aircraft_registration ?? 'N/A',
                'stage' => $sched->stage_name ?? 'N/A',
                'lesson' => $sched->lesson_type ?? 'N/A',
                'hours' => $duration,
                'status' => $sched->status,
                'type' => 'Schedule',
            ]);
        }

        // Encoded flight hours
        $encodedFlightHoursList = DB::table('flight_hours')
            ->leftJoin('instructors', 'flight_hours.instructor_id', '=', 'instructors.id')
            ->leftJoin('aircrafts', 'flight_hours.aircraft_id', '=', 'aircrafts.id')
            ->leftJoin('students_staging', 'flight_hours.stage_id', '=', 'students_staging.id')
            ->where('flight_hours.student_id', $studentId)
            ->select(
                'flight_hours.*',
                'students_staging.stage as stage_name',
                DB::raw("CONCAT(instructors.first_name, ' ', COALESCE(instructors.middle_name, ''), ' ', instructors.last_name) as instructor_name"),
                'aircrafts.registration as aircraft_registration'
            )
            ->get();

        $encodedFlightHours = $encodedFlightHoursList->sum('total_time');
        $encodedRecords = collect();

        foreach ($encodedFlightHoursList as $fh) {
            $encodedRecords->push([
                'date' => $fh->date,
                'instructor' => $fh->instructor_name ?? 'N/A',
                'aircraft' => $fh->aircraft_registration ?? 'N/A',
                'stage' => $fh->stage_name ?? 'N/A',
                'lesson' => $fh->lesson ?? 'N/A',
                'hours' => (float)$fh->total_time,
                'status' => $fh->status ?? 'Validated',
                'type' => 'Encoded',
            ]);
        }

        $totalAccumulatedHours = round($completedScheduleHours + $encodedFlightHours, 1);

        $requiredHours = DB::table('students_staging')
            ->where('student_id', $studentId)
            ->sum('required_hours');

        if ($requiredHours == 0) {
            $requiredHours = 60;
        }

        $remainingHours = round(max(0, $requiredHours - $totalAccumulatedHours), 1);

        // Combine records and sort by date descending
        $flightRecords = $scheduleRecords->concat($encodedRecords)->sortByDesc('date')->values();

        return view('student.flight_hours.index', compact(
            'providerName',
            'totalAccumulatedHours',
            'remainingHours',
            'requiredHours',
            'flightRecords'
        ));
    }
}
