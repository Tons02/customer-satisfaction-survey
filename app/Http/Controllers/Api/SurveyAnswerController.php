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
use App\Models\SurveyPeriod;
use Illuminate\Http\Request;
use App\Models\ReceiptNumber;
use App\Models\QuestionAnswer;
use App\Models\SurveyInterval;
use App\Models\VoucherValidity;
use App\Functions\GlobalFunction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\SurveyAnswerRequest;
use App\Http\Requests\ExtendValidityRequest;
use App\Http\Resources\SurveyAnswerResource;
use App\Http\Requests\RegisterCheckingRequest;

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
            $query->whereBetween('claimed_date', [$from_date, $to_date])
                  ->whereNotNull('claim_by_user_id');
        })      
        ->when(!empty($voucher_code), function($query) use ($voucher_code) {
           $query->where('voucher_code', $voucher_code);
        })
        ->when($voucher_code === null && $claim != null, function($query) use ($claim, $voucher_code, $reports) {
            $query->when($voucher_code === null && $claim !== null, function($query) use ($claim, $reports) {
                if ($claim === 'not_yet' && $reports === 'valid_until') {
                    // Add multiple conditions using `where` or `orWhere`
                    $query->where(function($query) use ($claim) {
                        $query->where('claim', $claim)
                              ->orWhere('claim', 'not_yet')
                              ->orWhere('claim', 'expired');
                    });
                } else {
                    // Default case when $claim is not 'not_yet' or $valid_until is not 'valid_until'
                    $query->where('claim', $claim);
                }
            });            
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

    public function getSurveyAnswer(Request $request, $id, $security_code, $receipt_number){

        $FormHistory = FormHistory::where('survey_id', $id)
        ->where('security_code', $security_code)
        ->first();
        
        if (!$FormHistory) {
            $SurveyId = SurveyAnswer::where('id', $id)
            ->where('receipt_number', $receipt_number)
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

    public function checkEntryCode(RegisterCheckingRequest $request, $mobile_number, $receipt_number, $store_id)
{

    //add validation for survey period
    $SurveyPeriod = SurveyPeriod::latest()
    ->first();

    if (!$SurveyPeriod) {
        return GlobalFunction::invalid(Message::SURVEY_PERIOD_INVALID);
    }

    // check if your date is valid between 
    if (Carbon::now()->between($SurveyPeriod->valid_from, $SurveyPeriod->valid_to)) {

    $mobile_number =  $request->mobile_number;
    $receipt_number =  $request->receipt_number;
    $store_id =  $request->store_id;
    
    // Check if the mobile number exists
    $receiptNumberByMobile = ReceiptNumber::where('contact_details', $mobile_number)
    ->where('is_valid', 1)
    ->latest()
    ->first();

    if (!$receiptNumberByMobile) {
    return GlobalFunction::invalid(
        Message::INVALID_MOBILE_NUMBER
    );
    }

    // Check if the receipt number connected to number is true
    $receiptNumberandReceipt = ReceiptNumber::where('contact_details', $mobile_number)
    ->where('receipt_number', $receipt_number)
    ->where('is_valid', 1)
    ->latest()
    ->first();

    if (!$receiptNumberandReceipt) {
    return GlobalFunction::invalid(
        Message::INVALID_RECEIPT_NUMBER,
    );
    }

    // Check if the store ID exists for the provided mobile number and receipt number
    $receiptNumberByStore = ReceiptNumber::where('contact_details', $mobile_number)
    ->where('receipt_number', $receipt_number)
    ->where('store_id', $store_id)
    ->where('is_valid', 1)
    ->latest()
    ->first();

    if (!$receiptNumberByStore) {
    return GlobalFunction::invalid(
        Message::INVALID_STORE,
    );
    }
    

    // check if the valid receipt number is expired before throwing available
    if ($receiptNumberByStore->expiration_date <= Carbon::now()) {
        return GlobalFunction::invalid(
            Message::RECEIPT_EXPIRED,
        );
    }


    // if the receipt number is not used it will give status available to inform the user that it can take survey
    if(!$receiptNumberByStore->is_used){
        return GlobalFunction::response_function(
            Message::RECEIPT_NUMBER_AVAILABLE,
            [
                'receipt_number' => $receipt_number,
                'mobile_number' => $mobile_number,
                'valid_until' => $receiptNumberByStore->expiration_date,
                'status' => 'available'
            ]
        );
    }
    

    // check if the receipt number tag survey is done if not throw the security code to continue the survey
    if(!$receiptNumberByStore->is_done){
        $VoucherId = SurveyAnswer::withTrashed()
        ->where('receipt_number', $receipt_number)
        ->where('mobile_number', $mobile_number)
        ->where('store_id', $store_id)
        ->latest()
        ->first();

        $FormHistoryId = FormHistory::where('survey_id', $VoucherId->id)->first();

        if (!$FormHistoryId) {
            return GlobalFunction::invalid(Message::NOT_FOUND, $FormHistoryId);
        }

        return GlobalFunction::not_found(
            Message::ENTRY_CODE_NOT_DONE,
            [
                'receipt_number' => $VoucherId->receipt_number,
                'survey_id' => $VoucherId->id,
                'security_code' => $FormHistoryId->security_code,
                'status' => "not done"
            ]
        );
    }

    // check if the receipt number tag survey is done 
    if($receiptNumberByStore->is_done){
        $VoucherId = SurveyAnswer::
        where('receipt_number', $receipt_number)
        ->where('mobile_number', $mobile_number)
        ->where('store_id', $store_id)
        ->latest()
        ->first();

        if (!$VoucherId) {
            return GlobalFunction::invalid(Message::NOT_FOUND, $VoucherId);
        }  
        
        if ($VoucherId->valid_until <= Carbon::now()) {
            return GlobalFunction::invalid(
                "You have already used your voucher. Your next available one is on " . $VoucherId->next_voucher_date,
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
    }else {
        // The current date is outside the survey period
        return GlobalFunction::invalid(Message::SURVEY_PERIOD_ALREADY_CLOSED);
    }

}


    public function createSurvey(SurveyAnswerRequest $request)
    {       
        $SurveyPeriod = SurveyPeriod::latest()
        ->first();

        if (!$SurveyPeriod) {
            return GlobalFunction::response_function(Message::SURVEY_PERIOD_INVALID);
        }
        // return $SurveyPeriod->valid_to;

        if ($SurveyPeriod->valid_from <= Carbon::now() && Carbon::now() >= $SurveyPeriod->valid_to ) {
            // i want here that valid_from is less than the day today and the valid_to is less than today it will go thgis
            return GlobalFunction::response_function(Message::SURVEY_PERIOD_ALREADY_CLOSED);
        }         

        // Check if the mobile number exists
        $receiptNumberByMobile = ReceiptNumber::
        where('contact_details', $request["mobile_number"])
        ->where('is_valid', 1)
        ->where('is_valid', 1)
        ->latest()
        ->first();

        if (!$receiptNumberByMobile) {
        return GlobalFunction::response_function(
            Message::INVALID_MOBILE_NUMBER
        );
        }

        // Check if the receipt number exists for the provided mobile number
        $receiptNumberByReceipt = ReceiptNumber::
        where('contact_details', $request["mobile_number"])
        ->where('receipt_number', $request["receipt_number"])
        ->where('is_valid', 1)
        ->latest()
        ->first();

        if (!$receiptNumberByReceipt) {
        return GlobalFunction::response_function(
            Message::INVALID_RECEIPT_NUMBER,
        );
        }

        // Check if the store ID exists for the provided mobile number and receipt number
        $receiptNumberByStore = ReceiptNumber::
        where('contact_details', $request["mobile_number"])
        ->where('receipt_number', $request["receipt_number"])
        ->where('store_id', $request["store_id"])
        ->where('is_valid', 1)
        ->latest()
        ->first();

        if (!$receiptNumberByStore) {
        return GlobalFunction::response_function(
            Message::INVALID_STORE,
        );
        }

        // check if the valid receipt number is expired before throwing available
        if ($receiptNumberByStore->expiration_date <= Carbon::now()) {
            return GlobalFunction::invalid(
                Message::RECEIPT_EXPIRED,
            );
        }


        // check if the survey is done
        if($receiptNumberByStore->expiration_date > Carbon::now() && $receiptNumberByStore->is_done == true){
            return GlobalFunction::invalid("You have already used your available voucher. Your next available one is on ".$getNextVoucherDateByMobileNumber);
        }

        //prevent the creation if receipt is already used
         // check if the survey is done
         if($receiptNumberByStore->is_used == true){
            return GlobalFunction::invalid("This receipt is already used");
        }
        
        //get the form
        $form = Forms::get()->first();

        //throws error when there is no form
        if (!$form) {
            return GlobalFunction::invalid(Message::SURVEY_FORM_INVALID);
        }

        // delete answers that is not continue
        SurveyAnswer::where('mobile_number', $request->input('mobile_number'))
        ->where('is_active', 0)
        ->forceDelete();

        //delete history that is not continue
        FormHistory::where('mobile_number', $request->input('mobile_number'))
        ->forceDelete();

        //create the survey
     $CreateSurveyAnswer = SurveyAnswer::create([
            "receipt_number" => $request["receipt_number"],
            "store_id" => $request["store_id"],
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

        //create a history of taking survey answers
        $FormHistory = FormHistory::create([
            "survey_id" => $CreateSurveyAnswer->id,
            "security_code" => $validUntil = Carbon::now()->format('YmdHis'),
            "mobile_number" => $request["mobile_number"],
            "title" => $form->title,
            "description" => $form->description,
            "sections" => $form->sections,
        ]);

        // update the status of receipt number that this receipt is used on survey
        $receiptNumberByStore->update([
            "is_used" => 1,
        ]);

        $responseData = $FormHistory->toArray();
        $responseData['entry_code'] = $CreateSurveyAnswer->entry_code;

        return GlobalFunction::response_function(Message::REGISTRATION_SUCCESSFULLY, $responseData);
        
    }

    public function updateSurveyAnswer(Request $request, $id){

        //add validation for survey period
        $SurveyPeriod = SurveyPeriod::latest()
        ->first();

        if (!$SurveyPeriod) {
            return GlobalFunction::response_function(Message::SURVEY_PERIOD_INVALID);
        }
        // return $SurveyPeriod->valid_to;

        if ($SurveyPeriod->valid_from <= Carbon::now() && Carbon::now() >= $SurveyPeriod->valid_to ) {
            // i want here that valid_from is less than the day today and the valid_to is less than today it will go thgis
            return GlobalFunction::response_function(Message::SURVEY_PERIOD_ALREADY_CLOSED);
        }         

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

        $duration = VoucherValidity::latest()
        ->first();

        if (!$duration) {
            return GlobalFunction::response_function(Message::SURVEY_VALIDITY_INVALID);
        }

        // Check the survey period and duration exceed to set the valid until date
        if (Carbon::parse($SurveyPeriod->valid_to) <= Carbon::now()->addDays($duration->duration)) {
            $validUntil = Carbon::parse($SurveyPeriod->valid_to);
        } else {
            $validUntil = Carbon::now()->addDays($duration->duration);
        }
        
        // Format $validUntil as 'Y-m-d H:i:s' for consistent output
        $validUntilFormatted = $validUntil->format('Y-m-d H:i:s');
        
        // Generate the voucher code with the formatted date
        $voucherCode = substr(str_replace('-', '', Str::uuid()), 0, 5) . Carbon::now()->format('Ymdhis'); 


        $SurveyAnswerId->restore();

        $SurveyAnswerId->update([
            "questionnaire_answer" => $request->input('questionnaire_answer'),
            "voucher_code" => $voucherCode,
            "valid_until" => $validUntil,
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

        //update the status of receipt to done
        $receiptNumber = ReceiptNumber::
        where('contact_details', $SurveyAnswerId->mobile_number)
        ->where('receipt_number', $SurveyAnswerId->receipt_number)
        ->where('is_valid', 1)
        ->latest()
        ->first();

        $receiptNumber->update([
            "is_done" => 1,
        ]);

        // delete the history of survey 
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
            "claimed_date" => Carbon::now()
        ]);
        
        return GlobalFunction::response_function(Message::VOUCHER_CLAIM_SUCCESSFULLY, 
        [
            "voucher_code" => $SurveyAnswerId->voucher_code,
            "claim" => $SurveyAnswerId->claim,
            "claim_by_user_id" => auth('sanctum')->user()->id, 
            "claimed_date" => Carbon::now()
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
        ->update([
            'valid_until' => $extendDate,
            'claim' => 'not_yet'
        ]);
    
        return GlobalFunction::response_function(Message::VOUCHER_VALIDITY_EXTEND);
    }

    public function getDataChart(Request $request) {
        $data = $request->query('data');
        $status = $request->query('status');
        $store = $request->query('store');
        $from_date = $request->query('from_date') ?? '2023-06-11';
        $to_date = $request->query('to_date') ?? Carbon::now();
        $year = $request->query('year') ?? Carbon::now()->year;
         $startyear = Carbon::createFromDate($year)->startOfYear();  // Start of the specified year
         $endyear = Carbon::createFromDate($year)->endOfYear();  // End of the specified year


        $ChartData = SurveyAnswer::
            when($status === "inactive", function ($query) {
                $query->onlyTrashed();
            })
            ->when($data === 'gender', function($query) use ($from_date, $to_date, $store) {
                $query->with('store')
                    ->select('gender', DB::raw('count(*) as total'))
                    ->when(!is_null($store), function($query) {
                        $query->addSelect('store_id')->groupBy('gender', 'store_id');
                    }, function($query) {
                        $query->groupBy('gender'); // Do not group by store_id when "All store" is selected
                    })
                    ->whereBetween('submit_date', [$from_date, $to_date]);
            })
            ->when($data === 'age', function($query) use ($from_date, $to_date, $store) {
                $query->with('store')
                    ->select(
                        DB::raw('CASE 
                                    WHEN birthday IS NULL THEN "Unknown"
                                    WHEN TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN 0 AND 14 THEN "Children (0-14)" 
                                    WHEN TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN 15 AND 24 THEN "Youth (15-24)" 
                                    WHEN TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN 25 AND 64 THEN "Adults (25-64)" 
                                    ELSE "Seniors (65+)"
                                END as age_group'),
                        DB::raw('count(*) as total')
                    )
                    ->whereNotNull('birthday') // Ensure birthday is not null
                    ->when(!is_null($store), function($query) {
                        $query->addSelect('store_id')->groupBy('age_group', 'store_id');
                    }, function($query) {
                        $query->groupBy('age_group'); // Do not group by store_id when "All store" is selected
                    })
                    ->whereBetween('submit_date', [$from_date, $to_date]);
            })                        
            ->when($data === 'line-chart', function ($query) use ($startyear, $endyear, $store) {
                $query->with('store')
                    ->select(
                        DB::raw("DATE_FORMAT(submit_date, '%Y-%m') as month"),
                        DB::raw('COUNT(*) as total_respondent'),
                        DB::raw("SUM(CASE WHEN claim = 'claimed' THEN 1 ELSE 0 END) as total_claimed"),
                        DB::raw("SUM(CASE WHEN claim = 'not_yet' THEN 1 ELSE 0 END) as total_not_claimed"),
                        DB::raw("SUM(CASE WHEN claim = 'expired' THEN 1 ELSE 0 END) as total_expired")
                    )
                    ->whereBetween('submit_date', [$startyear, $endyear])
                    ->when(!is_null($store), function ($query) use ($store) {
                        $query->addSelect('store_id')->groupBy('month', 'store_id');
                    }, function ($query) {
                        $query->groupBy('month'); // Group by month only when "All store" is selected
                    });
            })                      
            ->useFilters()
            ->dynamicPaginate();
    
        $is_empty = $ChartData->isEmpty();
    
        if ($is_empty) {
            return GlobalFunction::response_function(Message::NOT_FOUND, $ChartData);
        }
    
        // Check if $store is null, then set all stores under "All store"
        if (is_null($store)) {
            $ChartData = collect([
                [
                    'store' => 'All store',
                    'data' => $ChartData->map(function ($item) use ($data) {
                        if ($data === 'gender') {
                            return [
                                'gender' => $item->gender,
                                'total' => $item->total,
                            ];
                        } elseif ($data === 'age') {
                            return [
                                'age' => $item->age_group,
                                'total' => $item->total,
                            ];
                        } elseif ($data === 'line-chart') {
                            return [
                                $item->month => [
                                    'total_respondent' => (int) $item->total_respondent,
                                    'total_claimed' => (int) $item->total_claimed,
                                    'total_not_claimed' => (int) $item->total_not_claimed,
                                    'total_expired' => (int) $item->total_expired,
                                ]
                            ];
                        }
                    })->values()
                ]
            ]);
        } else {
            // When a specific store is selected, group by store name
            $ChartData = $ChartData->groupBy('store.name')->map(function (Collection $items, $storeName) use ($data) {
                return [
                    'store' => $storeName,
                    'data' => $items->map(function ($item) use ($data) {
                        if ($data === 'gender') {
                            return [
                                'gender' => $item->gender,
                                'total' => $item->total,
                            ];
                        } elseif ($data === 'age') {
                            return [
                                'age' => $item->age_group,
                                'total' => $item->total,
                            ];
                        } elseif ($data === 'line-chart') {
                            return [
                                $item->month => [
                                    'total_respondent' => (int) $item->total_respondent,
                                    'total_claimed' => (int) $item->total_claimed,
                                    'total_not_claimed' => (int) $item->total_not_claimed,
                                    'total_expired' => (int) $item->total_expired,
                                ]
                            ];
                        }
                    })->values()
                ];
            })->values();
        }

    
        return GlobalFunction::response_function(Message::CHART_FOR_AGE, $ChartData);
    }
    

    
    
    

}
