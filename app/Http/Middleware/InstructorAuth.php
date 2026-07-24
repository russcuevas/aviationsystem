<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstructorAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('instructor_logged_in') || !$request->session()->get('instructor_logged_in')) {
            return redirect()->route('login.page')->withErrors(['login_error' => 'Please log in as an Instructor to access this page.']);
        }

        return $next($request);
    }
}
