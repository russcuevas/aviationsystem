<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperadminAircraftLogBooksController extends Controller
{
    public function SuperadminAircraftLogBooksPage()
    {
        return view('superadmin.aircraft_logbooks.index');
    }
}
