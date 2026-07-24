<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperadminReportsController extends Controller
{
    public function SuperadminReportsPage()
    {
        return view('superadmin.reports.index');
    }
}
