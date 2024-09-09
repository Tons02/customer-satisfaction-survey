<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Ichtrojan\Otp\Otp;
use App\Response\Message;
use App\Models\SurveyAnswer;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Requests\SmsRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\SmsForgetPasswordRequest;

class SmsController extends Controller
{
    
    public function sendverificationcode(SmsRequest $request){
    
        $otp = new Otp();
        $otpValue = $otp->generate($request->input('mobile_number'), 'numeric', 6, 5);
        
        $otpsms = $otpValue->token;

        $token = env('SMS_TOKEN');
        $sms_post = env('SMS_POST');
    
        $response = Http::withToken($token)->post($sms_post, [
                    'system_name' => 'Customer Service Satisfaction',
                    'message' => 'Fresh Morning, here is your OTP: ' . $otpsms . ' to proceed to your survey',
                    'mobile_number' => $request["mobile_number"]
        ]);


        return GlobalFunction::response_function(Message::SMS_OTP_SAVE);
    }

    public function sendverificationcoderesetpassword(SmsForgetPasswordRequest $request){
        $otp = new Otp();
        $otpValue = $otp->generate($request->input('mobile_number'), 'numeric', 6, 3);
        
        $otpsms = $otpValue->token;

        $token = env('SMS_TOKEN');
        $sms_post = env('SMS_POST');
    
        $response = Http::withToken($token)->post($sms_post, [
                    'system_name' => 'Customer Service Satisfaction',
                    'message' => 'Fresh Morning, here is your OTP: ' . $otpsms . ' to reset your password',
                    'mobile_number' => $request["mobile_number"]
        ]);


        return GlobalFunction::response_function(Message::SMS_OTP_SAVE);
    }
    

    public function validatecode(SmsRequest $request){
        
        $otp = new Otp();
        return $otpValue = $otp->validate($request->input('mobile_number'), $request->input('code'));
    
    }
}
