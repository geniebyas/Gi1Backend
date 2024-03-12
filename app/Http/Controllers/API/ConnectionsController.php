<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ConnectionsController extends Controller
{
    function getUserConnections(Request $request){
        $uid = $request->header('uid');
        $user = User::with('connections')->where('uid',$uid)->first();



    }
}
