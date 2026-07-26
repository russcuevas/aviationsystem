<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\GradeSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminGradeSheetsController extends Controller
{
    public function AdminGradeSheetsPage()
    {
        $flightId = session('flight_id');

        $providerName = 'Aviation Academy';
        if ($flightId) {
            $provider = DB::table('training_providers')->where('id', $flightId)->first();
            if ($provider) {
                $providerName = $provider->name;
            }
        }

        $query = GradeSheet::with(['student', 'instructor', 'stage'])->orderBy('id', 'desc');

        if ($flightId) {
            $query->whereHas('student', function ($q) use ($flightId) {
                $q->where('flying_id', $flightId);
            });
        }

        $gradeSheets = $query->get();

        return view('admin.grade_sheets.index', compact('providerName', 'gradeSheets'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Accepted,For Review,Rejected',
        ]);

        $sheet = GradeSheet::findOrFail($id);

        if ($request->status === 'Rejected') {
            $sheetId = $sheet->sheet_id;
            $sheet->delete();
            return redirect()->back()->with('success', "Grade sheet {$sheetId} has been rejected and deleted from the system.");
        }

        $sheet->update([
            'status' => $request->status,
        ]);

        // When Grade Sheet is Accepted, update related scheduling lessons for that student to 'Completed'
        if ($request->status === 'Accepted') {
            $studentId = $sheet->student_id;
            $lessonGrades = is_array($sheet->lesson_grades) ? $sheet->lesson_grades : json_decode($sheet->lesson_grades, true);

            if ($lessonGrades && count($lessonGrades) > 0) {
                foreach ($lessonGrades as $lg) {
                    $lessonName = $lg['lesson'] ?? null;
                    if ($lessonName) {
                        DB::table('schedules')
                            ->where('student_id', $studentId)
                            ->where('lesson_type', $lessonName)
                            ->update([
                                'status' => 'Completed',
                                'updated_at' => now(),
                            ]);
                    }
                }
            } elseif ($sheet->stage_id) {
                DB::table('schedules')
                    ->where('student_id', $studentId)
                    ->where('stage_id', $sheet->stage_id)
                    ->update([
                        'status' => 'Completed',
                        'updated_at' => now(),
                    ]);
            }

            // Check if all stage lessons have been accepted and update stage status to 'Completed'
            $this->checkAndUpdateStageCompletion($studentId);
        } elseif ($request->status === 'Rejected') {
            $studentId = $sheet->student_id;
            $lessonGrades = is_array($sheet->lesson_grades) ? $sheet->lesson_grades : json_decode($sheet->lesson_grades, true);

            if ($lessonGrades && count($lessonGrades) > 0) {
                foreach ($lessonGrades as $lg) {
                    $lessonName = $lg['lesson'] ?? null;
                    if ($lessonName) {
                        DB::table('schedules')
                            ->where('student_id', $studentId)
                            ->where('lesson_type', $lessonName)
                            ->where('status', 'Completed (Pending Approval)')
                            ->update([
                                'status' => 'Scheduled',
                                'updated_at' => now(),
                            ]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', "Grade sheet {$sheet->sheet_id} status updated to '{$request->status}' successfully.");
    }

    private function checkAndUpdateStageCompletion($studentId)
    {
        $studentStages = DB::table('students_staging')->where('student_id', $studentId)->get();
        if ($studentStages->isEmpty()) {
            return;
        }

        $acceptedGradeSheets = GradeSheet::where('student_id', $studentId)
            ->where('status', 'Accepted')
            ->get();

        $acceptedLessons = [];
        foreach ($acceptedGradeSheets as $gs) {
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

        $defaultLessons = [
            'Stage 1' => [
                'Lesson 1: Aircraft Orientation & Normal Procedures',
                'Lesson 2: Slow Flight, Stalls & Steep Turns',
                'Lesson 3: Traffic Pattern & Touch-and-Go Drills',
                'Lesson 4: Emergency Procedures & Forced Landings',
                'Lesson 5: First Solo Flight Evaluation',
            ],
            'Stage 2' => [
                'Lesson 6: Cross-Country Navigation Planning',
                'Lesson 7: Dual Cross-Country Flight',
                'Lesson 8: Solo Cross-Country Flight',
                'Lesson 9: Instrument & Night Navigation',
            ],
            'Stage 3' => [
                'Lesson 10: Instrument Flight & Partial Panel',
                'Lesson 11: Complex Maneuvers & Emergency Drills',
                'Lesson 12: Practical Test / Mock Checkride',
            ]
        ];

        foreach ($studentStages as $stg) {
            $schedLessons = DB::table('schedules')
                ->where('student_id', $studentId)
                ->where('stage_id', $stg->id)
                ->pluck('lesson_type')
                ->filter()
                ->unique()
                ->toArray();

            if (empty($schedLessons)) {
                $presets = [];
                foreach ($defaultLessons as $key => $items) {
                    if (stripos($stg->stage, $key) !== false || stripos($key, $stg->stage) !== false) {
                        $presets = $items;
                        break;
                    }
                }
                if (empty($presets)) {
                    $presets = [
                        'Lesson 1: Aircraft Orientation',
                        'Lesson 2: Flight Maneuvers & Traffic Pattern',
                        'Lesson 3: Cross-Country Navigation',
                        'Lesson 4: Solo & Checkride Preparation'
                    ];
                }
                $schedLessons = $presets;
            }

            $allCompleted = true;
            foreach ($schedLessons as $reqLesson) {
                if (!in_array(trim($reqLesson), $acceptedLessons)) {
                    $allCompleted = false;
                    break;
                }
            }

            if ($allCompleted && count($schedLessons) > 0) {
                DB::table('students_staging')
                    ->where('id', $stg->id)
                    ->update([
                        'status' => 'Completed',
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('students_staging')
                    ->where('id', $stg->id)
                    ->update([
                        'status' => 'In progress',
                        'updated_at' => now(),
                    ]);
            }
        }
    }
}
