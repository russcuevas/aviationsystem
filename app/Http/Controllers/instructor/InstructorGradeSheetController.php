<?php

namespace App\Http\Controllers\instructor;

use App\Http\Controllers\Controller;
use App\Models\GradeSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstructorGradeSheetController extends Controller
{
    public function InstructorGradeSheetPage()
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

        $students = collect();
        if ($flightId) {
            $students = DB::table('students')->where('flying_id', $flightId)->orderBy('first_name')->get();
        } else {
            $students = DB::table('students')->orderBy('first_name')->get();
        }

        $studentIds = $students->pluck('id');
        $studentStages = DB::table('students_staging')->whereIn('student_id', $studentIds)->orderBy('created_at', 'asc')->get();
        $schedules = DB::table('schedules')->whereIn('student_id', $studentIds)->get();

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

        $existingGradeSheets = DB::table('grade_sheets')
            ->whereIn('student_id', $studentIds)
            ->get();

        foreach ($students as $student) {
            $studentGs = $existingGradeSheets->where('student_id', $student->id);
            $submittedLessonsMap = [];
            foreach ($studentGs as $gs) {
                $lg = is_array($gs->lesson_grades) ? $gs->lesson_grades : json_decode($gs->lesson_grades, true);
                if ($lg) {
                    foreach ($lg as $item) {
                        if (!empty($item['lesson'])) {
                            $submittedLessonsMap[trim($item['lesson'])] = [
                                'lesson' => trim($item['lesson']),
                                'status' => $gs->status,
                                'score' => $item['score'] ?? null,
                                'grade' => $item['grade'] ?? null,
                            ];
                        }
                    }
                }
            }
            $student->submitted_lessons = $submittedLessonsMap;

            $stages = $studentStages->where('student_id', $student->id)->values();
            foreach ($stages as $stg) {
                $schedLessons = $schedules->where('student_id', $student->id)
                    ->where('stage_id', $stg->id)
                    ->pluck('lesson_type')
                    ->filter()
                    ->unique()
                    ->values()
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

                $stg->lessons = array_values($schedLessons);
            }
            $student->stages = $stages->toArray();
        }

        $query = GradeSheet::with(['student', 'instructor', 'stage'])->orderBy('id', 'desc');
        if ($instructorId) {
            $query->where('instructor_id', $instructorId);
        }

        $gradeSheets = $query->get();

        return view('instructor.grade_sheets.index', compact('providerName', 'students', 'gradeSheets'));
    }

    public function store(Request $request)
    {
        $instructorId = session('instructor_id');

        $request->validate([
            'date' => 'required|date',
            'student_id' => 'required|exists:students,id',
            'stage_id' => 'nullable',
            'scores' => 'required|array',
            'remarks' => 'nullable|string',
        ]);

        $stageId = ($request->stage_id === 'all' || empty($request->stage_id)) ? null : $request->stage_id;

        $scores = $request->scores;
        $stagesData = $request->input('lesson_stages', []);
        $timeoutsData = $request->input('lesson_timeouts', []);

        $lessonGrades = [];
        $totalScoreSum = 0;
        $count = 0;
        $latestTimeOutDb = null;

        foreach ($scores as $lessonKey => $scoreVal) {
            if ($scoreVal !== null && $scoreVal !== '') {
                $scoreFloat = (float) $scoreVal;
                $letterGrade = $this->calculateLetterGrade($scoreFloat);
                $stageName = $stagesData[$lessonKey] ?? 'General';
                $timeOutRaw = $timeoutsData[$lessonKey] ?? null;

                $formattedTimeOut = null;
                if (!empty($timeOutRaw)) {
                    try {
                        $timestamp = strtotime($timeOutRaw);
                        if ($timestamp !== false) {
                            $formattedTimeOut = date('h:i A', $timestamp);
                            $latestTimeOutDb = date('H:i:s', $timestamp);
                        } else {
                            $formattedTimeOut = $timeOutRaw;
                        }
                    } catch (\Exception $e) {
                        $formattedTimeOut = $timeOutRaw;
                    }
                }

                $lessonGrades[] = [
                    'lesson' => $lessonKey,
                    'stage' => $stageName,
                    'time_out' => $formattedTimeOut,
                    'score' => $scoreFloat,
                    'grade' => $letterGrade
                ];

                $totalScoreSum += $scoreFloat;
                $count++;
            }
        }

        $totalScoreAvg = $count > 0 ? round($totalScoreSum / $count, 2) : 0.00;
        $overallGrade = $this->calculateLetterGrade($totalScoreAvg);

        GradeSheet::create([
            'date' => $request->date,
            'time_out' => $latestTimeOutDb,
            'student_id' => $request->student_id,
            'instructor_id' => $instructorId,
            'stage_id' => $stageId,
            'lesson_grades' => $lessonGrades,
            'total_score' => $totalScoreAvg,
            'overall_grade' => $overallGrade,
            'status' => 'For Review',
            'remarks' => $request->remarks,
        ]);

        return redirect()->back()->with('success', 'Combined grade sheet submitted successfully for review.');
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
