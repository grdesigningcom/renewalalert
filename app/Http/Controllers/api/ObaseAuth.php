<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class ObaseAuth extends Controller
{
    public function createAccount(Request $request){

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email'=> 'required|email|unique:users',
            'password' => 'required|min:8',
            'confirm_password'=>'required|same:password'
        ]);

        if($validator->fails()){
            $response = [
                'success'=>false,
                'message'=>'Wrong params',
                'errors'=>$validator->errors()
            ];

            return response()->json($response, 401);
        }
        
        $requested_data = request()->all();
        $requested_data['password'] = Hash::make($requested_data['password']);
        $user = User::create($requested_data);

        $success['token'] = $user->createToken(time())->plainTextToken;
        $success['user'] = $user->makeHidden('id','updated_at');

        $response = [
            'success' => true,
            'message' => 'Account has been created successfully',
            'data' => $success
        ];

        return response()->json($response, 200);
        
    }

    public function login(Request $request){

    }
}
