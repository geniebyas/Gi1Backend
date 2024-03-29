<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtectRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        date_default_timezone_set("Asia/Kolkata");
        $uid = $request->header('uid');
        $user = User::where('uid', $uid)->get()->first();
        $path = $request->path();
        if (str_contains($path, "checkuserexists") || str_contains($path, "register") || str_contains($path, "isuniqueuser")) {

            return $next($request);
        } else if (is_null($user)) {
            $response = [
                'message' => 'Unauthorized Access',
                'status' => 0,
                'data'  => null,
            ];
            return response()->json($response, 401);
        } else {
            return $next($request);
        }
    }
}
