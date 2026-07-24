<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAircraftLogbooksController extends Controller
{
    public function AdminAirCraftLogbooksPage()
    {
        $flightId = session('flight_id');
        $providerName = 'Aviation Academy';
        if ($flightId) {
            $provider = DB::table('training_providers')->where('id', $flightId)->first();
            if ($provider) {
                $providerName = $provider->name;
            }
        }

        return view('admin.aircraft_logbooks.index', compact('providerName'));
    }
}

