<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        public function index(){
            
    }

    public function allActiveIndustries(){
        $list = Industry::get();
        if (count($list) > 0) {
            //users exists
            $response = [
                'message' => count($list) . ' industries found',
                'status' => 1,
                'data' => $list
            ];
        } else {
            $response = [
                'message' => count($list) . ' industries found',
                'status' => 1,
                'data' => null
            ];
        }
        return response()->json($response,200);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $uid = $request->header('uid');
        $industry = new Industry();
        $client = new Client();

        $resp = $client->request('POST', "https://api.gi1superapp.com/api/file/upload", [
            'headers' => [
                'uid' => $uid
            ],
            'multipart' => [
                'dir' => $request->dir,
                'name' => $request->name,
                'contents' => Psr7\Utils::tryFopen($request->file($request->name)->path(),'r')
            ]
        ]);

        return response()->json([
            'message' => $resp
        ]);




        // if(!is_null($industry))
        // DB::beginTransaction();
        //     try {
        //         // Industry::create($industry);
        //         DB::commit();
        //     } catch (\Throwable $th) {
        //         //throw $th;
        //         DB::rollBack();
        //         $industry = null;
        //         $e = $th;
        //     }
        //     if ($industry != null) {
        //         return response()->json(
        //             [
        //                 "message" => "Industry Added successfully",
        //                 "status" => 1,
        //                 "data" => $industry
        //             ],
        //             200
        //         );
        //     } else {
        //         return response()->json(
        //             [
        //                 'message' => "Error Occured" . $e->getMessage() ,
        //                 'status' => 0,
        //                 'data' =>$request->all()
        //             ],
        //             500
        //         );
        //     }
        p($request->all());

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
