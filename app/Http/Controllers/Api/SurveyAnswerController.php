<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Forms;
use Ichtrojan\Otp\Otp;
use App\Response\Message;
use App\Models\FormHistory;
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

    public function getSurveyAnswer(Request $request, $id, $security_code, $entry_code){

        $FormHistory = FormHistory::where('survey_id', $id)
        ->where('security_code', $security_code)
        ->first();
        
        if (!$FormHistory) {
            $SurveyId = SurveyAnswer::where('id', $id)
            ->where('entry_code', $entry_code)
            ->first();

            if (!$SurveyId) {
                return GlobalFunction::response_function(Message::NOT_FOUND, $SurveyId);
            }

            return GlobalFunction::not_found(Message::SURVEY_ANSWER_ALREADY_DONE);

        }

        return GlobalFunction::response_function(Message::SURVEY_ANSWER_DISPLAY, $FormHistory);

    }

    public function checkEntryCode(Request $request, $mobile_number, $entry_code){
        
        $validator = \Validator::make(
            [
                'mobile_number' => $mobile_number,
                'entry_code' => $entry_code    
            ],
            [
                'mobile_number' => ['required', 'regex:/^\+63\d{10}$/'],
                'entry_code' => ['required']
            ]
        );
    
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid mobile number format.'], 400);
        }

        $VoucherId = SurveyAnswer::where('mobile_number', $mobile_number)
            ->where('entry_code', $entry_code)
            ->first();

        if($VoucherId){

            if($VoucherId->next_voucher_date > Carbon::now() && $VoucherId->is_active == 1){
                
                if($VoucherId->valid_until > Carbon::now()){
                    return GlobalFunction::response_function(
                        "You have already used your available voucher. Your next available one is on ".$VoucherId->next_voucher_date, 
                        [
                            "voucher_code" => $VoucherId->voucher_code,
                            "valid_until" => $VoucherId->valid_until,
                            "status" => 'available'
                        ]
                    );
                }

                return GlobalFunction::response_function(
                    "You have already used your available voucher. Your next available one is on ".$VoucherId->next_voucher_date, 
                    [
                        "voucher_code" => $VoucherId->voucher_code,
                        "valid_until" => $VoucherId->valid_until,
                        "status" => 'expired'
                    ]
                );

            }
        }
        
        if (!$VoucherId) {
            return GlobalFunction::response_function(
                Message::ENTRY_CODE_AVAILABLE, 
                [
                    'entry_code' => $entry_code, 
                    'mobile_number' =>  $mobile_number, 
                    'status' => "available"
                ]
             );
        }

        if ( $VoucherId->voucher_code == null) {
            $FormHistoryId = FormHistory::where('survey_id',  $VoucherId->id)
            ->first();

            if (!$FormHistoryId) {
                return GlobalFunction::response_function(Message::NOT_FOUND, $FormHistoryId);
            }

            return GlobalFunction::not_found(
                Message::ENTRY_CODE_NOT_DONE, 
                [
                    'entry_code' => $entry_code, 
                    'survey_id' => $VoucherId->id, 
                    'security_code' => $FormHistoryId->security_code, 
                    'status' => "not done"
                ]
            );
        }

    }

    public function createSurvey(SurveyAnswerRequest $request)
    {   
        $getNextVoucherDateByMobileNumber = SurveyAnswer::where('mobile_number', $request->input('mobile_number'))
            ->get()
            ->pluck(['next_voucher_date'])
            ->first();

         $getSurveyFormIfDone = SurveyAnswer::where('mobile_number', $request->input('mobile_number'))
            ->get()
            ->first();

        if($getNextVoucherDateByMobileNumber > Carbon::now() && $getSurveyFormIfDone->is_active == 1){
            return GlobalFunction::invalid("You have already used your available voucher. Your next available one is on ".$getNextVoucherDateByMobileNumber);
        }
        

        $form = Forms::get()->first();

        if (!$form) {
            return GlobalFunction::invalid(Message::INVALID_ACTION);
        }
        
        if (!$duration = VoucherValidity::get()[0]['duration']) {
            return GlobalFunction::response_function(Message::INVALID_ACTION);
        }
        
        $validUntil = Carbon::now()->addDays($duration);

        SurveyAnswer::where('mobile_number', $request->input('mobile_number'))
        ->where('is_active', 0)
        ->forceDelete();

        $security = substr(str_replace('-', '', Str::uuid()), 0, 6) . $validUntil->format('YmdHis');


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

            "questionnaire_answer" => $request->questionnaire_answer,
            "valid_until" => $validUntil,
            "next_voucher_date" => Carbon::now()->addDays(90),
            "claim" => "not_yet",
            "is_active" => 0,
        ]);

        $FormHistory = FormHistory::create([
            "survey_id" => $CreateSurveyAnswer->id,
            "security_code" => $security,
            "mobile_number" => $request["mobile_number"],
            "title" => $form->title,
            "description" => $form->description,
            "sections" => $form->sections,
        ]);

        return GlobalFunction::response_function(Message::REGISTRATION_SUCCESSFULLY, $FormHistory);
        
    }

    public function updateSurveyAnswer(Request $request, $id){

        return $request->input('questionnaire_answer');

        $SurveyAnswerId = SurveyAnswer::where('id', $id)
        ->first();

        $FormHistoryId = FormHistory::where('survey_id', $id)
        ->first();
        // return $role
        if (!$SurveyAnswerId) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        if ($SurveyAnswerId->is_active == "1") {
            return GlobalFunction::not_found(Message::SURVEY_ANSWER_ALREADY_DONE);
        }

        if (!$duration = VoucherValidity::get()[0]['duration']) {
            return GlobalFunction::response_function(Message::INVALID_ACTION);
        }
        
        $validUntil = Carbon::now()->addDays($duration);
        
        $voucherCode = substr(str_replace('-', '', Str::uuid()), 0, 6) . $validUntil->format('YmdHis');        

        $SurveyAnswerId->update([
            "questionnaire_answer" => $request->input('questionnaire_answer'),
            "voucher_code" => $voucherCode,
            "is_active" => 1,
        ]);

        

        FormHistory::where('mobile_number', $SurveyAnswerId->mobile_number)
        ->delete();

        return GlobalFunction::response_function(Message::SURVEY_ANSWER_SAVE, $SurveyAnswerId);

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

}
