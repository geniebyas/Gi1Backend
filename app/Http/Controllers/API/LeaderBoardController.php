<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderBoardController extends Controller
{
    public function getLeaderBoard()
{
    // Get users who have public profiles
    $users = User::whereHas('settings', function ($query) {
            $query->where('is_private', false);
        })
        // Load the count of connectors for each user
        ->withCount('connectors')
        // Load the total_bal from the wallet relationship
        ->withCount(['wallet as total_bal' => function ($query) {
            $query->select('total_bal');
        }])
        ->get();

    // Calculate the total_bal for each user by adding a bonus for each connector
    foreach ($users as $user) {
        // Bonus calculation: Each connector contributes 5 to total_bal
        $user->total_bal += $user->connectors_count * 5;
    }

    // Sort users by total_bal in descending order
    $users = $users->sortBy('total_bal');

    // Limit the list to the top 50 users
    $users = $users->take(50);

    return response()->json([
        'message' => 'Successfully loaded',
        'status' => 1,
        'data' => $users
    ]);
}

}
