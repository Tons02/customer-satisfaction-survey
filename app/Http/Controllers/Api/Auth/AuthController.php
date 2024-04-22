<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use App\Response\Message;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgetPasswordRequest;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request){
        
        $username=$request->username;
        $password=$request->password;
        
        $login=User::with('role')->where('username',$username)->get()->first();

        if (! $login || ! Hash::check($password, $login->password)) {
            return GlobalFunction::denied(Message::LOGIN_FAILED);
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
        
        auth('sanctum')->user()->currentAccessToken()->delete();
        return GlobalFunction::denied(Message::LOGOUT_USER);

    }

    public function resetPassword(Request $request, $id){
        $user = User::where('id', $id)->first();

        if (!$user) {
            return GlobalFunction::response_function(Message::INVALID_ID);
        }
        
        $user->update([
            'password' => $user->username,
        ]);
        
        return GlobalFunction::response_function(Message::RESET_PASSWORD);
    }

    public function changedPassword(ChangePasswordRequest $request){

        $user = auth('sanctum')->user();
        $username = $user->username;

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
    
        return GlobalFunction::response_function(Message::CHANGE_PASSWORD);
    }

    public function forgetPassword(ForgetPasswordRequest $request, $mobileNumber){

    
        $id = User::where('contact_details', $mobileNumber)->first();

        if (!$id) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        
        $id->update([
            'password' => Hash::make($request->new_password),
        ]);
    
        return GlobalFunction::response_function(Message::CHANGE_PASSWORD);
    }

}
