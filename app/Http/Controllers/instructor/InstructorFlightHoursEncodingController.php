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
        if ($flightId) {
            $students = DB::table('students')->where('flying_id', $flightId)->orderBy('first_name')->get();
            $aircrafts = DB::table('aircrafts')->where('flying_id', $flightId)->orderBy('registration')->get();
        } else {
            $students = DB::table('students')->orderBy('first_name')->get();
            $aircrafts = DB::table('aircrafts')->orderBy('registration')->get();
        }

        $flightHours = FlightHour::with(['student', 'aircraft'])->orderBy('id', 'desc')->get();

        return view('instructor.flight_hours_encoding.index', compact('providerName', 'students', 'aircrafts', 'flightHours'));
    }

    public function store(Request $request)
    {
        $instructorId = session('instructor_id');
        if (!$instructorId) {
            return redirect()->route('login.page')->withErrors(['login_error' => 'Invalid session context.']);
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'aircraft_id' => 'required|exists:aircrafts,id',
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
            'student_id' => $request->student_id,
            'aircraft_id' => $request->aircraft_id,
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
