<?php

namespace App\Http\Controllers\instructor;

use App\Http\Controllers\Controller;
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
        if ($flightId) {
            $students = DB::table('students')->where('flying_id', $flightId)->orderBy('first_name')->get();
        }

        return view('instructor.flight_hours_encoding.index', compact('providerName', 'students'));
    }

    public function store(Request $request)
    {
        $instructorId = session('instructor_id');
        if (!$instructorId) {
            return redirect()->route('login.page')->withErrors(['login_error' => 'Invalid session context.']);
        }

        $request->validate([
            'hoursDate' => 'required|date',
            'hoursValue' => 'required|numeric|min:0.1',
            'hoursStudent' => 'required',
            'hoursAircraft' => 'required|string',
            'hoursFlightType' => 'required|string',
            'hoursRemarks' => 'nullable|string',
        ]);

        return redirect()->back()->with('success', 'Flight hours encoded successfully.');
    }
}
