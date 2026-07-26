<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use App\Models\GradeSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperadminGradeSheetsController extends Controller
{
    public function SuperadminGradeSheetsPage()
    {
        $gradeSheets = GradeSheet::with(['student', 'instructor', 'stage'])
            ->leftJoin('students', 'grade_sheets.student_id', '=', 'students.id')
            ->leftJoin('training_providers', 'students.flying_id', '=', 'training_providers.id')
            ->select('grade_sheets.*', 'training_providers.name as provider_name')
            ->orderBy('grade_sheets.id', 'desc')
            ->get();

        $studentsGrouped = $gradeSheets->groupBy('student_id');

        $compressedStudents = [];

        foreach ($studentsGrouped as $studentId => $sheets) {
            $firstSheet = $sheets->first();
            $studentObj = $firstSheet->student;

            if (!$studentObj) {
                continue;
            }

            $fullName = trim($studentObj->first_name . ' ' . ($studentObj->middle_name ? $studentObj->middle_name . ' ' : '') . $studentObj->last_name);
            $providerName = $firstSheet->provider_name ?? 'Aviation Academy';

            $totalSheets = $sheets->count();
            $acceptedCount = $sheets->whereIn('status', ['Accepted', 'Approved'])->count();
            $forReviewCount = $sheets->whereIn('status', ['For Review', 'Pending'])->count();
            $rejectedCount = $sheets->whereIn('status', ['Rejected'])->count();

            $avgScoreSum = $sheets->avg('total_score');
            $overallAvg = round($avgScoreSum, 1);

            $letterGrade = 'N/A';
            if ($overallAvg >= 95) $letterGrade = 'A+';
            elseif ($overallAvg >= 90) $letterGrade = 'A';
            elseif ($overallAvg >= 85) $letterGrade = 'B+';
            elseif ($overallAvg >= 80) $letterGrade = 'B';
            elseif ($overallAvg >= 75) $letterGrade = 'C+';
            elseif ($overallAvg >= 70) $letterGrade = 'C';
            elseif ($overallAvg > 0) $letterGrade = 'F';

            $sheetsList = [];
            foreach ($sheets as $s) {
                $uniqueStages = [];
                if (is_array($s->lesson_grades)) {
                    foreach ($s->lesson_grades as $lg) {
                        if (!empty($lg['stage'])) {
                            $uniqueStages[] = $lg['stage'];
                        }
                    }
                }
                $uniqueStages = array_unique($uniqueStages);
                $stgDisp = $s->stage ? $s->stage->stage : (count($uniqueStages) > 0 ? implode(', ', $uniqueStages) : 'All Stages (Combined)');

                $sheetsList[] = [
                    'sheet_id' => $s->sheet_id,
                    'date' => $s->date ? $s->date->format('M d, Y') : '-',
                    'time_out' => $s->time_out ? date('h:i A', strtotime($s->time_out)) : '-',
                    'instructor_name' => $s->instructor ? ($s->instructor->first_name . ' ' . $s->instructor->last_name) : 'N/A',
                    'stage' => $stgDisp,
                    'lessons_count' => is_array($s->lesson_grades) ? count($s->lesson_grades) : 0,
                    'total_score' => number_format($s->total_score, 1),
                    'overall_grade' => $s->overall_grade,
                    'status' => $s->status,
                    'remarks' => $s->remarks,
                    'lesson_grades' => $s->lesson_grades,
                ];
            }

            $compressedStudents[] = (object)[
                'student_id' => $studentObj->id,
                'student_code' => 'STU-' . date('Y', strtotime($studentObj->created_at)) . '-' . sprintf('%03d', $studentObj->id),
                'student_name' => $fullName,
                'provider_name' => $providerName,
                'total_sheets' => $totalSheets,
                'accepted_count' => $acceptedCount,
                'for_review_count' => $forReviewCount,
                'rejected_count' => $rejectedCount,
                'overall_avg' => $overallAvg,
                'overall_grade' => $letterGrade,
                'sheets_list' => $sheetsList,
            ];
        }

        return view('superadmin.grade_sheets.index', compact('compressedStudents'));
    }
}
