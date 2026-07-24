<?php

use App\Http\Controllers\admin\AdminDashboardController;
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
});

// ADMIN ROUTES
Route::middleware(['admin.auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'AdminDashboardPage'])->name('admin.dashboard.page');
});
