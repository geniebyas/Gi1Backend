<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PersonalNotification;
use App\Models\User;
use App\Models\UsersConnection;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ConnectionsController extends Controller
{
    function getUserConnections(Request $request)
    {
        $uid = $request->header('uid');
        $user = User::with('connections')->where('uid', $uid)->first();

        return response()->json(
            [
                'message' => 'User loaded',
                'status' => 1,
                'data' => $user
            ],
            200
        );
    }

    public function sendFriendRequest(Request $request, $dest_uid)
    {
        $user = User::where('uid', $request->header('uid'))->first();
        $dest_user = User::where('uid', $dest_uid)
            ->with('settings')
            ->get()
            ->first();

        $status = "pending";
        $setting = $dest_user->settings;
        if ($setting->is_private) {
            $status = "pending";
            sendPersonalNotification(new PersonalNotification([
                "sender_uid"=>$user->uid,
                "reciever_uid"=>$dest_uid,
                "title"=>"new Connection Request",
                "body" => "$user->username sent you a connection request",
            ]));
        } else {
            $status = "accepted";
            sendPersonalNotification(new PersonalNotification([
                "sender_uid"=>$user->uid,
                "reciever_uid"=>$dest_uid,
                "title"=>"new Connection",
                "body" => "$user->username connected with you",
            ]));
        }

        // Check if a request already exists
        if (!$user->hasSentFriendRequest($dest_uid) && !$user->isFriendWith($dest_uid)) {
            UsersConnection::create([
                'source_uid' => $user->uid,
                'dest_uid' => $dest_uid,
                'status' => $status,
            ]);

            return response()->json([
                'message' => 'Friend request sent.',
                'status' => '1',
                'data' => "success"
            ], 200);
        }

        return response()->json([
            'message' => 'Unable to send friend request.',
            'status' => 0,
            'data' => "unable to send friend request"
        ], 400);
    }


    public function deleteRequest(Request $request, $dest_uid)
    {
        $source_uid = $request->header('uid');
        $connection = UsersConnection::where('source_uid', $source_uid)->where("dest_uid", $dest_uid)->get()->first();
        if (is_null($connection)) {
            return response()->json(
                [
                    'message' => 'You\'r not connected',
                    'status' => 0,
                    'data' => null
                ],
                400
            );
        } else {
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

    function getUserWithDetails(Request $request, $uid)
    {
        $source_uid = $request->header('uid');
        if (User::find($uid)->exists()) {
            $user = User::where('uid', $uid)
                ->with("wallet")
                ->with(["settings.referrer" => function ($query){
                    $query->withCount('connectors');
                }])
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
            $user->is_in_connections = UsersConnection::where("source_uid", $source_uid)->where("dest_uid", $uid)->where('status', 'accepted')->exists();
            $user->is_in_connectors = UsersConnection::where("dest_uid", $source_uid)->where("source_uid", $uid)->where('status', 'accepted')->exists();
            $user->connectors_count = $user->connectorsCount();
            $user->connections_count = $user->connectionsCount();
            $user->is_pending_request = UsersConnection::where("source_uid", $source_uid)->where("dest_uid", $uid)->where('status', 'pending')->exists();
            return response()->json(
                [
                    'message' => "User Found",
                    'status' => 1,
                    'data' => $user
                ],
                200
            );
        } else {
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


    public function getPendingRequest(Request $request)
    {
        $uid = $request->header('uid');

        $requests = UsersConnection::where('dest_uid', $uid)
            ->with(["sourceUser" => function ($query) {
                $query->withCount('connections', 'connectors');
            }])
            ->get();

        if ($requests->count() > 0) {
            return response()->json(
                [
                    'message' => 'Pending Requests',
                    'status' => 1,
                    'data' => $requests
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'message' => 'Pending Requests',
                    'status' => 0,
                    'data' => "No Requests"
                ],
                400
            );
        }
    }

    public function updateRequest($id, $status)
    {
        $connection = UsersConnection::with('sourceUser')->find($id);
        if ($status == "accepted") {
            $connection->status = $status;
            $connection->update();
            return response()->json([
                'message' => 'Request accepted successfully',
                'status' => 1,
                'data' => "Request accepted successfully"
            ], 200);
            sendPersonalNotification(new PersonalNotification([
                "sender_uid" => $connection->dest_uid,
                "reciever_uid" => $connection->source_uid,
                "title" => "Request Accepted",
                "body"=> $connection->source_user->username . " accepted your connection request",
            ]));
        } else {
            $connection->delete();
            return response()->json([
                'message' => 'Request rejected successfully',
                'status' => 0,
                'data' => "Request rejected successfully"
            ], 200);
        }
    }

    public function getUserRelation(Request $request, $dest_uid)
    {
        $source_uid = $request->header('uid');

        $dest_user = User::where("uid", $dest_uid)
            ->with(["connections.destUser" => function ($query) {
                $query->withCount('connections', 'connectors');
            }])
            ->with(["connectors.sourceUser" => function ($query) {
                $query->withCount('connections', 'connectors');
            }])
            ->first();

        $dest_user->mutuals = $this->getMutualConnections($source_uid, $dest_uid);

        return response()->json([
            'message' => 'User Relations',
            'status' => 1,
            'data' => $dest_user
        ]);
    }

    public function getMutualConnections($sourceUid, $destUid)
    {
        // Retrieve connections for the source user
        $sourceConnections = UsersConnection::where('source_uid', $sourceUid)
            ->where('status', 'accepted')
            ->pluck('dest_uid')
            ->toArray();

        // Retrieve connections for the destination user
        $destConnections = UsersConnection::where('source_uid', $destUid)
            ->where('status', 'accepted')
            ->pluck('dest_uid')
            ->toArray();

        // Find the intersection of connections
        $mutualConnections = array_intersect($sourceConnections, $destConnections);

        $mutuals = [];

        foreach($mutualConnections as $m){
            $mutuals[] = User::where("uid",$m)
            ->withCount('connectors')
            ->withCount('connections')
            ->get()
            ->first();
        }

        return $mutuals;
    }
}
