<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminStudentProgressController extends Controller
{
    public function AdminStudentProgressPage(Request $request)
    {
        $flightId = session('flight_id');

        $providerName = 'Aviation Academy';
        if ($flightId) {
            $provider = DB::table('training_providers')->where('id', $flightId)->first();
            if ($provider) {
                $providerName = $provider->name;
            }
        }

        // Query students
        $query = DB::table('students')
            ->leftJoin('training_providers', 'students.flying_id', '=', 'training_providers.id')
            ->select(
                'students.id as student_id',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.flying_id',
                'training_providers.name as provider_name'
            );

        if ($flightId) {
            $query->where('students.flying_id', $flightId);
        }

        $students = $query->orderBy('students.first_name', 'asc')->get();
        $studentIds = $students->pluck('student_id')->toArray();

        // Fetch stages for these students
        $stages = DB::table('students_staging')
            ->whereIn('student_id', $studentIds)
            ->get();

        // Fetch schedules for these students
        $schedules = DB::table('schedules')
            ->whereIn('student_id', $studentIds)
            ->orderBy('date', 'desc')
            ->orderBy('end_time', 'desc')
            ->get();

        $progressList = [];

        foreach ($students as $student) {
            $fullName = trim($student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name);
            $provider = $student->provider_name ?? $providerName;

            $studentStages = $stages->where('student_id', $student->student_id);

            if ($studentStages->isEmpty()) {
                $progressList[] = (object)[
                    'student_name' => $fullName,
                    'provider_name' => $provider,
                    'stage_name' => 'No Stage Assigned',
                    'last_lesson' => 'N/A',
                    'completed_hours' => 0,
                    'required_hours' => 0,
                    'total_hours_formatted' => '0 hours',
                    'progress_pct' => 0,
                    'health' => 'Critical Delay',
                    'stage_status' => 'Pending',
                ];
            } else {
                foreach ($studentStages as $stage) {
                    $stageSchedules = $schedules->where('stage_id', $stage->id);

                    // Calculate completed lesson hours for this specific training stage
                    $completedMinutes = 0;
                    $completedSchedules = $stageSchedules->where('status', 'Completed');

                    foreach ($completedSchedules as $sched) {
                        try {
                            $start = Carbon::parse($sched->start_time);
                            $end = Carbon::parse($sched->end_time);
                            if ($end->lessThanOrEqualTo($start)) {
                                $end->addDay();
                            }
                            $completedMinutes += $end->diffInMinutes($start);
                        } catch (\Exception $e) {
                            // ignore parse error
                        }
                    }

                    // Determine last lesson
                    $lastLesson = 'N/A';
                    if ($completedSchedules->isNotEmpty()) {
                        $lastLesson = $completedSchedules->first()->lesson_type;
                    } elseif ($stageSchedules->isNotEmpty()) {
                        $lastLesson = $stageSchedules->first()->lesson_type . ' (Scheduled)';
                    }

                    $completedHours = round($completedMinutes / 60, 1);
                    $requiredHours = (float)$stage->required_hours;

                    $pct = 0;
                    if ($requiredHours > 0) {
                        $pct = min(100, round(($completedHours / $requiredHours) * 100));
                    }

                    // Health determination based on progress percentage and stage status
                    if ($stage->status === 'Completed' || $pct >= 100) {
                        $health = 'On Track';
                    } elseif ($pct >= 60) {
                        $health = 'On Track';
                    } elseif ($pct >= 30) {
                        $health = 'Behind';
                    } else {
                        $health = 'Critical Delay';
                    }

                    $completedFormatted = (floor($completedHours) == $completedHours) ? (int)$completedHours : $completedHours;
                    $requiredFormatted = (floor($requiredHours) == $requiredHours) ? (int)$requiredHours : $requiredHours;

                    $totalHoursFormatted = $requiredFormatted . ' hours';

                    $progressList[] = (object)[
                        'student_name' => $fullName,
                        'provider_name' => $provider,
                        'stage_name' => $stage->stage,
                        'last_lesson' => $lastLesson,
                        'completed_hours' => $completedHours,
                        'required_hours' => $requiredHours,
                        'total_hours_formatted' => $totalHoursFormatted,
                        'progress_pct' => $pct,
                        'health' => $health,
                        'stage_status' => $stage->status,
                    ];
                }
            }
        }

        $totalTracked = count($progressList);
        $onTrackCount = collect($progressList)->where('health', 'On Track')->count();
        $behindCount = collect($progressList)->where('health', 'Behind')->count();
        $criticalCount = collect($progressList)->where('health', 'Critical Delay')->count();

        return view('admin.student_progress.index', compact(
            'providerName',
            'progressList',
            'totalTracked',
            'onTrackCount',
            'behindCount',
            'criticalCount'
        ));
    }
}
