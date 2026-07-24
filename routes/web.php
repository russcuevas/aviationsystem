<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\superadmin\SuperadminAircraftController;
use App\Http\Controllers\superadmin\SuperadminAircraftLogBooksController;
use App\Http\Controllers\superadmin\SuperadminDashboardController;
use App\Http\Controllers\superadmin\SuperadminFlightHoursController;
use App\Http\Controllers\superadmin\SuperadminFlightSchoolController;
use App\Http\Controllers\superadmin\SuperadminGradeSheetsController;
use App\Http\Controllers\superadmin\SuperadminInstructorController;
use App\Http\Controllers\superadmin\SuperadminReportsController;
use App\Http\Controllers\superadmin\SuperadminStudentController;
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


Route::get('/login', [AuthController::class, 'LoginPage'])->name('auth.login.page');


// SUPERADMIN ROUTES
Route::get('/superadmin/dashboard', [SuperadminDashboardController::class, 'SuperadminDashboardPage'])->name('superadmin.dashboard.page');

Route::get('/superadmin/flight-school', [SuperadminFlightSchoolController::class, 'SuperadminFlightSchoolPage'])->name('superadmin.flight.school.page');
Route::post('/superadmin/flight-school', [SuperadminFlightSchoolController::class, 'store'])->name('superadmin.flight.school.store');
Route::get('/superadmin/flight-school/{id}', [SuperadminFlightSchoolController::class, 'show'])->name('superadmin.flight.school.show');
Route::post('/superadmin/flight-school/{id}/update', [SuperadminFlightSchoolController::class, 'update'])->name('superadmin.flight.school.update');
Route::delete('/superadmin/flight-school/{id}', [SuperadminFlightSchoolController::class, 'destroy'])->name('superadmin.flight.school.destroy');

Route::get('/superadmin/students', [SuperadminStudentController::class, 'SuperadminStudentPage'])->name('superadmin.student.page');

Route::get('/superadmin/instructors', [SuperadminInstructorController::class, 'SuperadminInstructorPage'])->name('superadmin.instructor.page');

Route::get('/superadmin/aircraft', [SuperadminAircraftController::class, 'SuperadminAircraftPage'])->name('superadmin.aircraft.page');

Route::get('/superadmin/flight-hours', [SuperadminFlightHoursController::class, 'SuperadminFlightHoursPage'])->name('superadmin.flight.hours.page');

Route::get('/superadmin/grade-sheets', [SuperadminGradeSheetsController::class, 'SuperadminGradeSheetsPage'])->name('superadmin.grade.sheets.page');

Route::get('/superadmin/aircraft-logbook', [SuperadminAircraftLogBooksController::class, 'SuperadminAircraftLogBooksPage'])->name('superadmin.aircraft.logbook.page');

Route::get('/superadmin/reports', [SuperadminReportsController::class, 'SuperadminReportsPage'])->name('superadmin.reports.page');
