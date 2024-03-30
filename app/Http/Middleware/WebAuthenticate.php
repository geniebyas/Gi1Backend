<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->isAuthenticated($request)) {
            return $next($request);
        }

        return redirect('/login'); // Redirect to your login route
    }

    private function isAuthenticated($request)
    {
        // Your authentication logic using Admin model
        if ($request->session()->has('username')) {
            $username = $request->session()->get('username');
            $password = $request->session()->get('password');
            return Admin::where('username', $username)->where('password',$password)->exists();
        }
        return false;
    }
}
