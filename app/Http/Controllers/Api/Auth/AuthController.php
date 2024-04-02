<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    public function login(Request $request){
        
        $username=$request->username;
        $password=$request->password;
        
        $login=User::with('role')->where('username',$username)->get()->first();

        if (! $login || ! Hash::check($password, $login->password)) {
            throw ValidationException::withMessages([
                'message' => ['The provided credentials are incorrect.']
            ])->status(404);
        }
        $token = $login->createToken('myapptoken')->plainTextToken;
        $cookie = cookie('authcookie',$token);

        return response()->json([
            'message' => 'Successfully Logged In',
            'token' => $token,
            'data' => $login ,
            
        ], 200)->withCookie($cookie);
        
    }

    public function Logout(Request $request){
        auth('sanctum')->user()->currentAccessToken()->delete();//logout currentAccessToken
        return response()->json(['message' => 'You are Successfully Logged Out!']);
    }
}
