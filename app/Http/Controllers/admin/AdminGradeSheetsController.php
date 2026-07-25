<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\GradeSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminGradeSheetsController extends Controller
{
    public function AdminGradeSheetsPage()
    {
        $flightId = session('flight_id');

        $providerName = 'Aviation Academy';
        if ($flightId) {
            $provider = DB::table('training_providers')->where('id', $flightId)->first();
            if ($provider) {
                $providerName = $provider->name;
            }
        }

        $query = GradeSheet::with(['student', 'instructor', 'stage'])->orderBy('id', 'desc');

        if ($flightId) {
            $query->whereHas('student', function ($q) use ($flightId) {
                $q->where('flying_id', $flightId);
            });
        }

        $gradeSheets = $query->get();

        return view('admin.grade_sheets.index', compact('providerName', 'gradeSheets'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Accepted,For Review,Rejected',
        ]);

        $sheet = GradeSheet::findOrFail($id);

        if ($request->status === 'Rejected') {
            $sheetId = $sheet->sheet_id;
            $sheet->delete();
            return redirect()->back()->with('success', "Grade sheet {$sheetId} has been rejected and deleted from the system.");
        }

        $sheet->update([
            'status' => $request->status,
        ]);

        // When Grade Sheet is Accepted, update related scheduling lessons for that student to 'Completed'
        if ($request->status === 'Accepted') {
            $studentId = $sheet->student_id;
            $lessonGrades = is_array($sheet->lesson_grades) ? $sheet->lesson_grades : json_decode($sheet->lesson_grades, true);

            if ($lessonGrades && count($lessonGrades) > 0) {
                foreach ($lessonGrades as $lg) {
                    $lessonName = $lg['lesson'] ?? null;
                    if ($lessonName) {
                        DB::table('schedules')
                            ->where('student_id', $studentId)
                            ->where('lesson_type', $lessonName)
                            ->update([
                                'status' => 'Completed',
                                'updated_at' => now(),
                            ]);
                    }
                }
            } elseif ($sheet->stage_id) {
                DB::table('schedules')
                    ->where('student_id', $studentId)
                    ->where('stage_id', $sheet->stage_id)
                    ->update([
                        'status' => 'Completed',
                        'updated_at' => now(),
                    ]);
            }
        }

        return redirect()->back()->with('success', "Grade sheet {$sheet->sheet_id} status updated to 'Accepted' and related lesson schedules tagged as Completed.");
    }
}
