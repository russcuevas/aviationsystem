<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\superadmin\SuperadminDashboardController;
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
