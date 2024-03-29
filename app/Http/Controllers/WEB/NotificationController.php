<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()  {
        return view('admin/frontend/notification/send_public_noti');        
    }

    function send(Request $request) {
    }
}
