<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\PublicNotification;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
    public function index() 
    {
        return view('admin/frontend/notification/send_public_noti'); // This method will only be accessible to authenticated users
    }

    public function send(Request $request) 
    {
        $title = $request->title;
        $body = $request->body;

        sendPublicNotification(new PublicNotification([
            'title' => $title,
            'body' => $body,
            'topic' => "all"
        ]));

        return back();
    }
}
