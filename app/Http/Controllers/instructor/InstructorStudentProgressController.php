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

        return view('instructor.student_progress.index', compact('providerName', 'schedules'));
    }

    public function updateProgress(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Scheduled,In Progress,Completed',
            'remarks' => 'nullable|string',
        ]);

        $dbStatus = $request->status;
        if ($dbStatus === 'In Progress') {
            $dbStatus = 'Scheduled';
        }

        DB::table('schedules')->where('id', $id)->update([
            'status' => $dbStatus,
            'remarks' => $request->remarks,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Student progress updated successfully.');
    }
}
