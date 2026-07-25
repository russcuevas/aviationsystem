<?php

namespace App\Http\Controllers\instructor;

use App\Http\Controllers\Controller;
use App\Models\FlightHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstructorFlightHoursEncodingController extends Controller
{
    public function InstructorFlightHoursPage()
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
        $aircrafts = collect();
        $instructors = collect();

        if ($flightId) {
            $students = DB::table('students')->where('flying_id', $flightId)->orderBy('first_name')->get();
            $aircrafts = DB::table('aircrafts')->where('flying_id', $flightId)->orderBy('registration')->get();
            $instructors = DB::table('instructors')->where('flying_id', $flightId)->orderBy('first_name')->get();
        } else {
            $students = DB::table('students')->orderBy('first_name')->get();
            $aircrafts = DB::table('aircrafts')->orderBy('registration')->get();
            $instructors = DB::table('instructors')->orderBy('first_name')->get();
        }

        $studentIds = $students->pluck('id');
        $studentStages = DB::table('students_staging')->whereIn('student_id', $studentIds)->orderBy('created_at', 'asc')->get();
        $schedules = DB::table('schedules')->whereIn('student_id', $studentIds)->get();

        foreach ($students as $student) {
            $stages = $studentStages->where('student_id', $student->id)->values();
            foreach ($stages as $stg) {
                // Get ONLY lessons from scheduling table for this student and selected stage
                $schedLessons = $schedules->where('student_id', $student->id)
                    ->where('stage_id', $stg->id)
                    ->pluck('lesson_type')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                $stg->lessons = array_values($schedLessons);
            }
            $student->stages = $stages->toArray();
        }

        $flightHours = FlightHour::with(['student', 'instructor', 'aircraft', 'stage'])->orderBy('id', 'desc')->get();

        return view('instructor.flight_hours_encoding.index', compact('providerName', 'students', 'aircrafts', 'instructors', 'flightHours'));
    }

    public function store(Request $request)
    {
        $instructorId = session('instructor_id');

        $request->validate([
            'date' => 'required|date',
            'student_id' => 'required|exists:students,id',
            'aircraft_id' => 'required|exists:aircrafts,id',
            'stage_id' => 'nullable|exists:students_staging,id',
            'lesson' => 'nullable|string',
            'dual_instruction_time' => 'nullable|numeric|min:0',
            'pic_time' => 'nullable|numeric|min:0',
            'solo_time' => 'nullable|numeric|min:0',
            'instrument_flight_time' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $dual = (float) ($request->dual_instruction_time ?? 0);
        $pic = (float) ($request->pic_time ?? 0);
        $solo = (float) ($request->solo_time ?? 0);
        $inst = (float) ($request->instrument_flight_time ?? 0);
        $totalTime = $dual + $pic + $solo + $inst;

        FlightHour::create([
            'date' => $request->date,
            'student_id' => $request->student_id,
            'instructor_id' => $instructorId ?? $request->instructor_id,
            'aircraft_id' => $request->aircraft_id,
            'stage_id' => $request->stage_id,
            'lesson' => $request->lesson,
            'dual_instruction_time' => $request->dual_instruction_time,
            'pic_time' => $request->pic_time,
            'solo_time' => $request->solo_time,
            'instrument_flight_time' => $request->instrument_flight_time,
            'total_time' => $totalTime,
            'status' => 'pending review',
            'remarks' => $request->remarks,
        ]);

        return redirect()->back()->with('success', 'Flight hours encoded successfully.');
    }
}
