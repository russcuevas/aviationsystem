<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GradeSheet;
use Illuminate\Support\Facades\DB;

class StudentGradeController extends Controller
{
    public function StudentGradesPage()
    {
        $studentId = session('student_id');
        $flightId = session('flight_id');

        $providerName = 'Aviation Academy';
        if ($flightId) {
            $provider = DB::table('training_providers')->where('id', $flightId)->first();
            if ($provider) {
                $providerName = $provider->name;
            }
        }

        $gradeSheets = GradeSheet::with(['student', 'instructor', 'stage'])
            ->where('student_id', $studentId)
            ->orderBy('id', 'desc')
            ->get();

        return view('student.grades.index', compact('providerName', 'gradeSheets'));
    }
}
