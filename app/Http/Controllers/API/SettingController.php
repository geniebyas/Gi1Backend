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

            $setting = UsersSetting::where("refer_code",$refer_code)->first();
            $user = User::where("uid",$setting->uid)->first();

            $response = [
                'message' => 'User Found',
                'status' => 1,
                'data' => [
                    "setting"=>$setting,
                    "user"=>$user
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

    public function updateSetting(Request $request){
        $setting = UsersSetting::where('uid',$request->header('uid'))->get()->first();

        if($setting->referred_by == null){
            if($request->referred_by != null){
                addCoins($request->header('uid'),5);
                addCoins($request->referred_by,4);
            }
        }

        $setting->is_private = boolval($request->is_private);
        $setting->referred_by = $request->referred_by;
        //More settings need to be implemented
        $res = $setting->update();



        return response()->json([
            'message' => "Settings Updated",
            'status' => 1,
            'data' => $setting
        ]);
    }




}
