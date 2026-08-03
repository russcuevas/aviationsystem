<?php

use App\Http\Controllers\admin\AdminAircraftLogbooksController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminFlightHoursController;
use App\Http\Controllers\admin\AdminGradeSheetsController;
use App\Http\Controllers\admin\AdminSchedulingController;
use App\Http\Controllers\admin\AdminStudentProgressController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\instructor\InstructorAircraftLogbooksController;
use App\Http\Controllers\superadmin\SuperadminAircraftController;
use App\Http\Controllers\superadmin\SuperadminAircraftLogBooksController;
use App\Http\Controllers\superadmin\SuperadminDashboardController;
use App\Http\Controllers\superadmin\SuperadminFlightHoursController;
use App\Http\Controllers\admin\AdminReportsController;
use App\Http\Controllers\superadmin\SuperadminFlightSchoolController;
use App\Http\Controllers\superadmin\SuperadminGradeSheetsController;
use App\Http\Controllers\superadmin\SuperadminInstructorController;
use App\Http\Controllers\superadmin\SuperadminReportsController;
use App\Http\Controllers\superadmin\SuperadminStudentController;
use App\Http\Controllers\instructor\InstructorDashboardController;
use App\Http\Controllers\instructor\InstructorFlightHoursEncodingController;
use App\Http\Controllers\instructor\InstructorGradeSheetController;
use App\Http\Controllers\instructor\InstructorSchedulingController;
use App\Http\Controllers\instructor\InstructorStudentProgressController;
use App\Http\Controllers\student\StudentDashboardController;
use App\Http\Controllers\student\StudentFlightHoursController;
use App\Http\Controllers\student\StudentGradeController;
use App\Http\Controllers\student\StudentSchedulingController;
use App\Http\Controllers\student\StudentTrainingProgressController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [AuthController::class, 'LoginPage'])->name('login.page');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// SUPERADMIN ROUTES
Route::middleware(['superadmin.auth'])->group(function () {
    Route::get('/superadmin/dashboard', [SuperadminDashboardController::class, 'SuperadminDashboardPage'])->name('superadmin.dashboard.page');

    Route::get('/superadmin/flight-school', [SuperadminFlightSchoolController::class, 'SuperadminFlightSchoolPage'])->name('superadmin.flight.school.page');
    Route::post('/superadmin/flight-school', [SuperadminFlightSchoolController::class, 'store'])->name('superadmin.flight.school.store');
    Route::get('/superadmin/flight-school/{id}', [SuperadminFlightSchoolController::class, 'show'])->name('superadmin.flight.school.show');
    Route::post('/superadmin/flight-school/{id}/update', [SuperadminFlightSchoolController::class, 'update'])->name('superadmin.flight.school.update');
    Route::delete('/superadmin/flight-school/{id}', [SuperadminFlightSchoolController::class, 'destroy'])->name('superadmin.flight.school.destroy');

    Route::get('/superadmin/students', [SuperadminStudentController::class, 'SuperadminStudentPage'])->name('superadmin.student.page');
    Route::post('/superadmin/students', [SuperadminStudentController::class, 'store'])->name('superadmin.student.store');
    Route::post('/superadmin/students/{id}/update', [SuperadminStudentController::class, 'update'])->name('superadmin.student.update');
    Route::delete('/superadmin/students/{id}', [SuperadminStudentController::class, 'destroy'])->name('superadmin.student.destroy');

    Route::get('/superadmin/instructors', [SuperadminInstructorController::class, 'SuperadminInstructorPage'])->name('superadmin.instructor.page');
    Route::post('/superadmin/instructors', [SuperadminInstructorController::class, 'store'])->name('superadmin.instructor.store');
    Route::post('/superadmin/instructors/{id}/update', [SuperadminInstructorController::class, 'update'])->name('superadmin.instructor.update');
    Route::delete('/superadmin/instructors/{id}', [SuperadminInstructorController::class, 'destroy'])->name('superadmin.instructor.destroy');

    Route::get('/superadmin/aircraft', [SuperadminAircraftController::class, 'SuperadminAircraftPage'])->name('superadmin.aircraft.page');
    Route::post('/superadmin/aircraft', [SuperadminAircraftController::class, 'store'])->name('superadmin.aircraft.store');
    Route::post('/superadmin/aircraft/{id}/update', [SuperadminAircraftController::class, 'update'])->name('superadmin.aircraft.update');
    Route::delete('/superadmin/aircraft/{id}', [SuperadminAircraftController::class, 'destroy'])->name('superadmin.aircraft.destroy');

    Route::get('/superadmin/flight-hours', [SuperadminFlightHoursController::class, 'SuperadminFlightHoursPage'])->name('superadmin.flight.hours.page');
    Route::get('/superadmin/grade-sheets', [SuperadminGradeSheetsController::class, 'SuperadminGradeSheetsPage'])->name('superadmin.grade.sheets.page');
    Route::get('/superadmin/aircraft-logbook', [SuperadminAircraftLogBooksController::class, 'SuperadminAircraftLogBooksPage'])->name('superadmin.aircraft.logbook.page');
    Route::get('/superadmin/reports', [SuperadminReportsController::class, 'SuperadminReportsPage'])->name('superadmin.reports.page');
    Route::get('/superadmin/reports/export-pdf', [SuperadminReportsController::class, 'exportPdf'])->name('superadmin.reports.export.pdf');
    Route::get('/superadmin/reports/export-excel', [SuperadminReportsController::class, 'exportExcel'])->name('superadmin.reports.export.excel');
});

// ADMIN ROUTES
Route::middleware(['admin.auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'AdminDashboardPage'])->name('admin.dashboard.page');

    Route::get('/admin/scheduling', [AdminSchedulingController::class, 'AdminSchedulingPage'])->name('admin.scheduling.page');
    Route::post('/admin/scheduling', [AdminSchedulingController::class, 'store'])->name('admin.scheduling.store');
    Route::post('/admin/scheduling/{id}/update', [AdminSchedulingController::class, 'update'])->name('admin.scheduling.update');
    Route::delete('/admin/scheduling/{id}', [AdminSchedulingController::class, 'destroy'])->name('admin.scheduling.destroy');

    Route::get('/admin/aircraft-logbooks', [AdminAircraftLogbooksController::class, 'AdminAirCraftLogbooksPage'])->name('admin.aircraft.logbooks.page');

    Route::get('/admin/student-progress', [AdminStudentProgressController::class, 'AdminStudentProgressPage'])->name('admin.student.progress.page');

    Route::get('/admin/flight-hours', [AdminFlightHoursController::class, 'AdminFlightHoursPage'])->name('admin.flight.hours.page');
    Route::post('/admin/flight-hours/{id}/status', [AdminFlightHoursController::class, 'updateStatus'])->name('admin.flight.hours.update.status');

    Route::get('/admin/grade-sheets', [AdminGradeSheetsController::class, 'AdminGradeSheetsPage'])->name('admin.grade.sheets.page');
    Route::post('/admin/grade-sheets/{id}/status', [AdminGradeSheetsController::class, 'updateStatus'])->name('admin.grade.sheets.status.update');

    Route::get('/admin/reports', [AdminReportsController::class, 'AdminReportsPage'])->name('admin.reports.page');
    Route::get('/admin/reports/export-pdf', [AdminReportsController::class, 'exportPdf'])->name('admin.reports.export.pdf');
    Route::get('/admin/reports/export-excel', [AdminReportsController::class, 'exportExcel'])->name('admin.reports.export.excel');
});

// INSTRUCTOR ROUTES
Route::middleware(['instructor.auth'])->group(function () {
    Route::get('/instructor/dashboard', [InstructorDashboardController::class, 'InstructorDashboardPage'])->name('instructor.dashboard.page');
    Route::post('/instructor/dashboard/maintenance/{id}', [InstructorDashboardController::class, 'updateMaintenance'])->name('instructor.dashboard.maintenance.update');

    Route::get('/instructor/scheduling', [InstructorSchedulingController::class, 'InstructorSchedulingPage'])->name('instructor.scheduling.page');

    Route::get('/instructor/aircraft-logbooks', [InstructorAircraftLogbooksController::class, 'InstructorAircraftLogbooksPage'])->name('instructor.aircraft.logbooks.page');
    Route::post('/instructor/aircraft-logbooks', [InstructorAircraftLogbooksController::class, 'store'])->name('instructor.aircraft.logbooks.store');

    Route::get('/instructor/flight-hours-encoding', [InstructorFlightHoursEncodingController::class, 'InstructorFlightHoursPage'])->name('instructor.flight.hours.encoding.page');
    Route::post('/instructor/flight-hours-encoding', [InstructorFlightHoursEncodingController::class, 'store'])->name('instructor.flight.hours.encoding.store');

    Route::get('/instructor/student-progress', [InstructorStudentProgressController::class, 'InstructorStudentProgressPage'])->name('instructor.student.progress.page');
    Route::post('/instructor/student-progress/{id}/update', [InstructorStudentProgressController::class, 'updateProgress'])->name('instructor.student.progress.update');

    Route::get('/instructor/grade-sheet', [InstructorGradeSheetController::class, 'InstructorGradeSheetPage'])->name('instructor.grade.sheet.page');
    Route::post('/instructor/grade-sheet', [InstructorGradeSheetController::class, 'store'])->name('instructor.grade.sheet.store');
});

// STUDENT ROUTES
Route::middleware(['student.auth'])->group(function () {
    Route::get('/student/dashboard', [StudentDashboardController::class, 'StudentDashboardPage'])->name('student.dashboard.page');
    Route::get('/student/scheduling', [StudentSchedulingController::class, 'StudentSchedulingPage'])->name('student.scheduling.page');
    Route::get('/student/flight-hours', [StudentFlightHoursController::class, 'StudentFlightHoursPage'])->name('student.flight.hours.page');
    Route::get('/student/training-progress', [StudentTrainingProgressController::class, 'StudentTrainingProgressPage'])->name('student.training.progress.page');
    Route::get('/student/grades', [StudentGradeController::class, 'StudentGradesPage'])->name('student.grades.page');
});

