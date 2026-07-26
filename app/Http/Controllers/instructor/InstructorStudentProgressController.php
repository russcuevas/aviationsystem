<?php

namespace App\Http\Controllers\instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstructorStudentProgressController extends Controller
{
    public function InstructorStudentProgressPage()
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

        $query = DB::table('schedules')
            ->join('students', 'schedules.student_id', '=', 'students.id')
            ->join('students_staging', 'schedules.stage_id', '=', 'students_staging.id')
            ->select(
                'schedules.*',
                DB::raw("CONCAT(students.first_name, ' ', COALESCE(students.middle_name, ''), ' ', students.last_name) as student_name"),
                'students_staging.stage as stage_name',
                DB::raw("ROUND(TIME_TO_SEC(TIMEDIFF(schedules.end_time, schedules.start_time)) / 3600, 1) as calculated_hours")
            )
            ->orderBy('schedules.date', 'desc');

        if ($instructorId) {
            $query->where('schedules.instructor_id', $instructorId);
        } elseif ($flightId) {
            $query->where('students.flying_id', $flightId);
        }

        $schedules = $query->get();

        $studentIds = $schedules->pluck('student_id')->unique();
        $gradeSheets = DB::table('grade_sheets')
            ->whereIn('student_id', $studentIds)
            ->get();

        foreach ($schedules as $sched) {
            $existingScore = null;
            $existingGrade = null;
            foreach ($gradeSheets as $gs) {
                if ($gs->student_id == $sched->student_id) {
                    $lg = is_array($gs->lesson_grades) ? $gs->lesson_grades : json_decode($gs->lesson_grades, true);
                    if ($lg) {
                        foreach ($lg as $item) {
                            if (!empty($item['lesson']) && trim($item['lesson']) === trim($sched->lesson_type)) {
                                $existingScore = $item['score'] ?? null;
                                $existingGrade = $item['grade'] ?? null;
                                break 2;
                            }
                        }
                    }
                }
            }
            $sched->existing_score = $existingScore;
            $sched->existing_grade = $existingGrade;
        }

        return view('instructor.student_progress.index', compact('providerName', 'schedules'));
    }

    public function updateProgress(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Scheduled,In Progress,Completed',
            'score' => 'nullable|numeric|min:0|max:100',
            'remarks' => 'nullable|string',
        ]);

        $sched = DB::table('schedules')
            ->join('students_staging', 'schedules.stage_id', '=', 'students_staging.id')
            ->where('schedules.id', $id)
            ->select('schedules.*', 'students_staging.stage as stage_name')
            ->first();

        if (!$sched) {
            return redirect()->back()->with('error', 'Schedule record not found.');
        }

        $dbStatus = $request->status;
        if ($dbStatus === 'In Progress') {
            $dbStatus = 'Scheduled';
        } elseif ($dbStatus === 'Completed') {
            $dbStatus = 'Completed (Pending Approval)';
        }

        DB::table('schedules')->where('id', $id)->update([
            'status' => $dbStatus,
            'remarks' => $request->remarks,
            'updated_at' => now(),
        ]);

        // If score is provided or status is Completed, create/submit a Grade Sheet automatically for admin review
        if ($request->filled('score')) {
            $scoreFloat = (float) $request->score;
            $letterGrade = $this->calculateLetterGrade($scoreFloat);

            $timeOutRaw = $sched->end_time;
            $timeOutDb = null;
            $timeOut12h = null;
            if (!empty($timeOutRaw)) {
                $ts = strtotime($timeOutRaw);
                if ($ts !== false) {
                    $timeOutDb = date('H:i:s', $ts);
                    $timeOut12h = date('h:i A', $ts);
                }
            }

            $instructorId = session('instructor_id') ?: $sched->instructor_id;

            \App\Models\GradeSheet::create([
                'date' => $sched->date,
                'time_out' => $timeOutDb,
                'student_id' => $sched->student_id,
                'instructor_id' => $instructorId,
                'stage_id' => $sched->stage_id,
                'lesson_grades' => [
                    [
                        'lesson' => $sched->lesson_type,
                        'stage' => $sched->stage_name,
                        'time_out' => $timeOut12h,
                        'score' => $scoreFloat,
                        'grade' => $letterGrade
                    ]
                ],
                'total_score' => $scoreFloat,
                'overall_grade' => $letterGrade,
                'status' => 'For Review',
                'remarks' => $request->remarks,
            ]);

            return redirect()->back()->with('success', 'Progress marked and Grade Sheet submitted for Admin approval.');
        }

        return redirect()->back()->with('success', 'Student progress updated successfully.');
    }

    private function calculateLetterGrade($score)
    {
        if ($score >= 95) return 'A+';
        if ($score >= 90) return 'A';
        if ($score >= 85) return 'B+';
        if ($score >= 80) return 'B';
        if ($score >= 75) return 'C+';
        if ($score >= 70) return 'C';
        return 'F';
    }
}
