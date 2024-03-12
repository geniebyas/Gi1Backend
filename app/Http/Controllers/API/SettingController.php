<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class SettingController extends Controller
{
    //

    public function isvalid($refer_code) {
    
        if(UsersSetting::where("refer_code",$refer_code)->exists()){

            $setting = UsersSetting::with('user')->where("refer_code",$refer_code)->first();

            $response = [
                'message' => 'User Found',
                'status' => 1,
                'data' => [
                    "setting"=>$setting
                ]
            ];
                    return response()->json($response,200);

        } else {
            $response = [
                'message' => 'Enter Valid Refer Code',
                'status' => 0,
                'data' => "Enter Valid Refer Code" . $refer_code
            ];
            return response()->json($response,400);
        }


    }


  

    public function add_setting(Request $request){
        $setting = new UsersSetting();
        $setting->uid = $request->header('uid');
        $setting->refer_code = generateReferCode();
        $setting->save();

        return response()->json(
            [
                'message'=>'Settings Added Successfully',
                'status'=>1,
                'data'=>$setting

            ],200
        );
    }

}
