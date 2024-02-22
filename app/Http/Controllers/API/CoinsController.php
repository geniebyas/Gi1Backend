<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Coins;
use App\Models\CoinsActions;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CoinsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */


     public function getCoinsDetailsForUser(Request $request){
        $user = User::where("uid",$request->header("uid"))->first();
        $trans = Coins::where("uid",$request->header("uid"))->get();
        $wallet = UserWallet::where("uid",$request->header("uid"))->first();
        foreach($trans as $t){
            $t->action = CoinsActions::find($t->action_id);
        }
        if($wallet == null){
            $wallet = new UserWallet();
            $wallet->total_bal = 0.0;
        }
        if($user != null){
                return response()->json(
                    [
                        "message" => "Success",
                        "status" => 1,
                        "data" => [
                            "user"=>$user,
                            "wallet"=>$wallet,
                            "transactions"=>$trans
                    ]
                        ],
                    200
                );
        }else{
            return response()->json(["message"=>"User Not Found","status"=>0,"data"=>null],404);
        }

     }


    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'uid' => 'required',
            'type' => ['required', 'in:add,remove'],
            'action_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {
            DB::beginTransaction();
            $coin = null;
            try {
                $wallet = UserWallet::where("uid",$request->uid)->first();
                if($wallet == null){
                    $wallet = new UserWallet();
                    $wallet->total_bal = 0;
                }
                $action = CoinsActions::find($request->action_id);
                $coin = new Coins();
                $coin->uid = $request->uid;
                $coin->type = $request->type;
                $coin->action_id = $request->action_id;
                $coin->save();
                if($action != null){
                if($coin->type == "add"){
                    $wallet->total_bal = intval($wallet->total_bal) + $action->amount;
                }else{
                    $wallet->total_bal = intval($wallet->total_bal) - $action->amount;
                }
                UserWallet::updateOrCreate(['uid'=>$request->uid],['total_bal'=>$wallet->total_bal]);
            }
                DB::commit();
            if($coin->type == "add")  $msg = "Coins Added Successfully" ;
                else $msg = "Coins Deducted Successfully" ;

                return response()->json(
                    [
                        "message" => $msg,
                        "status" => 1,
                        "data" => "success"
                    ],
                    200
                );
            } catch (\Throwable $e) {
                DB::rollBack();
            return response()->json(
                    [
                        'message' => "Transaction Failed",
                        'status' => 0,
                        'data' => $e->getMessage()
                    ],
                    500
                );
            }

        }





        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
