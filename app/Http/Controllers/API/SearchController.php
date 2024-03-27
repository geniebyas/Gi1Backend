<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    //
    function globalSearch(Request $request,$query, $filter)
    {

        $users = User::
        where('phone',"!=",null)
        ->where('uid','!=',$request->header('uid'))
        ->where(function (Builder $queryBuilder) use ($query) {
                $queryBuilder
                    ->where('name', 'LIKE', "%$query%")
                    ->orWhere('username', 'LIKE', "%$query%")
                    ->orWhere('email', 'LIKE', "%$query%");
            })
            ->withCount('connections', 'connectors')
            ->get();
    
        $industries = Industry::where(function (Builder $queryBuilder) use ($query) {
            $queryBuilder
                ->where('name', 'LIKE', "%$query%")
                ->orWhere('description', 'LIKE', "%$query%");
        })
        ->where("status",true)
            ->get();

        if ($users->count() > 0 || $industries->count() > 0){
            $response = [
                'message' => 'Result Found',
                'status' => 1,
                'data' => [
                    'users' => $users,
                    'industries' => $industries
                ]
            ];
            return response()->json($response, 200);
        } else {
            $response = [

                'message' => 'Result Not Found',
                'status' => 0,
                'data' => [
                    'users' => null,
                    'industries'=>null
                ]
            ];

            return response()->json($response, 400);
        }
    }

    function getExplore() {
        // Retrieve 4 random users
        $users = User::withCount('connectors')->inRandomOrder()->limit(5)->get();
    
        // Retrieve 2 random industries
        $industries = Industry::where("status",true)->inRandomOrder()->limit(3)->get();
    
        return response()->json([
            'message' => "Explorer Loaded",
            'status' => 1,
            'data' => [
                'users' => $users,
                'industries' => $industries
            ]
        ]);
    }
}
