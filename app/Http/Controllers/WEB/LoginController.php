<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index()  {
        return view('admin/frontend/login');        
    }

    function login(Request $request) {
        $username = $request->username;
        $pass = $request->password;
    }
}
