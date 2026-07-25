<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use App\Models\GradeSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperadminGradeSheetsController extends Controller
{
    public function SuperadminGradeSheetsPage()
    {
        $gradeSheets = GradeSheet::with(['student', 'instructor', 'stage'])
            ->leftJoin('students', 'grade_sheets.student_id', '=', 'students.id')
            ->leftJoin('training_providers', 'students.flying_id', '=', 'training_providers.id')
            ->select('grade_sheets.*', 'training_providers.name as provider_name')
            ->orderBy('grade_sheets.id', 'desc')
            ->get();

        return view('superadmin.grade_sheets.index', compact('gradeSheets'));
    }
}
