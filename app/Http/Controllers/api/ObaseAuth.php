<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;

class ObaseAuth extends Controller
{
    public function failed(Request $request){

        $response = [
            'success' => false,
            'message' => 'Unauthorized request'
        ];

        return response()->json($response, 401);
    }

    public function signup(Request $request){

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email'=> 'required|email|unique:users',
            'password' => 'required|min:8',
            'confirm_password'=>'required|same:password'
        ]);

        if($validator->fails()){
            $response = [
                'success'=>false,
                'message'=>'The given parameters are incorrect.',
                'errors'=>$validator->errors()
            ];

            return response()->json($response, 400);
        }
        
        $requested_data = request()->all();
        $requested_data['password'] = Hash::make($requested_data['password']);
        $user = User::create($requested_data);

        $success['token'] = $user->createToken(time())->plainTextToken;
        $success['user'] = $user->makeHidden('id');

        $response = [
            'success' => true,
            'message' => 'Account has been created successfully',
            'data' => $success
        ];

        return response()->json($response, 200);
        
    }

    public function signin(Request $request){
        
        $validator = Validator::make($request->all(),[
            'email'=> 'required|email|exists:users',
            'password' => 'required|min:8',
        ]);

        if($validator->fails()){
            $response = [
                'success'=>false,
                'message'=>'The given parameters are incorrect.',
                'errors'=>$validator->errors()
            ];

            return response()->json($response, 400);

        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){

            $user = User::find(Auth::user()->id);
            $success['token'] = $user->createToken(time())->plainTextToken;
            $success['user'] = $user->makeHidden(['id','email_verified_at']);

            $response = [
                'success' => true,
                'message' => "Logged in successfully.",
                'data' => $success
            ];

            return response()->json($response, 200);

        }else{

            $response = [
                'success' => false,
                'message' => "The given parameters are incorrect.",
                'errors' => [
                    'password' => [
                        'The selected password is invalid.'
                    ]
                ]
            ];

            return response()->json($response, 409);
            
        }

    }

    public function logout(Request $request){

        $validator = Validator::make($request->all(),[
            'email'=> 'required|email|exists:users'
        ]);

        if($validator->fails()){
            $response = [
                'success'=>false,
                'message'=>'The given parameters are incorrect.',
                'errors'=>$validator->errors()
            ];

            return response()->json($response, 400);

        }

        $user = User::where('email',$request->email)->get();
        
        $request->user()->currentAccessToken()->delete();

        $response = [
            'success' => true,
            'message' => "Logged out successfully."
        ];

        return response()->json($response, 200);
        
    }

}
