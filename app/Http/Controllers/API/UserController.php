<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersSetting;
use GuzzleHttp\Client;
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

    public function getAllPublicUsers()
    {
        $uids = UsersSetting::where('is_private', false)->get();
        $users = array();
        if (!$uids->isEmpty()) {
            foreach ($uids as $uid) {
                $user = User::where('uid', $uid->uid)->first();
                $user->refer_code = $uid->refer_code;
                $users[] = $user;
            }
        }
        if (count($users) > 0) {
            //users exists
            $response = [
                'message' => count($users) . ' users found',
                'status' => 1,
                'data' => $users
            ];

            return response()->json($response, 200);
        } else {
            $response = [
                'message' => count($users) . ' users found',
                'status' => 0,
                'data' => null
            ];
            return response()->json($response, 204);
        }
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
        return response()->json($response, 200);
    }

    public function isuniqueuser(Request $request, $username)
    {
        if (User::where('username', $username)->exists()) {
            $response = [
                'message' => 'User Found',
                'status' => 1,
                'data' => false
            ];
        } else {
            $response = [
                'message' => 'User does not exist',
                'status' => 0,
                'data' => true
            ];
        }
        return response()->json($response, 200);
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
            'username' => ['required', 'unique:users,username'],
            'name' => 'required',
            'email' => ['required', 'email', 'unique:users,email'],
            'uid' => ['required', 'unique:users,uid']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {
            DB::beginTransaction();
            try {
                $ex = null;
                $request['password'] = Hash::make($request->password);
                $user = User::create([
                    'username' => $request->username,
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
                $ex = $th;
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
                        'data' => $ex->getMessage()
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
    public function show(Request $request)
    {

        $user = User::where('uid', $request->uid)->get()->first();
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
    public function update(Request $request)
    {
       
        $uid = $request->header('uid');
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:255',
            'dob' => 'required',
            'gender' => 'required|in:Male,Female',
            'city' => 'required|string|max:255',
            'bio' => 'nullable|string|max:255',
            'profile_pic' => 'nullable|string|max:255',
            'referred_by' => 'nullable|string|max:255', // Assuming referred_by is a string
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 0,
                'data' => $validator->errors()->first()
            ], 422);
        }
        $user = User::where('uid', $uid)->first();
     return response()->json(
            [
                'message' => p($user)
            ]
            );
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'status' => 0
            ], 404);
        }
    
        DB::beginTransaction();
        try {
            $user->phone = $request['phone'];
            $user->dob = $request['dob'];
            $user->gender = $request['gender'];
            $user->city = $request['city'];
            $user->bio = $request['bio'];
            $user->profile_pic = $request['profile_pic'];
            $user->update([
                'phone' => $request->phone,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'city' => $request->city,
                'bio' => $request->bio,
                'profile_pic' => $request->profile_pic
            ]);
    
            $setting = UsersSetting::firstOrNew(['uid' => $uid]);
            $setting->refer_code = generateReferCode();
            $setting->referred_by = $request->input('referred_by');
            $setting->save();
                addCoins($request->header('uid'),2);

            if ($setting->referred_by !== null) {
                addCoins($setting->referred_by, 4);
                addCoins($request->header('uid'), 5);
            }
    
            DB::commit();
    
            return response()->json([
                'message' => 'Registration Successfully',
                'status' => 1,
                'data' => ""
            ], 200);
        } catch (Throwable $th) {
            DB::rollback();
            return response()->json([
                'message' => $th->getMessage(),
                'status' => 0,
                'data' => ''
            ], 500);
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
