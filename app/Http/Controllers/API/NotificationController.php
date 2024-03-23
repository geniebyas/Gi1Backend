<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PersonalNotification;
use App\Models\PublicNotification;
use Illuminate\Http\Request;
class NotificationController extends Controller
{
    public function getNotifications(Request $request){
        $uid = $request->header('uid');

        $publicNotifications = PublicNotification::get();
        $personalNotifications = PersonalNotification::where("reciever_uid",$uid)->with('sender')->get();


    }
}
