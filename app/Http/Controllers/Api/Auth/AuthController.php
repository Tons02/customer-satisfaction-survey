<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Validation\ValidationException;

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

    public function resetPassword(Request $request, $id){
       return $user = User::where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'status_code' => "404",
                'message' => "User not found"
                ], 404);
        }
        
        $user->update([
            'password' => $user->username,
        ]);
        return response()->json(['message' => 'Password has been Reset!'], 200);
    }

    public function changedPassword(ChangePasswordRequest $request){

        $user = auth('sanctum')->user();
        $username = $user->username;
    
        if($username === $request->new_password){
            return response()->json(['message' => 'The new password must be different from your username.'], 422);
        }

        if (! $user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
    
        return response()->json(['message' => 'Password Successfully Changed!!'], 200);
    }

}
