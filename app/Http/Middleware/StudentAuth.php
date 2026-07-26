<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('student_logged_in') || !$request->session()->get('student_logged_in')) {
            return redirect()->route('login.page')->withErrors(['login_error' => 'Please log in as a Student to access this page.']);
        }

        return $next($request);
    }
}
