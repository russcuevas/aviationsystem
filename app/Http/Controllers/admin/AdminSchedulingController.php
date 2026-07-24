<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class AdminSchedulingController extends Controller
{
    public function AdminSchedulingPage()
    {
        $flightId = session('flight_id');
        if (!$flightId) {
            return redirect()->route('login.page')->withErrors(['login_error' => 'Invalid session context.']);
        }

        $provider = DB::table('training_providers')->where('id', $flightId)->first();
        $providerName = $provider ? $provider->name : 'Aviation Academy';

        // Get schedules joined with students, instructors, aircrafts, and staging configurations
        $schedules = DB::table('schedules')
            ->join('students', 'schedules.student_id', '=', 'students.id')
            ->join('students_staging', 'schedules.stage_id', '=', 'students_staging.id')
            ->join('instructors', 'schedules.instructor_id', '=', 'instructors.id')
            ->join('aircrafts', 'schedules.aircraft_id', '=', 'aircrafts.id')
            ->where('students.flying_id', $flightId)
            ->select(
                'schedules.*',
                DB::raw("CONCAT(students.first_name, ' ', COALESCE(students.middle_name, ''), ' ', students.last_name) as student_name"),
                'students_staging.stage as stage_name',
                DB::raw("CONCAT(instructors.first_name, ' ', COALESCE(instructors.middle_name, ''), ' ', instructors.last_name) as instructor_name"),
                'aircrafts.registration as aircraft_reg'
            )
            ->orderBy('schedules.date', 'desc')
            ->orderBy('schedules.start_time', 'asc')
            ->get();

        // Get dropdown lists filtered by flight provider, and load active/completed stages for each student
        $students = DB::table('students')->where('flying_id', $flightId)->orderBy('first_name')->get();
        $studentStages = DB::table('students_staging')->orderBy('created_at', 'asc')->get();
        
        foreach ($students as $student) {
            $student->stages = $studentStages->where('student_id', $student->id)->values()->toArray();
        }

        $instructors = DB::table('instructors')->where('flying_id', $flightId)->orderBy('first_name')->get();
        $aircrafts = DB::table('aircrafts')->where('flying_id', $flightId)->orderBy('registration')->get();

        return view('admin.scheduling.index', compact(
            'providerName',
            'schedules',
            'students',
            'instructors',
            'aircrafts'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'scheduleDate' => 'required|date',
            'scheduleStart' => 'required',
            'scheduleEnd' => 'required',
            'scheduleStudent' => 'required|integer|exists:students,id',
            'scheduleStage' => 'required|integer|exists:students_staging,id',
            'scheduleInstructor' => 'required|integer|exists:instructors,id',
            'scheduleAircraft' => 'required|integer|exists:aircrafts,id',
            'lessonType' => 'required|string|max:255',
            'scheduleRemarks' => 'nullable|string',
        ]);

        DB::table('schedules')->insert([
            'date' => $request->scheduleDate,
            'start_time' => $request->scheduleStart,
            'end_time' => $request->scheduleEnd,
            'student_id' => $request->scheduleStudent,
            'stage_id' => $request->scheduleStage,
            'instructor_id' => $request->scheduleInstructor,
            'aircraft_id' => $request->scheduleAircraft,
            'lesson_type' => $request->lessonType,
            'status' => 'Scheduled', // automatic Scheduled
            'remarks' => $request->scheduleRemarks,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Flight schedule saved successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'scheduleDate' => 'required|date',
            'scheduleStart' => 'required',
            'scheduleEnd' => 'required',
            'scheduleStudent' => 'required|integer|exists:students,id',
            'scheduleStage' => 'required|integer|exists:students_staging,id',
            'scheduleInstructor' => 'required|integer|exists:instructors,id',
            'scheduleAircraft' => 'required|integer|exists:aircrafts,id',
            'lessonType' => 'required|string|max:255',
            'scheduleStatus' => 'required|string|max:255',
            'scheduleRemarks' => 'nullable|string',
        ]);

        DB::table('schedules')->where('id', $id)->update([
            'date' => $request->scheduleDate,
            'start_time' => $request->scheduleStart,
            'end_time' => $request->scheduleEnd,
            'student_id' => $request->scheduleStudent,
            'stage_id' => $request->scheduleStage,
            'instructor_id' => $request->scheduleInstructor,
            'aircraft_id' => $request->scheduleAircraft,
            'lesson_type' => $request->lessonType,
            'status' => $request->scheduleStatus,
            'remarks' => $request->scheduleRemarks,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Flight schedule updated successfully.');
    }

    public function destroy($id)
    {
        DB::table('schedules')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Flight schedule deleted successfully.');
    }
}
