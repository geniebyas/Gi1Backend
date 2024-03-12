<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    //
    function globalSearch($query,$filter){

        $users = User::with('settings')->where(function (Builder $queryBuilder) use ($query) {
            $queryBuilder
                ->where('name', 'LIKE', "%$query%")
                ->orWhere('username', 'LIKE', "%$query%")
                ->orWhere('email', 'LIKE', "%$query%");
        })
        ->get();

        $industries = Industry::where(function (Builder $queryBuilder) use ($query) {
            $queryBuilder
                ->where('name', 'LIKE', "%$query%")
                ->orWhere('description', 'LIKE', "%$query%");
        })
        ->get();

        if($users->count() > 0){
            $response = [
                'message' => 'Result Found',
                'status' => 1,
                'data' =>[
                    'users'=>$users,
                    'industries' =>$industries
                ]
                ];
                return response()->json($response,200);
        }else{
            $response = [
                'message' => 'Result Not Found',
                'status' => 0,
                'data' => [
                    'users' => null
                ]
                ];
                
                return response()->json($response,404);
        }
    }
}
