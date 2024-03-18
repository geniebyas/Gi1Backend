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


    public function getCoinsDetailsForUser(Request $request)
    {
        $user = User::where("uid", $request->header("uid"))->first();
        $trans = Coins::where("uid", $request->header("uid"))->get();
        $wallet = UserWallet::where("uid", $request->header("uid"))->first();
        foreach ($trans as $t) {
            $t->action = CoinsActions::find($t->action_id);
        }
        if ($wallet == null) {
            $wallet = new UserWallet();
            $wallet->total_bal = 0.0;
        }

        $data = User::where('uid',$request->header('uid'))
        ->with('wallet')
        ->with('transactions.action')
        ->get()
        ->first();



        if ($data != null) {
            return response()->json(
                [
                    "message" => "Success",
                    "status" => 1,
                    "data" => $data
                ],
                200
            );
        } else {
            return response()->json(["message" => "User Not Found", "status" => 0, "data" => null], 404);
        }
    }


    
public function create(Request $request)
{
    try {
    $uid = $request->header('uid');

    $validator = Validator::make($request->all(), [
        'type' => ['required', 'in:add,remove'],
        'action_id' => 'required|exists:coins_actions_mst,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => $validator->errors()->first(),
            'status' => 0
        ], 400);
    }

    DB::beginTransaction();
    
        // Retrieve or create user wallet
        $wallet = UserWallet::firstOrCreate(['uid' => $uid], ['total_bal' => 0]);

        // Retrieve the coins action
        $action = CoinsActions::findOrFail($request->action_id);

        // Create a new coins record
        $coin = new Coins();
        $coin->uid = $uid;
        $coin->type = $request->type;
        $coin->action_id = $request->action_id;
        $coin->amount = $action->amount;
        $coin->save();

        // Update wallet balance based on the type of action
        $amount = ($coin->type === 'add') ? $action->amount : -$action->amount;
        $wallet->increment('total_bal', $amount);

        DB::commit();

        $msg = ($coin->type === 'add') ? 'Coins Added Successfully' : 'Coins Deducted Successfully';

        return response()->json([
            'message' => $msg,
            'status' => 1,
            'data' => 'success'
        ], 200);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Transaction Failed',
            'status' => 0,
            'data' => $e->getMessage()
        ], 500);
    }
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
