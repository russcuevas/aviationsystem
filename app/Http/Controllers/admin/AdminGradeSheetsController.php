<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminGradeSheetsController extends Controller
{
    public function AdminGradeSheetsPage()
    {
        return view('admin.grade_sheets.index');
    }
}
