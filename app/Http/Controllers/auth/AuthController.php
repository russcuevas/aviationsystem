<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function LoginPage()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 1. Check SuperAdmin
        $superadmin = DB::table('super_admins')->where('email', $request->email)->first();
        if ($superadmin && Hash::check($request->password, $superadmin->password)) {
            $request->session()->put([
                'superadmin_logged_in' => true,
                'superadmin_id' => $superadmin->id,
                'superadmin_name' => $superadmin->first_name . ' ' . $superadmin->last_name,
                'superadmin_email' => $superadmin->email,
            ]);
            return redirect()->route('superadmin.flight.school.page');
        }

        // 2. Check Admin
        $admin = DB::table('admins')->where('email', $request->email)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            $request->session()->put([
                'admin_logged_in' => true,
                'admin_id' => $admin->id,
                'admin_name' => $admin->first_name . ' ' . $admin->last_name,
                'admin_email' => $admin->email,
                'flight_id' => $admin->flight_id,
            ]);
            return redirect()->route('admin.dashboard.page');
        }

        // 3. Check Instructor
        $instructor = DB::table('instructors')->where('email', $request->email)->first();
        if ($instructor && Hash::check($request->password, $instructor->password)) {
            $fullName = trim($instructor->first_name . ' ' . ($instructor->middle_name ? $instructor->middle_name . ' ' : '') . $instructor->last_name);
            $request->session()->put([
                'instructor_logged_in' => true,
                'instructor_id' => $instructor->id,
                'instructor_name' => $fullName,
                'instructor_email' => $instructor->email,
                'flight_id' => $instructor->flying_id,
            ]);
            return redirect()->route('instructor.dashboard.page');
        }

        // 4. Check Student
        $student = DB::table('students')->where('email', $request->email)->first();
        if ($student && Hash::check($request->password, $student->password)) {
            $fullName = trim($student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name);
            $request->session()->put([
                'student_logged_in' => true,
                'student_id' => $student->id,
                'student_name' => $fullName,
                'student_email' => $student->email,
                'flight_id' => $student->flying_id,
            ]);
            return redirect()->route('student.dashboard.page');
        }

        return redirect()->back()->withInput()->withErrors([
            'login_error' => 'Invalid email address or password.'
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget([
            'superadmin_logged_in',
            'superadmin_id',
            'superadmin_name',
            'superadmin_email',
            'admin_logged_in',
            'admin_id',
            'admin_name',
            'admin_email',
            'instructor_logged_in',
            'instructor_id',
            'instructor_name',
            'instructor_email',
            'student_logged_in',
            'student_id',
            'student_name',
            'student_email',
            'flight_id',
        ]);

        return redirect()->route('login.page')->with('success', 'Logged out successfully.');
    }
}
