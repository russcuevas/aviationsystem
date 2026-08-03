<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\GradeSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportsController extends Controller
{
    public function AdminReportsPage()
    {
        $flightId = session('flight_id');

        $providerName = 'Aviation Academy';
        if ($flightId) {
            $provider = DB::table('training_providers')->where('id', $flightId)->first();
            if ($provider) {
                $providerName = $provider->name;
            }
        }

        // 1. School Performance Reports for Admin's provider
        $totalStudents = DB::table('students')
            ->when($flightId, function ($q) use ($flightId) {
                return $q->where('flying_id', $flightId);
            })->count();

        $completedStudents = DB::table('students')
            ->join('grade_sheets', 'students.id', '=', 'grade_sheets.student_id')
            ->when($flightId, function ($q) use ($flightId) {
                return $q->where('students.flying_id', $flightId);
            })
            ->where('grade_sheets.status', 'Accepted')
            ->distinct('students.id')
            ->count('students.id');

        $completionRate = $totalStudents > 0 ? round(($completedStudents / $totalStudents) * 100, 1) : 0.0;

        $totalSheets = DB::table('grade_sheets')
            ->join('students', 'grade_sheets.student_id', '=', 'students.id')
            ->when($flightId, function ($q) use ($flightId) {
                return $q->where('students.flying_id', $flightId);
            })
            ->count();

        $passingSheets = DB::table('grade_sheets')
            ->join('students', 'grade_sheets.student_id', '=', 'students.id')
            ->when($flightId, function ($q) use ($flightId) {
                return $q->where('students.flying_id', $flightId);
            })
            ->where('grade_sheets.total_score', '>=', 70)
            ->where('grade_sheets.overall_grade', '!=', 'F')
            ->count();

        $passingRate = $totalSheets > 0 ? round(($passingSheets / $totalSheets) * 100, 1) : 100.0;

        $schoolReports = [
            (object)[
                'provider_name' => $providerName,
                'total_students' => $totalStudents,
                'completed' => $completedStudents,
                'completion_rate' => $completionRate . '%',
                'passing_rate' => $passingRate . '%',
                'report_date' => now()->format('M d, Y'),
            ]
        ];

        // 2. Student Progress Monitoring data for Admin's provider
        $studentsQuery = DB::table('students')
            ->leftJoin('training_providers', 'students.flying_id', '=', 'training_providers.id')
            ->select(
                'students.id as student_id',
                DB::raw("CONCAT(students.first_name, ' ', COALESCE(students.middle_name, ''), ' ', students.last_name) as student_name"),
                'training_providers.name as provider_name'
            )
            ->orderBy('students.first_name', 'asc');

        if ($flightId) {
            $studentsQuery->where('students.flying_id', $flightId);
        }

        $students = $studentsQuery->get();
        $studentIds = $students->pluck('student_id')->toArray();

        $gradeSheets = GradeSheet::whereIn('student_id', $studentIds)->orderBy('id', 'desc')->get();
        $flightHours = DB::table('flight_hours')->whereIn('student_id', $studentIds)->get();
        $stages = DB::table('students_staging')->whereIn('student_id', $studentIds)->get();

        $progressMonitoring = [];

        foreach ($students as $student) {
            $stSheets = $gradeSheets->where('student_id', $student->student_id);
            $stHours = $flightHours->where('student_id', $student->student_id);
            $stStages = $stages->where('student_id', $student->student_id);

            $latestSheet = $stSheets->first();

            $totalEvaluatedLessons = 0;
            foreach ($stSheets as $gs) {
                if (is_array($gs->lesson_grades)) {
                    $totalEvaluatedLessons += count($gs->lesson_grades);
                }
            }

            $totalHours = $stHours->sum('total_time');
            $requiredHours = $stStages->first() ? (float)$stStages->first()->required_hours : 250;
            $requiredHoursFormatted = (floor($requiredHours) == $requiredHours) ? (int)$requiredHours : number_format($requiredHours, 1);

            $overallGrade = $latestSheet ? $latestSheet->overall_grade : 'N/A';
            $overallScore = $latestSheet ? number_format($latestSheet->total_score, 1) : null;

            $statusStr = $latestSheet ? $latestSheet->status : ($stStages->first() ? $stStages->first()->status : 'Active');

            $pct = $requiredHours > 0 ? min(100, round(($totalHours / $requiredHours) * 100)) : 0;
            if ($pct >= 85 || ($latestSheet && $latestSheet->overall_grade === 'A+')) {
                $progressLevel = 'Near Completion';
            } elseif ($pct >= 50 || ($latestSheet && in_array($latestSheet->overall_grade, ['A', 'B+', 'B']))) {
                $progressLevel = 'On Track';
            } elseif ($pct >= 25) {
                $progressLevel = 'Behind';
            } else {
                $progressLevel = 'Critical Delay';
            }

            $progressMonitoring[] = (object)[
                'student_name' => $student->student_name,
                'provider_name' => $student->provider_name ?? $providerName,
                'modules' => $totalEvaluatedLessons > 0 ? "{$totalEvaluatedLessons} lessons" : '0 lessons',
                'flight_hours' => $requiredHoursFormatted . ' hrs',
                'grade' => $overallGrade,
                'score' => $overallScore,
                'status' => $statusStr,
                'progress_level' => $progressLevel,
            ];
        }

        return view('admin.reports.index', compact('providerName', 'schoolReports', 'progressMonitoring'));
    }

    public function exportPdf()
    {
        $flightId = session('flight_id');
        $providerName = 'Aviation Academy';
        if ($flightId) {
            $provider = DB::table('training_providers')->where('id', $flightId)->first();
            if ($provider) {
                $providerName = $provider->name;
            }
        }

        $reportData = $this->getStudentFlightHoursReportData($flightId);
        $title = 'Student Flight Hours & Lesson Progress Report';
        $scope = $flightId ? $providerName : 'All Training Providers';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', compact('reportData', 'title', 'scope'));
        return $pdf->download('student_flight_hours_report_' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel()
    {
        $flightId = session('flight_id');
        $reportData = $this->getStudentFlightHoursReportData($flightId);

        $filename = 'student_flight_hours_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($reportData) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\BB\BF");
            fputcsv($file, ['Student', 'Training Provider', 'Total Flight Hours', 'Instructor']);

            foreach ($reportData as $row) {
                fputcsv($file, [
                    $row->student_name,
                    $row->provider_name,
                    $row->total_flight_hours,
                    $row->instructor_name,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStudentFlightHoursReportData($flightId = null)
    {
        $query = DB::table('students')
            ->leftJoin('training_providers', 'students.flying_id', '=', 'training_providers.id')
            ->select(
                'students.id as student_id',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'training_providers.name as provider_name'
            );

        if ($flightId) {
            $query->where('students.flying_id', $flightId);
        }

        $students = $query->orderBy('students.first_name', 'asc')->get();
        $studentIds = $students->pluck('student_id')->toArray();

        $schedules = DB::table('schedules')
            ->whereIn('student_id', $studentIds)
            ->get();

        $flightHours = DB::table('flight_hours')
            ->whereIn('student_id', $studentIds)
            ->get();

        $gradeSheets = DB::table('grade_sheets')
            ->whereIn('student_id', $studentIds)
            ->get();

        $instructors = DB::table('instructors')->get();

        $reportData = [];

        foreach ($students as $student) {
            $fullName = trim($student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name);
            $providerName = $student->provider_name ?? 'Aviation Academy';

            // Calculate schedule hours from progress of lessons
            $stSchedules = $schedules->where('student_id', $student->student_id);
            $scheduleHours = 0;
            foreach ($stSchedules as $sched) {
                if (!empty($sched->start_time) && !empty($sched->end_time)) {
                    try {
                        $start = \Carbon\Carbon::parse($sched->start_time);
                        $end = \Carbon\Carbon::parse($sched->end_time);
                        if ($end->lt($start)) {
                            $end->addDay();
                        }
                        $scheduleHours += round($start->diffInMinutes($end) / 60, 2);
                    } catch (\Exception $e) {
                    }
                }
            }

            // Encoded flight hours
            $stFlightHours = $flightHours->where('student_id', $student->student_id);
            $encodedHours = (float) $stFlightHours->sum('total_time');

            $totalHours = round($scheduleHours + $encodedHours, 1);
            $formattedTotalHours = (floor($totalHours) == $totalHours) ? (int)$totalHours . ' hrs' : number_format($totalHours, 1) . ' hrs';

            // Determine instructor(s)
            $instructorIds = collect();
            $instructorIds = $instructorIds->concat($stSchedules->pluck('instructor_id'))
                ->concat($stFlightHours->pluck('instructor_id'))
                ->concat($gradeSheets->where('student_id', $student->student_id)->pluck('instructor_id'))
                ->filter()
                ->unique();

            $instructorNames = [];
            foreach ($instructorIds as $instId) {
                $inst = $instructors->where('id', $instId)->first();
                if ($inst) {
                    $instName = trim($inst->first_name . ' ' . ($inst->middle_name ? $inst->middle_name . ' ' : '') . $inst->last_name);
                    $instructorNames[] = $instName;
                }
            }

            $instructorStr = !empty($instructorNames) ? implode(', ', array_unique($instructorNames)) : 'N/A';

            $reportData[] = (object)[
                'student_name' => $fullName,
                'provider_name' => $providerName,
                'total_flight_hours' => $formattedTotalHours,
                'instructor_name' => $instructorStr,
            ];
        }

        return $reportData;
    }
}
