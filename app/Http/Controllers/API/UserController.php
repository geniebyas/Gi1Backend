<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function getAllUsers()
    {
        $users = User::select('id', 'email', 'name', 'uid')->get();
        if (count($users) > 0) {
            //users exists
            $response = [
                'message' => count($users) . ' users found',
                'status' => 1,
                'data' => $users
            ];
        } else {
            $response = [
                'message' => count($users) . ' users found',
                'status' => 1,
                'data' => null
            ];
        }
        return response()->json($response);
    }
    public function index()
    {
    }


    public function checkUserExists($uid)
    {
        if (User::where('uid', $uid)->exists()) {
            $response = [
                'message' => 'User Found',
                'status' => 1,
                'data' => User::where('uid', $uid)->get()->first()
            ];
        } else {
            $response = [
                'message' => 'User does not exist',
                'status' => 0,
                'data' => null
            ];
        }
        return response()->json($response,200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => ['required', 'email', 'unique:users,email'],
            'uid' => ['required', 'unique:users,uid']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {
            DB::beginTransaction();
            try {
                $request['password'] = Hash::make($request->password);
                $user = User::create([
                    'email' => $request->email,
                    'password' => $request->password,
                    'name' => $request->name,
                    'uid' => $request->uid
                ]);
                DB::commit();
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack();
                $user = null;
            }
            if ($user != null) {
                return response()->json(
                    [
                        "message" => "Signup successfully",
                        "status" => 1,
                        "data" => "success"
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'message' => "Signup failed",
                        'status' => 0,
                        'data' => "error"
                    ],
                    500
                );
            }
        }
        p($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                'message' => 'User Not Found',
                'status' => 0,
                'data'  => null,
            ];
        } else {
            $response = [
                'message' => 'User Found',
                'status' => 1,
                'data' => $user
            ];
        }
        return response()->json($response, 200);
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
    public function update(Request $request, string $uid)
    {
        //
        $user = User::where('uid',$uid)->get()->first();
        if (is_null($user)) {
            return response()->json(
                [
                    'message' => 'User not found',
                    'status' => 0
                ],
                404
            );
        } else {
            DB::beginTransaction();
            try {
                $user->phone = $request['phone'];
                $user->dob = $request['dob'];
                $user->gender = $request['gender'];
                $user->city = $request['city'];
                $user->bio = $request['bio'];
                $user->save();
                DB::commit();

                $response = [
                    'message' => 'Registration Successfully',
                    'status' => 1,
                    'data' =>""
                ];
            } catch (Throwable $th) {
                DB::rollback();
                $response = [
                    'message' => $th->getMessage(),
                    'status' => 0,
                    'data' =>""
                ];
            }
            return response()->json($response, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                'message' => "User Does'nt Exist",
                'status' => 0,
            ];
            $responseCode = 404;
        } else {
            DB::beginTransaction();
            try {

                $user->delete();
                DB::commit();
                $response = [
                    'message' => "User Deleted Successfully",
                    'status' => 1
                ];

                $responseCode = 200;
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack();
                $response = [
                    'message' => "Internal Server Error",
                    'status' => 1
                ];

                $responseCode = 500;
            }
        }
        return response()->json($response, $responseCode);
    }
}
