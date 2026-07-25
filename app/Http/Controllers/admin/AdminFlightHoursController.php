<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\FlightHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFlightHoursController extends Controller
{
    public function AdminFlightHoursPage()
    {
        $flightId = session('flight_id');

        $providerName = 'Aviation Academy';
        if ($flightId) {
            $provider = DB::table('training_providers')->where('id', $flightId)->first();
            if ($provider) {
                $providerName = $provider->name;
            }
        }

        $query = FlightHour::with(['student', 'instructor', 'aircraft', 'stage'])->orderBy('id', 'desc');

        if ($flightId) {
            $query->whereHas('student', function ($q) use ($flightId) {
                $q->where('flying_id', $flightId);
            });
        }

        $flightHours = $query->get();

        return view('admin.flight_hours.index', compact('providerName', 'flightHours'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,approved,cancelled,pending review',
        ]);

        $flightHour = FlightHour::findOrFail($id);
        $flightHour->status = $request->status;
        $flightHour->save();

        return redirect()->back()->with('success', "Flight hour status updated to '{$request->status}' successfully.");
    }
}
