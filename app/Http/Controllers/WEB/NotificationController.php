<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\PublicNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index() {
        session_start();
        if(isset($_SESSION['username']) && $_SESSION['username'] != ''){
            return view('admin/frontend/notification/send_public_noti');        
        } else {
            return back();
        }
    }
    

    function send(Request $request) {
        $title = $request->title;
        $body = $request->body;

        sendPublicNotification(new PublicNotification([
            'title' => $title,
            'body' => $body,
            'topic' =>"all"
        ]));

        return back();
    }
}
