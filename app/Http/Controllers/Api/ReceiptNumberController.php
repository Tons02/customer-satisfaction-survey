<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Response\Message;
use App\Models\SurveyPeriod;
use App\Models\TriggerSetUp;
use Illuminate\Http\Request;
use App\Models\ReceiptNumber;
use App\Models\SurveyInterval;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\ReceiptNumberRequest;
use App\Http\Resources\ReceiptNumberResource;

class ReceiptNumberController extends Controller
{
    use ApiResponse;
    
    public function index(Request $request)
    {   
        $status = $request->query('status');
        
        $ReceiptNumber = ReceiptNumber::
        when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
        ->orderBy('created_at', 'desc')
        ->useFilters()
        ->dynamicPaginate();
        
        $is_empty = $ReceiptNumber->isEmpty();

        if ($is_empty) {
            return GlobalFunction::response_function(Message::NOT_FOUND);
        }
            ReceiptNumberResource::collection($ReceiptNumber);
            return GlobalFunction::response_function(Message::RECEIPT_NUMBER_DISPLAY,$ReceiptNumber);

    }

    public function store(ReceiptNumberRequest $request)
    {      
        $storeId = $request->store_id;

        // Count the total receipt numbers for the given store_id
        $count = ReceiptNumber::withTrashed()->where('store_id', $storeId)->count();

        // Get the limit and trigger point from the TriggerSetup model
        $triggerSetup = TriggerSetUp::first();
        if (!$triggerSetup) {
            return GlobalFunction::not_found(Message::TRIGGER_INVALID);
        }
        
        $limit = $triggerSetup->limit; 
        $trigger_point = $triggerSetup->trigger_point; 

        // Calculate valid trigger points up to the limit
         $validTriggerPoints = range($trigger_point, $limit, $trigger_point);

        // Check if the count exceeds the limit
        if ($count > $limit) {
            return GlobalFunction::response_function(Message::RECEIPT_NUMBER_LIMIT);
        } else {
            // Check if the current count is a valid trigger point
             $isValid = in_array($count+1, $validTriggerPoints);
        }

        //for the duration of the reciept number 
       $SurveyIntervalDay = SurveyInterval::first();
       if (!$SurveyIntervalDay) {
            return GlobalFunction::not_found(Message::SURVEY_INTERVAL_INVALID);
        }
       $SurveyPeriod = SurveyPeriod::first();
       if (!$SurveyPeriod) {
        return GlobalFunction::not_found(Message::SURVEY_PERIOD_INVALID);
        }   

        $expiryDate = Carbon::today()->addDays($SurveyIntervalDay->days);

        // comment this line if you dont want to limit the claiming base on the valid_to of survey period
        if($expiryDate > $SurveyPeriod->valid_to){
            $expiryDate = $SurveyPeriod->valid_to;
        }

        if ($SurveyPeriod->valid_from <= Carbon::today() && Carbon::today() <= $SurveyPeriod->valid_to) {
           
            $create_role = ReceiptNumber::create([
                "receipt_number" => $request->receipt_number,
                "contact_details" => $request->contact_details,
                "store_id" => auth('sanctum')->user()->store_id,
                "expiration_date" => $expiryDate,
                "is_valid" => $isValid
            ]);
    
            if($isValid){
                // Send sms when the reciept number is valid
                $token = env('SMS_TOKEN');
                $sms_post = env('SMS_POST');
    
                $response = Http::withToken($token)->post($sms_post, [
                            'system_name' => 'Customer Service Satisfaction',
                            'message' => 'Fresh Morning! You have been selected to participate in our survey. Your receipt number is ' . $request->receipt_number . '. Please visit the CSS website to complete it.',
                            'mobile_number' => $request->receipt_number,
                ]);
            }
           
            return GlobalFunction::response_function(Message::RECEIPT_NUMBER_SAVE);
         
        }

        return GlobalFunction::denied(Message::SURVEY_PERIOD_DONE);
        
         
    }

    public function update(ReceiptNumberRequest $request, $id)
    {   
        if (ReceiptNumber::where('id', $id)->where('is_used', 1)->exists()) {
            return GlobalFunction::invalid(Message::RECEIPT_NUMBER_ALREADY_USED);
        }        
        
        $receipt = ReceiptNumber::find($id);

        if (!$receipt) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        $receipt->receipt_number = $request['receipt_number'];
        $receipt->contact_details = $request['contact_details'];

        if (!$receipt->isDirty()) {
            return GlobalFunction::response_function(Message::NO_CHANGES);
        }

        $receipt->save();
        
        return GlobalFunction::response_function(Message::RECEIPT_NUMBER_UPDATE);
    }

    public function archived(Request $request, $id)
    {
        $receiptNumber = ReceiptNumber::withTrashed()->find($id);
        // return $role
        if (!$receiptNumber) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        
        if ($receiptNumber->deleted_at) {

            $receiptNumber->update([
                'is_active' => 1
            ]);
            $receiptNumber->restore();
            return GlobalFunction::response_function(Message::RESTORE_STATUS);
        }

        if (ReceiptNumber::where('id', $id)->where('is_used', 1)->exists()) {
            return GlobalFunction::invalid(Message::RECEIPT_NUMBER_ALREADY_USED);
        }        

        if (!$receiptNumber->deleted_at) {

            $receiptNumber->update([
                'is_active' => 0
            ]);
            $receiptNumber->delete();
            return GlobalFunction::response_function(Message::ARCHIVE_STATUS);

        } 
    }



}
