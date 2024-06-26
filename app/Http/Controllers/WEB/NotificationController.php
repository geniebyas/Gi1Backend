<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\PublicNotification;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
    public function index(Request $request) {
        //session_start();
      //  $username = $request->session('username'); // Retrieve the username from the session
       // if(isset($username) && $username != ''){
            return view('admin/frontend/notification/send_public_noti')->with('username', "Admin");        
     //   } else {
   //         return back();
     //   }
    }
    

    public function send(Request $request) 
    {
        $title = $request->title;
        $body = $request->body;
        $is_announcement = $request->is_announcement;

        sendPublicNotification(new PublicNotification([
            'title' => $title,
            'body' => $body,
            'topic' => "all",
            'is_announcement'=>$is_announcement
        ]));

        return back();
    }
}
