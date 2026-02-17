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

    public $publicRoutes = [
        'news/all',
        'news/get/',
        'news/p/',
        'news/analytics',
        'news/track-share',
        'news/like',
        'checkuserexists',
        'register',
        'isuniqueuser',
        'git-deploy',
        'login',
        'registration',
        'publicusers',
        'admin',
        'api/jobs/all',
        'api/jobs/get/',
        'api/jobs/apply/'
    ];

    public function handle(Request $request, Closure $next): Response
    {
        date_default_timezone_set("Asia/Kolkata");
        $uid = $request->header('uid') ?? $request->header('Uid');
        $user = User::where('uid', $uid)->get()->first();
        $path = $request->path();
        if(in_array($path, $this->publicRoutes)){
            return $next($request);
        }
        if (str_contains($path, "checkuserexists") || str_contains($path, "register") || str_contains($path, "isuniqueuser") || str_contains($path,"git-deploy") || str_contains($path,"login") || str_contains($path,"registration") || str_contains($path,"publicusers") || str_contains($path,"admin") ) {
            $request->headers->set('uid', $uid);
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
