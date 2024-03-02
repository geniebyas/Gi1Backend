<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UsersSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    //

    public function isvalid($code) {
        $refer_code = $code;

        if(UsersSetting::where("refer_code",$refer_code)->exists()){
            $response = [
                'message' => 'User Found',
                'status' => 1,
                'data' => UsersSetting::where("refer_code",$refer_code)->first()
            ];
        } else {
            $response = [
                'message' => 'Enter Valid Refer Code',
                'status' => 0,
                'data' => "Enter Valid Refer Code" . $refer_code
            ];
        }
        return response()->json($response,200);


    }

}
