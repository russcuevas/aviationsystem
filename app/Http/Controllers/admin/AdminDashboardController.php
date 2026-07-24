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

        if ($flightId) {
            $provider = DB::table('training_providers')->where('id', $flightId)->first();
            if ($provider) {
                $providerName = $provider->name;
            }
            
            $studentsCount = DB::table('students')->where('flying_id', $flightId)->count();
            $instructorsCount = DB::table('instructors')->where('flying_id', $flightId)->count();
            $aircraftsCount = DB::table('aircrafts')->where('flying_id', $flightId)->count();
        }

        return view('admin.dashboard.index', compact(
            'providerName', 
            'studentsCount', 
            'instructorsCount', 
            'aircraftsCount'
        ));
    }
}
