<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentTrainingProgressController extends Controller
{
    public function StudentTrainingProgressPage()
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

        $stages = DB::table('students_staging')
            ->where('student_id', $studentId)
            ->orderBy('id', 'asc')
            ->get();

        $schedules = DB::table('schedules')
            ->leftJoin('instructors', 'schedules.instructor_id', '=', 'instructors.id')
            ->where('schedules.student_id', $studentId)
            ->select(
                'schedules.*',
                DB::raw("CONCAT(instructors.first_name, ' ', COALESCE(instructors.middle_name, ''), ' ', instructors.last_name) as instructor_name")
            )
            ->get();

        $gradeSheets = DB::table('grade_sheets')
            ->where('student_id', $studentId)
            ->get();

        $acceptedLessons = [];
        foreach ($gradeSheets as $gs) {
            if ($gs->status === 'Accepted') {
                $lg = is_array($gs->lesson_grades) ? $gs->lesson_grades : json_decode($gs->lesson_grades, true);
                if ($lg) {
                    foreach ($lg as $item) {
                        if (!empty($item['lesson'])) {
                            $acceptedLessons[] = trim($item['lesson']);
                        }
                    }
                }
            }
        }
        $acceptedLessons = array_unique($acceptedLessons);

        $progressList = [];

        foreach ($stages as $stg) {
            $schedLessons = $schedules->where('stage_id', $stg->id)->pluck('lesson_type')->filter()->unique()->toArray();

            foreach ($gradeSheets as $gs) {
                if ($gs->stage_id == $stg->id || empty($gs->stage_id)) {
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
            $schedLessons = array_unique(array_filter($schedLessons));

            foreach ($schedLessons as $lsnName) {
                $cleanLsn = trim($lsnName);
                $isCompleted = in_array($cleanLsn, $acceptedLessons);
                $matchingSched = $schedules->firstWhere('lesson_type', $lsnName);

                $status = $isCompleted ? 'Completed' : 'In Progress';

                $progressList[] = [
                    'stage' => $stg->stage,
                    'lesson' => $cleanLsn,
                    'instructor' => $matchingSched->instructor_name ?? 'N/A',
                    'date_updated' => $matchingSched ? date('M j, Y', strtotime($matchingSched->updated_at ?? $matchingSched->date)) : '-',
                    'date_raw' => $matchingSched ? ($matchingSched->updated_at ?? $matchingSched->date) : null,
                    'status' => $status,
                    'remarks' => !empty($matchingSched->remarks) ? $matchingSched->remarks : null,
                ];
            }
        }

        return view('student.training_progress.index', compact('providerName', 'progressList'));
    }
}
