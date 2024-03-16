<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderBoardController extends Controller
{
    public function getLeaderBoard() {
        $users = User::
        whereHas('settings', function ($query) {
            $query->where('is_private', false);
        })
        ->withCount("connectors")
        ->withCount(['wallet as total_bal' => function ($query) {
            $query->select('total_bal');
        }])
        ->get();

        foreach($users as $u){
            $u->total_bal += $u->connectors_count * 5; // Add bonus for each connector
        }

        return response()->json(
            [
                'message' => "Successfully loaded",
                'status' => 1,
                'data' => $users
            ]
            );
    }
}
