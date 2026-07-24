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
            'flight_id',
        ]);

        return redirect()->route('login.page')->with('success', 'Logged out successfully.');
    }
}
