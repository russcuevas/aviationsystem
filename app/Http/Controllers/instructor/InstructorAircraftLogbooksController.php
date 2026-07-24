<?php

namespace App\Http\Controllers\instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstructorAircraftLogbooksController extends Controller
{
    public function InstructorAircraftLogbooksPage()
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
        }

        $logbooks = collect();
        if ($instructorId) {
            $logbooks = DB::table('aircrafts_logbook')
                ->join('students', 'aircrafts_logbook.student_id', '=', 'students.id')
                ->join('instructors', 'aircrafts_logbook.instructor_id', '=', 'instructors.id')
                ->where('aircrafts_logbook.instructor_id', $instructorId)
                ->select(
                    'aircrafts_logbook.*',
                    DB::raw("CONCAT(students.first_name, ' ', COALESCE(students.middle_name, ''), ' ', students.last_name) as student_name"),
                    DB::raw("CONCAT(instructors.first_name, ' ', COALESCE(instructors.middle_name, ''), ' ', instructors.last_name) as instructor_name")
                )
                ->orderBy('aircrafts_logbook.date_time', 'desc')
                ->get();
        }

        return view('instructor.aircraft_logbooks.index', compact('providerName', 'students', 'logbooks'));
    }

    public function store(Request $request)
    {
        $instructorId = session('instructor_id');
        if (!$instructorId) {
            return redirect()->route('login.page')->withErrors(['login_error' => 'Invalid session context.']);
        }

        $request->validate([
            'aircraft' => 'required|string|max:255',
            'date_time' => 'required',
            'student_id' => 'required|integer|exists:students,id',
            'block_off_start' => 'required|numeric',
            'take_off' => 'required|numeric',
            'landing' => 'required|numeric',
            'block_on_off' => 'required|numeric',
            'fuel_used_gal' => 'required|numeric|min:0',
        ]);

        $blockOffStart = (float)$request->block_off_start;
        $blockOnOff = (float)$request->block_on_off;
        $takeOff = (float)$request->take_off;
        $landing = (float)$request->landing;

        $blockTime = $blockOffStart + $blockOnOff;
        $flightTime = abs($takeOff - $landing);

        DB::table('aircrafts_logbook')->insert([
            'aircraft' => $request->aircraft,
            'date_time' => $request->date_time,
            'student_id' => $request->student_id,
            'instructor_id' => $instructorId,
            'block_off_start' => $blockOffStart,
            'take_off' => $takeOff,
            'landing' => $landing,
            'block_on_off' => $blockOnOff,
            'block_time' => $blockTime,
            'flight_time' => $flightTime,
            'fuel_used_gal' => $request->fuel_used_gal,
            'technical_issues' => null,
            'mechanics' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Aircraft logbook entry saved successfully.');
    }
}
