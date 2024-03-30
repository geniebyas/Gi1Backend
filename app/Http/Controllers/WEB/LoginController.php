<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index()  {
        return view('admin/frontend/login');        
    }

    function login(Request $request) {
        $username = $request->username;
        $pass = $request->password;

        if(Admin::where('username', $username)->where('password', $pass)->exists()){
            session_start();
            $_SESSION['username'] = $username;
            return view('admin/frontend/notification/send_public_noti');
        }else{
            return back();
        }
    }
}
