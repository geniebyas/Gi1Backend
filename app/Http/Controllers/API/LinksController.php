<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UsersLinks;
use Dotenv\Validator;
use Exception;
use Illuminate\Http\Request;

class LinksController extends Controller
{
    public function addLink($request){

        $validator = Validator::make($request->all(),[
           'link'=>['required'],
           'title'=>['required']
        ]);
        if($validator->failes()){
            return response()->json($validator->messages(),400);
        }else{
            try{
                $link = UsersLinks::create([
                    'uid'=>$request->header('uid'),
                    'link'=>$request->link,
                    'title'=>$request->title,
                    'clicks'=>0
                ]);
                if($link != null){
                    return response()->json([
                        "message"=>"Link created Successfully",
                        "status"=>1,
                        "data"=>"Success"
                    ]);
                }else{
                    throw new Exception("Link Creation Failed");
                }
            }catch (Exception $ex){
                return response()->json(
                    [
                        'message' => $ex->getMessage(),
                        'status' => 0,
                        'data' => $ex->getMessage()
                    ],
                    500
                );
            }
        }


    }

    public function updateLink($request){

    }
}
