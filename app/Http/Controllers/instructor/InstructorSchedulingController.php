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
        $students = collect();

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

            $studentIds = $schedules->pluck('student_id')->unique();
            $students = DB::table('students')->whereIn('id', $studentIds)->orderBy('first_name')->get();
            $studentStages = DB::table('students_staging')->whereIn('student_id', $studentIds)->orderBy('created_at', 'asc')->get();
            $gradeSheets = DB::table('grade_sheets')->whereIn('student_id', $studentIds)->get();

            foreach ($students as $student) {
                $stgs = $studentStages->where('student_id', $student->id)->values();
                $studentScheds = $schedules->where('student_id', $student->id)->values();
                $studentGs = $gradeSheets->where('student_id', $student->id);

                $acceptedLessons = [];
                foreach ($studentGs as $gs) {
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

                $stagesBreakdown = [];
                foreach ($stgs as $stg) {
                    $schedLessons = $studentScheds->where('stage_id', $stg->id)->pluck('lesson_type')->filter()->unique()->toArray();

                    foreach ($studentGs as $gs) {
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

                    $lessonsList = [];
                    foreach ($schedLessons as $lsnName) {
                        $cleanLsn = trim($lsnName);
                        $isCompleted = in_array($cleanLsn, $acceptedLessons);
                        $matchingSched = $studentScheds->firstWhere('lesson_type', $lsnName);

                        if ($isCompleted) {
                            $status = 'Completed';
                        } elseif ($matchingSched) {
                            $status = $matchingSched->status;
                        } else {
                            $status = 'Pending';
                        }

                        $lessonsList[] = [
                            'lesson_name' => $cleanLsn,
                            'status' => $status,
                            'date' => $matchingSched ? date('M j, Y', strtotime($matchingSched->date)) : null,
                            'time' => $matchingSched ? (date('h:i A', strtotime($matchingSched->start_time)) . ' - ' . date('h:i A', strtotime($matchingSched->end_time))) : null,
                            'aircraft' => $matchingSched->aircraft_reg ?? null,
                        ];
                    }

                    $stageStatus = $stg->status;
                    if (!empty($lessonsList)) {
                        $hasUncompleted = false;
                        foreach ($lessonsList as $lsn) {
                            if ($lsn['status'] !== 'Completed') {
                                $hasUncompleted = true;
                                break;
                            }
                        }
                        if ($hasUncompleted) {
                            $stageStatus = 'In Progress';
                            if ($stg->status === 'Completed') {
                                DB::table('students_staging')->where('id', $stg->id)->update([
                                    'status' => 'In Progress',
                                    'updated_at' => now()
                                ]);
                            }
                        }
                    }

                    $stagesBreakdown[] = [
                        'id' => $stg->id,
                        'stage' => $stg->stage,
                        'required_hours' => $stg->required_hours,
                        'status' => $stageStatus,
                        'lessons' => $lessonsList,
                    ];
                }

                $student->stages_breakdown = $stagesBreakdown;
                $student->schedules_count = $studentScheds->count();
                $student->schedules_list = $studentScheds->toArray();
            }
        }

        return view('instructor.scheduling.index', compact('providerName', 'schedules', 'students'));
    }
}
