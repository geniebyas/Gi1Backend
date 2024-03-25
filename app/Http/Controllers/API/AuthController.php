<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        //validation
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->messages()
            ];
            return response()->json($response, 400);
        }

        //create user
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $success['token'] = $user->createToken('Gi1InfoApp')->accessToken;
        $success['name'] = $user->name;

        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'User Registered Successfully'
        ];
        return $response()->json($response,200);
    }

    public function login(Request $req){
        if(Auth::attempt(['email' => $req->email,'password' => $req->password])){
            $user = Auth::user();

        // $success['token'] = $user->createToken('Gi1InfoApp')->accessToken;
        $success['name'] = $user->name;

        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'User Login Successfully'
        ];
        return $response()->json($response,200);
        }else{
            $response = [
                'success' => false,
                'message' => "Unauthorised"
            ];
            return $response()->json($response);
        }
    }
}
