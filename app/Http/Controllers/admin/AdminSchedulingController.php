<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class AdminSchedulingController extends Controller
{
    public function AdminSchedulingPage()
    {
        $flightId = session('flight_id');
        if (!$flightId) {
            return redirect()->route('login.page')->withErrors(['login_error' => 'Invalid session context.']);
        }

        $provider = DB::table('training_providers')->where('id', $flightId)->first();
        $providerName = $provider ? $provider->name : 'Aviation Academy';

        // Get schedules joined with students, instructors, aircrafts, and staging configurations
        $schedules = DB::table('schedules')
            ->join('students', 'schedules.student_id', '=', 'students.id')
            ->join('students_staging', 'schedules.stage_id', '=', 'students_staging.id')
            ->join('instructors', 'schedules.instructor_id', '=', 'instructors.id')
            ->join('aircrafts', 'schedules.aircraft_id', '=', 'aircrafts.id')
            ->where('students.flying_id', $flightId)
            ->select(
                'schedules.*',
                DB::raw("CONCAT(students.first_name, ' ', COALESCE(students.middle_name, ''), ' ', students.last_name) as student_name"),
                'students_staging.stage as stage_name',
                DB::raw("CONCAT(instructors.first_name, ' ', COALESCE(instructors.middle_name, ''), ' ', instructors.last_name) as instructor_name"),
                'aircrafts.registration as aircraft_reg'
            )
            ->orderBy('schedules.date', 'desc')
            ->orderBy('schedules.start_time', 'asc')
            ->get();

        $allStudents = DB::table('students')->where('flying_id', $flightId)->orderBy('first_name')->get();
        $studentIds = $allStudents->pluck('id');
        $studentStages = DB::table('students_staging')->whereIn('student_id', $studentIds)->orderBy('created_at', 'asc')->get();
        $gradeSheets = DB::table('grade_sheets')->whereIn('student_id', $studentIds)->get();

        foreach ($allStudents as $student) {
            $stgs = $studentStages->where('student_id', $student->id)->values();
            $student->stages = $stgs->toArray();
            $studentScheds = $schedules->where('student_id', $student->id)->values();
            $studentGs = $gradeSheets->where('student_id', $student->id);

            $acceptedLessons = [];
            foreach ($studentGs as $gs) {
                if ($gs->status === 'Accepted') {
                    $lg = is_array($gs->lesson_grades) ? $gs->lesson_grades : json_decode($gs->lesson_grades, true);
                    if ($lg) {
                        foreach ($lg as $item) {
                            if (!empty($item['lesson'])) {
                                $acceptedLessons[] = trim($item['lesson']);
                            }
                        }
                    }
                }
            }
            $acceptedLessons = array_unique($acceptedLessons);

            $stagesBreakdown = [];
            foreach ($stgs as $stg) {
                $schedLessons = $studentScheds->where('stage_id', $stg->id)->pluck('lesson_type')->filter()->unique()->toArray();

                foreach ($studentGs as $gs) {
                    if ($gs->stage_id == $stg->id || empty($gs->stage_id)) {
                        $lg = is_array($gs->lesson_grades) ? $gs->lesson_grades : json_decode($gs->lesson_grades, true);
                        if ($lg) {
                            foreach ($lg as $item) {
                                if (!empty($item['lesson'])) {
                                    $schedLessons[] = trim($item['lesson']);
                                }
                            }
                        }
                    }
                }
                $schedLessons = array_unique(array_filter($schedLessons));

                $lessonsList = [];
                foreach ($schedLessons as $lsnName) {
                    $cleanLsn = trim($lsnName);
                    $isCompleted = in_array($cleanLsn, $acceptedLessons);
                    $matchingSched = $studentScheds->firstWhere('lesson_type', $lsnName);

                    if ($isCompleted) {
                        $status = 'Completed';
                    } elseif ($matchingSched) {
                        $status = $matchingSched->status;
                    } else {
                        $status = 'Pending';
                    }

                    $lessonsList[] = [
                        'lesson_name' => $cleanLsn,
                        'status' => $status,
                        'date' => $matchingSched ? date('M j, Y', strtotime($matchingSched->date)) : null,
                        'time' => $matchingSched ? (date('h:i A', strtotime($matchingSched->start_time)) . ' - ' . date('h:i A', strtotime($matchingSched->end_time))) : null,
                        'instructor' => $matchingSched->instructor_name ?? null,
                        'aircraft' => $matchingSched->aircraft_reg ?? null,
                        'route' => $matchingSched->route ?? null,
                    ];
                }

                $stageStatus = $stg->status;
                if (!empty($lessonsList)) {
                    $hasUncompleted = false;
                    foreach ($lessonsList as $lsn) {
                        if ($lsn['status'] !== 'Completed') {
                            $hasUncompleted = true;
                            break;
                        }
                    }
                    if ($hasUncompleted) {
                        $stageStatus = 'In Progress';
                        if ($stg->status === 'Completed') {
                            DB::table('students_staging')->where('id', $stg->id)->update([
                                'status' => 'In Progress',
                                'updated_at' => now()
                            ]);
                        }
                    }
                }

                $stagesBreakdown[] = [
                    'id' => $stg->id,
                    'stage' => $stg->stage,
                    'required_hours' => $stg->required_hours,
                    'status' => $stageStatus,
                    'lessons' => $lessonsList,
                ];
            }

            $student->stages_breakdown = $stagesBreakdown;
            $student->schedules_count = $studentScheds->count();
            $student->schedules_list = $studentScheds->toArray();
        }

        // Only display students who have existing scheduled lessons in the main scheduling board table
        $students = $allStudents->filter(function ($student) {
            return $student->schedules_count > 0;
        })->values();

        $instructors = DB::table('instructors')->where('flying_id', $flightId)->orderBy('first_name')->get();
        $aircrafts = DB::table('aircrafts')->where('flying_id', $flightId)->orderBy('registration')->get();

        return view('admin.scheduling.index', compact(
            'providerName',
            'schedules',
            'students',
            'allStudents',
            'instructors',
            'aircrafts'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'scheduleDate' => 'required|date',
            'scheduleStart' => 'required',
            'scheduleEnd' => 'required',
            'scheduleStudent' => 'required|integer|exists:students,id',
            'scheduleStage' => 'required|integer|exists:students_staging,id',
            'scheduleInstructor' => 'required|integer|exists:instructors,id',
            'scheduleAircraft' => 'required|integer|exists:aircrafts,id',
            'lessonType' => 'required|string|max:255',
            'scheduleRoute' => 'nullable|string|max:255',
            'scheduleRemarks' => 'nullable|string',
        ]);

        DB::table('schedules')->insert([
            'date' => $request->scheduleDate,
            'start_time' => $request->scheduleStart,
            'end_time' => $request->scheduleEnd,
            'student_id' => $request->scheduleStudent,
            'stage_id' => $request->scheduleStage,
            'instructor_id' => $request->scheduleInstructor,
            'aircraft_id' => $request->scheduleAircraft,
            'lesson_type' => $request->lessonType,
            'route' => $request->scheduleRoute,
            'status' => 'Scheduled', // automatic Scheduled
            'remarks' => $request->scheduleRemarks,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Revert stage status to In Progress as a new lesson/schedule has been added to this stage
        DB::table('students_staging')
            ->where('id', $request->scheduleStage)
            ->update([
                'status' => 'In Progress',
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Flight schedule saved successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'scheduleDate' => 'required|date',
            'scheduleStart' => 'required',
            'scheduleEnd' => 'required',
            'scheduleStudent' => 'required|integer|exists:students,id',
            'scheduleStage' => 'required|integer|exists:students_staging,id',
            'scheduleInstructor' => 'required|integer|exists:instructors,id',
            'scheduleAircraft' => 'required|integer|exists:aircrafts,id',
            'lessonType' => 'required|string|max:255',
            'scheduleRoute' => 'nullable|string|max:255',
            'scheduleStatus' => 'required|string|max:255',
            'scheduleRemarks' => 'nullable|string',
        ]);

        DB::table('schedules')->where('id', $id)->update([
            'date' => $request->scheduleDate,
            'start_time' => $request->scheduleStart,
            'end_time' => $request->scheduleEnd,
            'student_id' => $request->scheduleStudent,
            'stage_id' => $request->scheduleStage,
            'instructor_id' => $request->scheduleInstructor,
            'aircraft_id' => $request->scheduleAircraft,
            'lesson_type' => $request->lessonType,
            'route' => $request->scheduleRoute,
            'status' => $request->scheduleStatus,
            'remarks' => $request->scheduleRemarks,
            'updated_at' => now(),
        ]);

        // If the schedule status is not Completed, set stage to In Progress
        if ($request->scheduleStatus !== 'Completed') {
            DB::table('students_staging')
                ->where('id', $request->scheduleStage)
                ->update([
                    'status' => 'In Progress',
                    'updated_at' => now(),
                ]);
        }

        return redirect()->back()->with('success', 'Flight schedule updated successfully.');
    }

    public function destroy($id)
    {
        DB::table('schedules')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Flight schedule deleted successfully.');
    }
}
