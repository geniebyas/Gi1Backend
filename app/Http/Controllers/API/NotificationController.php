<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PersonalNotification;
use App\Models\PublicNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        $uid = $request->header('uid');

        $publicNotifications = PublicNotification::where("is_announcement", true)->get();
        $personalNotifications = PersonalNotification::where("receiver_uid", $uid)->with('sender')->get();
        return response()->json([
            'message' => 'Notification Collected',
            'status' => 1,
            'data' => [
                'public' => $publicNotifications,
                'personal' => $personalNotifications
            ]
        ]);
    }

    public function getAnnouncement(Request $request)
    {
        $announcement = PublicNotification::where("is_announcement", true)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($announcement != null) {
            return response()->json([
                "message" => "Announcement Loaded",
                "status" => 1,
                "data" => $announcement
            ]);
        } else {
            return response()->json([
                "message" => "No Announcement Found",
                "status" => 0,
                "data" => null
            ]);
        }
    }

    public function sendNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'is_announcement' => 'required|boolean',
            'img_url' => 'nullable|image',
            'android_route' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 0
            ]);
        }

        $title = $request->title;
        $body = $request->body;
        $is_announcement = $request->is_announcement;
        $img_url = $request->file('img_url') ? $request->file('img_url')->store('notifications','public') : null;
        $android_route = $request->android_route;


        sendPublicNotification(new PublicNotification([
            'title' => $title,
            'body' => $body,
            'topic' => "all",
            'is_announcement'=>$is_announcement,
            'img_url' => $img_url,
            'android_route' => $android_route
        ]));
        return response()->json([
            'message' => 'Notification Sent',
            'status' => 1
        ]);
    }

}
