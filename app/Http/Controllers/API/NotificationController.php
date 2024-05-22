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

        $publicNotifications = PublicNotification::where("is_announcement",true)->get();
        $personalNotifications = PersonalNotification::where("receiver_uid",$uid)->with('sender')->get();
        return response()->json([
            'message' => 'Notification Collected',
            'status'=> 1,
            'data'=> [
                'public'=>$publicNotifications,
                'personal'=>$personalNotifications
            ]
        ]);
    }

    public function getAnnouncement(Request $request){
        $announcement = PublicNotification::where("is_announcement", true)
            ->orderBy('created_at', 'desc')
            ->first();
            
                    if($announcement != []){
            return response()->json([
                "message"=>"Announcement Loaded",
                "status"=>1,
                "data"=>$announcement[0]
            ]);
        }else{
            return response()->json([
                "message"=>"No Announcement Found",
                "status"=>0,
                "data"=>null
            ]);
        }
    }

}
