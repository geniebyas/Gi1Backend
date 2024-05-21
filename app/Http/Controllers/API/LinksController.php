<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UsersLinks;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LinksController extends Controller
{
    public function addLink(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'link' => ['required'],
            'title' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {
            try {
                $link = UsersLinks::create([
                    'uid' => $request->header('uid'),
                    'link' => $request->link,
                    'title' => $request->title,
                    'clicks' => 0
                ]);
                if ($link != null) {
                    return response()->json([
                        "message" => "Link created Successfully",
                        "status" => 1,
                        "data" => "Success"
                    ]);
                } else {
                    throw new Exception("Link Creation Failed");
                }
            } catch (Exception $ex) {
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

    public function registerLinkClick(int $id)
    {
        $link = UsersLinks::find($id);
        if ($link != null) {
            $link->clicks += 1;
            $link->update();
            return response()->json([
                "message" => "Click Registered",
                "status" => 1,
                "data" => "Success"
            ]);
        } else {

            return response()->json([
                "message" => "Failed",
                "status" => 0,
                "data" => "Failed"
            ]);
        }
    }

    public function deleteLink(int $id)
    {
        $link = UsersLinks::find($id);
        if ($link != null) {
            $link->delete();
            return response()->json([
                "message" => "Deleted Link",
                "status" => 1,
                "data" => "Success"
            ]);
        } else {
            return response()->json([
                "message" => "Failed",
                "status" => 0,
                "data" => "Failed"
            ]);
        }
    }


    public function updateLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'link' => ['required'],
            'title' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {
            try {
                $link = UsersLinks::find($request->id);
                if($link != null) {
                    $link->title = $request->title;
                    $link->link = $request->link;
                    $link->update();
                    return response()->json([
                        "message" => "Link Updated Successfully",
                        "status" => 1,
                        "data" => "Updated Link"
                    ]);
                }else{
                    return response()->json([
                        "message"=>"Failed",
                        "status"=>0,
                        "data"=>"Failed"
                    ]);
                }
            } catch (Exception $ex) {
                return response()->json([
                    "message"=>$ex->getMessage(),
                    "status"=>1,
                    "data"=>"Updated Link"
                ]);
            }
        }


    }
}
