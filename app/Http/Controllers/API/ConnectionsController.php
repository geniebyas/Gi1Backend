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
        if (!$user->hasSentFriendRequest($dest_uid) && !$user->isFriendWith($dest_uid)) {
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


    public function deleteRequest(Request $request,$dest_uid){
        $source_uid = $request->header('uid');
        $connection = UsersConnection::where('source_uid',$source_uid)->where("dest_uid",$dest_uid)->get()->first();
        if(is_null($connection)){
            return response()->json(
                [
                    'message' => 'You\'r not connected',
                    'status' => 0,
                    'data' => null
                ],
                400
                );

        }else{
            $connection->delete();
            return response()->json(
                [
                    'message' => 'Removed from your connection',
                    'status' => 1,
                    'data' => 'Removed from your connection'
                ],
                200
            );
        }

    }

    function getUserWithDetails(Request $request,$uid){
        $source_uid = $request->header('uid');
        if(User::find($uid)->exists()){
            $user = User::where('uid',$uid)
            ->with("wallet")
            ->with("settings")
            ->with('responses.question.category')
            ->with('transactions.action')
            ->with(["connections.destUser" => function ($query) {
                $query->withCount('connections', 'connectors');
            }])
            ->with(["connectors.sourceUser" => function ($query) {
                $query->withCount('connections', 'connectors');
            }])
            
            ->get()
            ->first();
            $user->is_in_connections = UsersConnection::where("source_uid",$source_uid)->where("dest_uid",$uid)->where('status','accepted')->exists();
            $user->is_in_connectors = UsersConnection::where("dest_uid",$source_uid)->where("source_uid",$uid)->where('status','accepted')->exists();
            $user->connectors_count= $user->connectorsCount();
            $user->connections_count= $user->connectionsCount();
            $user->is_pending_request = UsersConnection::where("source_uid",$source_uid)->where("dest_uid",$uid)->where('status','pending')->exists();
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


    public function getPendingRequest(Request $request){
        $uid = $request->header('uid');

        $requests = UsersConnection::where('dest_uid',$uid)
        ->with(["sourceUser" => function ($query) {
            $query->withCount('connections', 'connectors');
        }])
        ->get();

        if($requests->count() > 0){
            return response()->json(
                [
                    'message' => 'Pending Requests',
                    'status' => 1,
                    'data' => $requests
                ],
                200
            );
        }else{
            return response()->json(
                [
                    'message' => 'Pending Requests',
                    'status' => 0,
                    'data' => "No Requests"
                ],
                200
            );
        }

    }

    public function updateRequest($id,$status){
        $connection = UsersConnection::find($id);
        if($status == "accepted"){
            $connection->status = $status;
            return response()->json([
                'message' => 'Request accepted successfully',
                'status' => 1,
                'data' => "Request accepted successfully"
            ],200);
        }else{
            $connection->delete();
            return response()->json([
                'message' => 'Request rejected successfully',
                'status' => 0,
                'data' => "Request rejected successfully"
            ],200);
        }
    }

}
