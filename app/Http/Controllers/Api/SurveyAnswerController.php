<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Ichtrojan\Otp\Otp;
use App\Response\Message;
use Illuminate\Support\Str;
use App\Models\SurveyAnswer;
use Illuminate\Http\Request;
use App\Models\VoucherValidity;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\SurveyAnswerRequest;
use App\Http\Resources\SurveyAnswerResource;

class SurveyAnswerController extends Controller
{
    use ApiResponse;
    
    public function index(Request $request)
    {   
        $status = $request->query('status');
        
        $SurveyAnswer = SurveyAnswer::
        when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
        ->orderBy('created_at', 'desc')
        ->useFilters()
        ->dynamicPaginate();
        
        $is_empty = $SurveyAnswer->isEmpty();

        if ($is_empty) {
            return GlobalFunction::response_function(Message::NOT_FOUND, $SurveyAnswer);
        }
            SurveyAnswerResource::collection($SurveyAnswer);
            return GlobalFunction::response_function(Message::SURVEY_ANSWER_DISPLAY, $SurveyAnswer);

    }

    public function store(SurveyAnswerRequest $request)
    {   
         $getNextVoucherDateByMobileNumber = SurveyAnswer::where('mobile_number', $request->input('mobile_number'))
                ->get()
                ->sortByDesc('created_at')
                ->pluck(['next_voucher_date'])
                ->first();

        if($getNextVoucherDateByMobileNumber > Carbon::now()){
            return GlobalFunction::response_function("You have already used your available voucher. Your next available one is on ".$getNextVoucherDateByMobileNumber);
        }
        

        if(!$duration = VoucherValidity::get()[0]['duration']){
            return GlobalFunction::response_function(Message::INVALID_ACTION);
        }

        $validUntil = Carbon::now()->addDays($duration);

        $vouchercode = str_replace('-', '', Str::uuid()) . $validUntil->format('YmdHis');

        $CreateSurveyAnswer = SurveyAnswer::create([
            "entry_code" => $request["entry_code"],
            "first_name" => $request["first_name"],
            "middle_name" => $request["middle_name"],
            "last_name" => $request["last_name"],
            "suffix" => $request["suffix"],
            "mobile_number" => $request["mobile_number"],
            "mobile_number_verified" => $request["mobile_number_verified"],
            "gender" => $request["gender"],
            "age" => $request["age"],

            "questionnaire_answer" => $request->answers,
            "voucher_code" => $vouchercode,
            "valid_until" => $validUntil,
            "next_voucher_date" => Carbon::now()->addDays(90),
            "claim" => "not_yet",
        ]);

        return GlobalFunction::response_function(Message::SURVEY_ANSWER_SAVE, $CreateSurveyAnswer);
        
    }


    public function update(SurveyAnswerRequest $request, $id)
    {   
        $SurveyAnswerId = SurveyAnswer::find($id);
        $validUntil = $SurveyAnswerId->valid_until;
        $voucherClaimed = $SurveyAnswerId->claim;

        if($voucherClaimed === "claimed"){
            return GlobalFunction::invalid("Your voucher has already been claimed.");
        }

        if (!$SurveyAnswerId) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        if($validUntil <= Carbon::now()){
            $SurveyAnswerId->update([
                "claim" => 'expired',
            ]);
            
            return GlobalFunction::invalid("Your voucher has expired, as it has exceeded 30 days.");
        }

        $SurveyAnswerId->update([
            "claim" => "claimed",
        ]);
        
        return GlobalFunction::response_function(Message::ROLE_UPDATE, $SurveyAnswerId);
    }

    public function archived(Request $request, $id)
    {
        $SurveyAnswerId = SurveyAnswer::withTrashed()->find($id);
        // return $role
        if (!$SurveyAnswerId) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        
        if ($SurveyAnswerId->deleted_at) {
            $SurveyAnswerId->update([
                'is_active' => 1
            ]);
            $SurveyAnswerId->restore();
            return GlobalFunction::response_function(Message::RESTORE_STATUS, $SurveyAnswerId);
        }

        if (!$SurveyAnswerId->deleted_at) {
            $SurveyAnswerId->update([
                'is_active' => 0
            ]);
            $SurveyAnswerId->delete();
            return GlobalFunction::response_function(Message::ARCHIVE_STATUS, $SurveyAnswerId);

        } 
    }

    public function sendverificationcode(Request $request){
        $otp = new Otp();
        $otpValue = $otp->generate($request->input('mobile_number'), 'numeric', 6, 10);
      
        return GlobalFunction::response_function(Message::SMS_OTP_SAVE, $otpValue);
    }
    

    public function validatecode(Request $request){
        $otp = new Otp();
        $otpValue = $otp->validate($request->input('mobile_number'), $request->input('code'));

        return GlobalFunction::response_function($otpValue);
    }
}
