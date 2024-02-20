<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Coins;
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
    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'uid' => 'required',
            'type' => ['required', 'in:add,remove'],
            'amount' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {
            DB::beginTransaction();
            $coin = null;
            try {
                $coin = new Coins();
                $coin->uid = $request->uid;
                $coin->type = $request->type;
                $coin->amount = $request->amount;
                $coin->save();
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                $coin = null;
            }
            if($coin != null){
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
            }else{
                return response()->json(
                    [
                        'message' => "Transaction Failed",
                        'status' => 500,
                        'data' => "error"
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
