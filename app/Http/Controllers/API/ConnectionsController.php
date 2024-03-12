<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersConnection;
use Illuminate\Http\Request;

class ConnectionsController extends Controller
{
    function getUserConnections(Request $request){
        $uid = $request->header('uid');
        $user = User::with('connections')->where('uid',$uid)->first();

        return response()->json(
            [
                'message'=> 'User loaded',
                'status' => 1,
                'data' =>$user
            ],
            200
        );
    }

    public function sendFriendRequest(Request $request,$dest_uid)
    {
        $user = User::where('uid',$request->header('uid'))->first();
        $dest_user = User::where('uid',$dest_uid)->first();
   
        $status = "pending";
        $setting = $dest_user->getSettings();
        if($setting->is_private){
            $status = "pending";
        }else{
            $status = "accepted";
        }

        // Check if a request already exists
        if (!$user->hasSentFriendRequest($dest_uid) && !$user->hasPendingFriendRequest($dest_uid) && !$user->isFriendWith($dest_uid)) {
            UsersConnection::create([
                'source_uid' => $user->uid,
                'dest_uid' => $dest_uid,
                'status' => $status,
            ]);

            return response()->json(['message' => 'Friend request sent.',
            'status' =>'1',
            'data' => "success"
        ], 200);
        }

        return response()->json(['message' => 'Unable to send friend request.',
        'status' => 0,
        'data' => "unable to send friend request"
    ], 400);
    }

}
