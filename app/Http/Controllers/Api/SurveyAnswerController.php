<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
// use Datetime;
use App\Models\User;
use App\Models\Forms;
use Ichtrojan\Otp\Otp;
use App\Response\Message;
use App\Models\FormHistory;
use Illuminate\Support\Str;
use App\Models\SurveyAnswer;
use Illuminate\Http\Request;
use App\Models\QuestionAnswer;
use App\Models\VoucherValidity;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\SurveyAnswerRequest;
use App\Http\Requests\ExtendValidityRequest;
use App\Http\Resources\SurveyAnswerResource;

class SurveyAnswerController extends Controller
{
    use ApiResponse;
    
    public function getAllSurveyAnswer(Request $request)
    {   
        $status = $request->query('status');
        $claim = $request->query('claim') ?? '';
        $voucher_code = $request->query('voucher_code');

        
        $from_date = $request->query('from_date') ?? '2023-06-11';
        $to_date = $request->query('to_date') ?? '2055-06-11';
         Carbon::parse($to_date)->addDays(1);
        //  return  $to_date=addDays(1);
        $reports = $request->query('reports');
        
        
        $SurveyAnswer = SurveyAnswer::
        when($status === "inactive", function ($query) {
            $query->onlyTrashed();        
        })
        ->when($reports === 'valid_until', function($query) use ($from_date, $to_date) {
            $query->whereBetween('valid_until', [$from_date, $to_date]);
        })   
        ->when($reports === 'submit_date', function($query) use ($from_date, $to_date) {
            $query->whereBetween('submit_date', [$from_date, $to_date]);
        })     
        ->when($reports === 'claimed_date', function($query) use ($from_date, $to_date) {
            $query->whereBetween('updated_at', [$from_date, $to_date])
                  ->whereNotNull('claim_by_user_id');
        })      
        ->when(!empty($voucher_code), function($query) use ($voucher_code) {
           $query->where('voucher_code', $voucher_code);
        })
        ->when($voucher_code === null && $claim != null, function($query) use ($claim) {
            $query->where('claim', $claim);
        })  
        ->orderBy('created_at', 'desc')
        ->useFilters()
        ->dynamicPaginate();
        
        $is_empty = $SurveyAnswer->isEmpty();

        if ($is_empty) {
            return GlobalFunction::response_function(Message::NOT_FOUND, $SurveyAnswer);
        }

        $SurveyAnswerResource =  SurveyAnswerResource::collection($SurveyAnswer);
        
        $ResultSurveyAnswer = $request->query('pagination') == 'none' 
        ? ['data' => SurveyAnswerResource::collection($SurveyAnswer)] 
        : $SurveyAnswer;

        return GlobalFunction::response_function(Message::SURVEY_ANSWER_DISPLAY, $ResultSurveyAnswer );

    }

    public function getPublicDoneSurvey(Request $request)
    {   
        $status = $request->query('status');
        $search = $request->query('search');
        
        $SurveyAnswer = SurveyAnswer::
        where('voucher_code', $search)
        ->get()
        ->first();
        
        if (!$SurveyAnswer) {
            return GlobalFunction::invalid(Message::NOT_FOUND, $SurveyAnswer);
        }

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
                return GlobalFunction::invalid(Message::NOT_FOUND, $SurveyId);
            }
            return GlobalFunction::invalid(Message::SURVEY_ANSWER_ALREADY_DONE, 
            [
                'voucher_code' => $SurveyId->voucher_code,
            ]);


            return GlobalFunction::not_found(Message::SURVEY_ANSWER_ALREADY_DONE,
            [
                'voucher_code' => $SurveyAnswerId->voucher_code, 
            ]);

        }

        return GlobalFunction::response_function(Message::SURVEY_ANSWER_DISPLAY, $FormHistory);

    }

    public function checkEntryCode(Request $request, $mobile_number, $entry_code)
{
    $validator = \Validator::make(
        [
            'mobile_number' => $mobile_number,
        ],
        [
            'mobile_number' => ['required', 'regex:/^\+63\d{10}$/'],
        ]
    );

    if ($validator->fails()) {
        return response()->json(['message' => 'Invalid mobile number format.'], 400);
    }

    // Fetch the latest voucher associated with the mobile number
    $VoucherId = SurveyAnswer::withTrashed()
        ->where('mobile_number', $mobile_number)
        ->latest('created_at')
        ->first();

    $now = Carbon::now();

    if (!$VoucherId) {
        return GlobalFunction::response_function(
            Message::ENTRY_CODE_AVAILABLE,
            [
                'entry_code' => $entry_code,
                'mobile_number' => $mobile_number,
                'status' => 'available'
            ]
        );
    }

    // Check if the voucher is valid and active
    if ($VoucherId->next_voucher_date > $now && $VoucherId->entry_code === $entry_code) {
        if ($VoucherId->valid_until < $now) {
            return GlobalFunction::invalid(
                "You have already used your available voucher. Your next available one is on " . $VoucherId->next_voucher_date,
                [
                    'voucher_code' => $VoucherId->voucher_code,
                    'valid_until' => $VoucherId->valid_until,
                    'claim' => $VoucherId->claim,
                    'status' => 'voucher_expired'
                ]
            );
        }

        return GlobalFunction::invalid(
            "You have already used your available voucher. Your next available one is on " . $VoucherId->next_voucher_date,
            [
                'voucher_code' => $VoucherId->voucher_code,
                'valid_until' => $VoucherId->valid_until,
                'claim' => $VoucherId->claim,
                'status' => 'voucher_available'
            ]
        );
    }

    // Check if the entry code is correct
    $VoucherId = SurveyAnswer::withTrashed()
        ->where('mobile_number', $mobile_number)
        ->where('entry_code', $entry_code)
        ->latest('created_at')
        ->first();

    if (!$VoucherId) {
        return GlobalFunction::invalid(
            "Invalid Entry Code."
        );
    }

    // Check if the voucher is done or not
    if ($VoucherId->voucher_code == null) {
        $FormHistoryId = FormHistory::where('survey_id', $VoucherId->id)->first();

        if (!$FormHistoryId) {
            return GlobalFunction::response_function(Message::NOT_FOUND, $FormHistoryId);
        }

        return GlobalFunction::not_found(
            Message::ENTRY_CODE_NOT_DONE,
            [
                'entry_code' => $VoucherId->entry_code,
                'survey_id' => $VoucherId->id,
                'security_code' => $FormHistoryId->security_code,
                'status' => "not done"
            ]
        );
    }

    return GlobalFunction::response_function(
        Message::ENTRY_CODE_AVAILABLE,
        [
            'entry_code' => $entry_code,
            'mobile_number' => $mobile_number,
            'status' => "available"
        ]
    );
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

        SurveyAnswer::where('mobile_number', $request->input('mobile_number'))
        ->where('is_active', 0)
        ->forceDelete();

        FormHistory::where('mobile_number', $request->input('mobile_number'))
        ->forceDelete();

     $CreateSurveyAnswer = SurveyAnswer::create([
            "entry_code" => $request["entry_code"],
            "first_name" => $request["first_name"],
            "middle_name" => $request["middle_name"],
            "last_name" => $request["last_name"],
            "suffix" => $request["suffix"],
            "mobile_number" => $request["mobile_number"],
            "mobile_number_verified" => $request["mobile_number_verified"],
            "gender" => $request["gender"],
            "birthday" => $request["birthday"],
            "is_active" => 0,
        ]);

        //softdelete the main survey
        $softdelete = SurveyAnswer::where('id', $CreateSurveyAnswer->id)
        ->Delete();

        $FormHistory = FormHistory::create([
            "survey_id" => $CreateSurveyAnswer->id,
            "security_code" => $validUntil = Carbon::now()->format('YmdHis'),
            "mobile_number" => $request["mobile_number"],
            "title" => $form->title,
            "description" => $form->description,
            "sections" => $form->sections,
        ]);

        $responseData = $FormHistory->toArray();
        $responseData['entry_code'] = $CreateSurveyAnswer->entry_code;

        return GlobalFunction::response_function(Message::REGISTRATION_SUCCESSFULLY, $responseData);
        
    }

    public function updateSurveyAnswer(Request $request, $id){

         $request->input('questionnaire_answer');

        $SurveyAnswerId = SurveyAnswer::
        withTrashed()
       ->where('id', $id)
        ->first();

        $FormHistoryId = FormHistory::where('survey_id', $id)
        ->first();
        
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

        

        $SurveyAnswerId->restore();

        $SurveyAnswerId->update([
            "questionnaire_answer" => $request->input('questionnaire_answer'),
            "voucher_code" => $voucherCode,
            "valid_until" => $validUntil,
            "next_voucher_date" => Carbon::now()->addDays(90),
            "claim" => "not_yet",
            "is_active" => 1,
            "submit_date" => Carbon::now(),
        ]);

        QuestionAnswer::where('survey_id', $id)
        ->forceDelete();

        $questionnaire_answers = $request->input('questionnaire_answer');

    // Loop through each section
    foreach ($questionnaire_answers as $questionnaire_answer) {
        // Loop through each question within the section
        foreach ($questionnaire_answer['questions'] as $question) {
            // Handle grid type questions separately
            if ($question['questionType'] === 'grid') {
                foreach ($question['answer'] as $gridAnswer) {
                    if ($gridAnswer['rowAnswer'] === "") {
                        continue; // Skip if the answer is empty
                    }

                    $finalAnswer = $gridAnswer['rowAnswer'] === "Other" 
                        ? (is_array($gridAnswer['otherAnswer']) ? implode(', ', $gridAnswer['otherAnswer']) : $gridAnswer['otherAnswer']) 
                        : $gridAnswer['rowAnswer'];

                    QuestionAnswer::create([
                        'survey_id' => $id,
                        'question_type' => $question['questionType'],
                        'question' => $question['questionName'] . ' - ' . $gridAnswer['rowQuestion'],
                        'answer' => $finalAnswer,
                    ]);
                }
            } else {
                $answers = is_array($question['answer']) ? $question['answer'] : [$question['answer']];
                
                foreach ($answers as $answer) {
                    if ($answer === "") {
                        continue; // Skip if the answer is empty
                    }

                    $finalAnswer = $answer === "Other" 
                        ? (is_array($question['otherAnswer']) ? implode(', ', $question['otherAnswer']) : $question['otherAnswer']) 
                        : $answer;

                    QuestionAnswer::create([
                        'survey_id' => $id,
                        'question_type' => $question['questionType'],
                        'question' => $question['questionName'],
                        'answer' => $finalAnswer,
                    ]);
                }
            }
        }
    }

        FormHistory::where('mobile_number', $SurveyAnswerId->mobile_number)
        ->forceDelete();

        return GlobalFunction::response_function(Message::SURVEY_ANSWER_SAVE, $SurveyAnswerId);

    }

    public function claimingVoucher(Request $request, $id)
    {   
        $SurveyAnswerId = SurveyAnswer::find($id);
        if (!$SurveyAnswerId) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
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
            
        return GlobalFunction::invalid("Your voucher has expired");
        }

        $SurveyAnswerId->update([
            "claim" => "claimed",
            "claim_by_user_id" => auth('sanctum')->user()->id, 
        ]);
        
        return GlobalFunction::response_function(Message::VOUCHER_CLAIM_SUCCESSFULLY, 
        [
            "voucher_code" => $SurveyAnswerId->voucher_code,
            "claim" => $SurveyAnswerId->claim,
            "claim_by_user_id" => auth('sanctum')->user()->id, 
        ]);
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

    public function extendVoucher(ExtendValidityRequest $request)
    {
        $surveyIds = $request->input('survey_ids');
        $extendDate = $request->input('extend_date');
    
        SurveyAnswer::whereIn('id', $surveyIds)
            ->update(['valid_until' => $extendDate]);
    
        return GlobalFunction::response_function(Message::VOUCHER_VALIDITY_EXTEND);
    }
    
    

}
