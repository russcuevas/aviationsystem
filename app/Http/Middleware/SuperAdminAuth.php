<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('superadmin_logged_in') || !$request->session()->get('superadmin_logged_in')) {
            return redirect()->route('login.page')->withErrors(['login_error' => 'Please log in as a SuperAdmin to access this page.']);
        }

        return $next($request);
    }
}
