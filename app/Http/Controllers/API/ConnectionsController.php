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
        $dest_user = User::where('uid',$dest_uid)
        ->with('settings')
        ->get()
        ->first();
   
        $status = "pending";
        $setting = $dest_user->settings;
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

    function getUserWithDetails(Request $request,$uid){
        $source_uid = $request->header('uid');
        if(User::find($uid)->exists()){
            $user = User::where('uid',$uid)
            ->with("wallet")
            ->with("settings")
            ->with('responses')
            ->with('transactions')
            ->with(["connections.destUser" => function ($query) {
                $query->withCount('connections', 'connectors');
            }])
            ->with(["connectors.sourceUser" => function ($query) {
                $query->withCount('connections', 'connectors');
            }])
            
            ->get()
            ->first();
            $user->is_in_connections = UsersConnection::where("source_uid",$source_uid)->exists();
            $user->is_in_connectors = UsersConnection::where("dest_uid",$source_uid)->exists();
            $user->connectors_count= $user->connectorsCount();
            $user->connections_count= $user->connectionsCount();
            return response()->json(
                [
                    'message' => "User Found",
                    'status' => 1,
                    'data' => $user
                ],
                200
            );

        }else{
            return response()->json(
                [
                    'message' => 'User not found',
                    'status'  => 0,
                    'data' => "No Data Found"
                ],
                402
                );
        }
    }

}
