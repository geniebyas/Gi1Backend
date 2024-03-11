<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    //
    function globalSearch($query){
        $users = User::where('name','LIKE',"%$query%")->orWhere('username','LIKE',"%$query%")->orWhere('email','LIKE',"%$query%");


        if($users->count() > 0){
            $response = [
                'message' => 'Result Found',
                'status' => 1,
                'data' =>$users
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
