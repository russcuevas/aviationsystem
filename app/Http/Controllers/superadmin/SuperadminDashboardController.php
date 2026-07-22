<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperadminDashboardController extends Controller
{
    public function SuperadminDashboardPage()
    {
        return view('superadmin.dashboard.index');
    }
}
