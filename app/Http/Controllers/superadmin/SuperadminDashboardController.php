<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperadminDashboardController extends Controller
{
    public function SuperadminDashboardPage()
    {
        $totalSchools = DB::table('training_providers')->count();
        $totalInstructors = DB::table('instructors')->count();
        $totalStudents = DB::table('students')->count();
        $totalAircraft = DB::table('aircrafts')->count();

        $providers = DB::table('training_providers')->get();
        $schoolBreakdowns = [];

        foreach ($providers as $provider) {
            $stCount = DB::table('students')->where('flying_id', $provider->id)->count();
            $instCount = DB::table('instructors')->where('flying_id', $provider->id)->count();
            $airCount = DB::table('aircrafts')->where('flying_id', $provider->id)->count();
            $completedCount = DB::table('students')
                ->join('grade_sheets', 'students.id', '=', 'grade_sheets.student_id')
                ->where('students.flying_id', $provider->id)
                ->where('grade_sheets.status', 'Accepted')
                ->distinct('students.id')
                ->count('students.id');

            $schoolBreakdowns[] = (object)[
                'name' => $provider->name,
                'total_students' => $stCount,
                'total_instructors' => $instCount,
                'total_aircraft' => $airCount,
                'completed_students' => $completedCount,
            ];
        }

        return view('superadmin.dashboard.index', compact(
            'totalSchools',
            'totalInstructors',
            'totalStudents',
            'totalAircraft',
            'schoolBreakdowns'
        ));
    }
}
