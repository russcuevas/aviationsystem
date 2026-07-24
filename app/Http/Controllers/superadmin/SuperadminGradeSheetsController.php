<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperadminGradeSheetsController extends Controller
{
    public function SuperadminGradeSheetsPage()
    {
        return view('superadmin.grade_sheets.index');
    }
}
