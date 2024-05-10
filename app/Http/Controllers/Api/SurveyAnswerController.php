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

    public function getSurveyAnswer(Request $request, $id){

        $FormHistory = FormHistory::where('survey_id', $id)
        ->first();
        
        if (!$FormHistory) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        if ($FormHistory->status == "0") {
            return GlobalFunction::not_found(Message::SURVEY_ANSWER_ALREADY_DONE);
        }

        return GlobalFunction::response_function(Message::SURVEY_ANSWER_DISPLAY, $FormHistory);

    }

    public function store(SurveyAnswerRequest $request)
    {   
         $getNextVoucherDateByMobileNumber = SurveyAnswer::where('mobile_number', $request->input('mobile_number'))
                ->get()
                ->sortByDesc('created_at')
                ->pluck(['next_voucher_date'])
                ->first();

        if($getNextVoucherDateByMobileNumber > Carbon::now()){
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
        ]);

        $FormHistory = FormHistory::create([
            "survey_id" => $CreateSurveyAnswer->id,
            "mobile_number" => $request["mobile_number"],
            "title" => $form->title,
            "description" => $form->description,
            "sections" => $form->sections,

        ]);

        return GlobalFunction::response_function(Message::SURVEY_ANSWER_SAVE, $FormHistory);
        
    }

    public function updateSurveyAnswer(Request $request, $id){

        $SurveyAnswerId = SurveyAnswer::where('id', $id)
        ->first();

        $FormHistoryId = FormHistory::where('survey_id', $id)
        ->first();
        // return $role
        if (!$SurveyAnswerId) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        if ($SurveyAnswerId->done == "1") {
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
        ]);

        $FormHistoryId->update([
            "status" => 0,
        ]);



        return GlobalFunction::response_function(Message::SURVEY_ANSWER_DISPLAY, $SurveyAnswerId);

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
