<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersConnection;
use Illuminate\Http\Request;

class LeaderBoardController extends Controller
{
    public function getLeaderBoard(Request $request)
{
    $uid = $request->header('uid');

    // Fetch the connections of the source user
    $source_user_connections = UsersConnection::where('source_uid', $uid)
        ->pluck("dest_uid")
        ->toArray();

    // Get users who have public profiles
    $users = User::whereHas('settings', function ($query) {
            $query->where('is_private', false);
        })
        ->withCount('connectors')
        ->withCount(['wallet as total_bal' => function ($query) {
            $query->select('total_bal');
        }])
        ->orderBy('created_at', 'desc') // Sort users by created_at timestamp in descending order
        ->get();

    // Calculate the total_bal for each user by adding a bonus for each connector
    foreach ($users as $user) {
        // Bonus calculation: Each connector contributes 5 to total_bal
        $user->total_bal += $user->connectors_count * 5;
    }

    // Sort users by total_bal in descending order
    $users = $users->sortByDesc('total_bal')->values()->all();

    // Limit the list to the top 50 users
    $top50_users = array_slice($users, 0, 50);

    // Add rank for each user in top 50
    $rank = 1;
    foreach ($top50_users as $userRank) {
        $userRank['rank'] = $rank++;
    }

    // Find mutual connections from the top 50 users
    $mutuals = [];
    foreach ($top50_users as $user) {
        if (in_array($user['uid'], $source_user_connections)) {
            $mutuals[] = $user;
        }
    }

    $primes = [];

    return response()->json([
        'message' => 'Successfully loaded',
        'status' => 1,
        'data' => [
            "top50" => $top50_users,
            "mutuals" => $mutuals,
            "primes" => $primes
        ]
    ]);
}

    

}
