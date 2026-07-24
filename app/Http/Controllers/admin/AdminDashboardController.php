<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function AdminDashboardPage()
    {
        $flightId = session('flight_id');
        $providerName = 'Aviation Academy';
        $studentsCount = 0;
        $instructorsCount = 0;
        $aircraftsCount = 0;
        $todaysFlightsCount = 0;
        $stagesStats = [];
        $aircraftStatusStats = [];

        if ($flightId) {
            $provider = DB::table('training_providers')->where('id', $flightId)->first();
            if ($provider) {
                $providerName = $provider->name;
            }
            
            $studentsCount = DB::table('students')->where('flying_id', $flightId)->count();
            $instructorsCount = DB::table('instructors')->where('flying_id', $flightId)->count();
            $aircraftsCount = DB::table('aircrafts')->where('flying_id', $flightId)->count();
            
            $todaysFlightsCount = DB::table('schedules')
                ->join('students', 'schedules.student_id', '=', 'students.id')
                ->where('students.flying_id', $flightId)
                ->whereDate('schedules.date', today())
                ->count();

            // Group upcoming schedules by lesson_type and count the number of students
            $schedulesStats = DB::table('schedules')
                ->join('students', 'schedules.student_id', '=', 'students.id')
                ->where('students.flying_id', $flightId)
                ->where('schedules.date', '>=', today())
                ->where('schedules.status', 'Scheduled')
                ->select('schedules.lesson_type', DB::raw('count(schedules.student_id) as count'))
                ->groupBy('schedules.lesson_type')
                ->get()
                ->toArray();

            // Aircraft stats: registration, model, and total hours
            $aircraftStats = DB::table('aircrafts')
                ->where('flying_id', $flightId)
                ->where('status', 'Available')
                ->select('registration', 'model', 'total_hours')
                ->get()
                ->toArray();
        }

        return view('admin.dashboard.index', compact(
            'providerName', 
            'studentsCount', 
            'instructorsCount', 
            'aircraftsCount',
            'todaysFlightsCount',
            'schedulesStats',
            'aircraftStats'
        ));
    }
}
