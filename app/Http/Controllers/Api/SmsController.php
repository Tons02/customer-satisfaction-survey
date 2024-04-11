<?php

namespace App\Http\Controllers\Api;

use Ichtrojan\Otp\Otp;
use App\Response\Message;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Requests\SmsRequest;
use App\Http\Controllers\Controller;

class SmsController extends Controller
{
    
    public function sendverificationcode(SmsRequest $request){
        $otp = new Otp();
        $otpValue = $otp->generate($request->input('mobile_number'), 'numeric', 6, 10);
      
        return GlobalFunction::response_function(Message::SMS_OTP_SAVE, $otpValue);
    }
    

    public function validatecode(SmsRequest $request){
        
        $otp = new Otp();
        $otpValue = $otp->validate($request->input('mobile_number'), $request->input('code'));

        return GlobalFunction::response_function($otpValue);
    }
}
