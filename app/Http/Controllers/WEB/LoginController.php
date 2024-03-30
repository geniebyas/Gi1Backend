<?php
namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index()  
    {
        return view('admin/frontend/login');        
    }

    public function login(Request $request) 
    {
        $username = $request->username;
        $pass = $request->password;

            return redirect('/send-notification'); // Assuming your route name for sending notification is 'notification.send'

        if(Admin::where('username', $username)->where('password', $pass)->exists()) {
            $request->session()->put('username', $username);
            return redirect('/send-notification'); // Assuming your route name for sending notification is 'notification.send'
        } else {
            return back()->withInput()->withErrors(['loginError' => 'Invalid username or password']);
        }
    }
}
