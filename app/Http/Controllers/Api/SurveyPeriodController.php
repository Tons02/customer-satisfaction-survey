<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Response\Message;
use App\Models\FormHistory;
use App\Models\SurveyAnswer;
use App\Models\SurveyPeriod;
use Illuminate\Http\Request;
use App\Models\ReceiptNumber;
use App\Models\VoucherValidity;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use App\Http\Requests\SurveyPeriodRequest;

class SurveyPeriodController extends Controller
{
    public function index(Request $request){
        $status = $request->query('status');
        
        $SurveyPeriod = SurveyPeriod::
        first();
        
        if (is_null($SurveyPeriod)) {
            return GlobalFunction::response_function(Message::NOT_FOUND, null);
        }
        
        return GlobalFunction::response_function(Message::SURVEY_PERIOD_DISPLAY, $SurveyPeriod);
    }   

    public function store(SurveyPeriodRequest $request) {

        if (SurveyPeriod::count()){
            return GlobalFunction::response_function(Message::ALREADY_EXIST);
        }

        $create_survey_period = SurveyPeriod::create([
            "valid_from" => $request->valid_from,
            "valid_to" => $request->valid_to,
        ]);

        return GlobalFunction::response_function(Message::SURVEY_PERIOD_SAVE);
    }

    public function update(SurveyPeriodRequest $request, $id) {
        

        $survey_period = Surveyperiod::find($id);

        // Extend the durations of receipt numbers within the specified period 
        $ExpirationDateIDs = ReceiptNumber::withTrashed()
            ->whereBetween('created_at', [$survey_period->valid_from, $survey_period->valid_to])
            ->pluck('id');  // Pluck the IDs to use them in the next query

        // Update the survey expiration date
        $updateExpirationIDs = ReceiptNumber::whereIn('id', $ExpirationDateIDs)
            ->update([
                "expiration_date" => $request['valid_to']
            ]);


        if (!$survey_period) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        
        //delete old form  pag mas malaki nayung previous na valid to parang new set of survey na siya
        if ($request['valid_from'] > $survey_period->valid_to) {
            if($request['valid_from'] <= Carbon::now()) {
                return GlobalFunction::invalid(Message::SURVEY_NEW_PERIOD_INVALID);
            }
            $deleted_unanswered_survey = SurveyAnswer::onlyTrashed()
            ->where('created_at', '<', $request['valid_to'])
            ->pluck('id'); // Pluck the IDs to use them in the next query
        
            // Delete the related form history records
            $delete_unanswered_survey_history = FormHistory::whereIn('survey_id', $deleted_unanswered_survey)
                ->delete();
            
            // Finally, delete the surveys themselves
            SurveyAnswer::whereIn('id', $deleted_unanswered_survey)->forceDelete();

        }   
        // if babawasan yung duration update the valid_until voucher
        if($request['valid_to'] < $survey_period->valid_to){
                    // return 'bawasan';   
                    $duration = VoucherValidity::latest()
                    ->first();

                    if (!$duration) {
                        return GlobalFunction::invalid(Message::SURVEY_VALIDITY_INVALID);
                    }
                    // Retrieve surveys with the specified valid_until and submit_date conditions
                    $update_valid_until_survey = SurveyAnswer::select('id', 'submit_date', 'valid_until')
                ->withTrashed()
                ->whereIn('claim', ['not_yet', 'expired'])
                ->whereBetween('valid_until', [
                    min($survey_period->valid_to, $request['valid_to']),
                    max($survey_period->valid_to, $request['valid_to'])
                ])
                ->get();
            

                    // Loop through the results to update each valid_until
                    foreach ($update_valid_until_survey as $survey) {
                        // Convert submit_date to a Carbon instance
                        $submitDate = Carbon::parse($survey->submit_date);

                        // Determine the new valid_until based on submit_date + duration
                        $newValidUntil = $submitDate->addDays($duration->duration);
                        
                        // Check if the calculated valid_until exceeds the survey period valid_to
                        if ($newValidUntil > $request['valid_to']) {
                            // If it exceeds, set valid_until to survey_period valid_to
                            $survey->valid_until = $request['valid_to'];
                        } else {
                            // Otherwise, use the calculated newValidUntil
                            $survey->valid_until = $newValidUntil;
                        }

                        // Save the updated survey
                        $survey->save();
                    }


                    // Update Receipt expiration date
                    $update_valid_until_receipt = ReceiptNumber::select('id')
                    ->withTrashed()
                    ->where('expiration_date', '=', $survey_period->valid_to) 
                    ->get();

                    // Loop through the results to update each expiration_date
                    foreach ($update_valid_until_receipt as $receipt) {
                    // Convert expiration_date to a Carbon instance (if needed for further calculations)
                    $expirationDate = Carbon::parse($receipt->expiration_date);

                    // Update the expiration_date with the new valid_to value
                    $receipt->expiration_date = $request['valid_to'];

                    // Save the updated receipt
                    $receipt->save();
                    }
            }

            if($request['valid_to'] > $survey_period->valid_to){
                    // return 'new set of survey';
                $duration = VoucherValidity::latest()
                ->first();

                if (!$duration) {
                    return GlobalFunction::invalid(Message::SURVEY_VALIDITY_INVALID);
                }


                // Retrieve surveys with the specified valid_until and submit_date conditions
                $update_valid_until_survey = SurveyAnswer::select('id', 'submit_date', 'valid_until')->withTrashed()
                    ->where('valid_until', '=', $survey_period->valid_to) 
                    ->get();

                // Loop through the results to update each valid_until
                foreach ($update_valid_until_survey as $survey) {
                    // Convert submit_date to a Carbon instance
                    $submitDate = Carbon::parse($survey->submit_date);

                    // Determine the new valid_until based on submit_date + duration
                    $newValidUntil = $submitDate->addDays($duration->duration);
                    
                    // Check if the calculated valid_until exceeds the survey period valid_to
                    if ($newValidUntil > $request['valid_to']) {
                        // If it exceeds, set valid_until to survey_period valid_to
                        $survey->valid_until = $request['valid_to'];
                    } else {
                        // Otherwise, use the calculated newValidUntil
                        $survey->valid_until = $newValidUntil;
                    }

                    // Save the updated survey
                    $survey->save();
 
                }
            }

            //update lang or extend durations
            $survey_period->update([
                "valid_from" => $request['valid_from'],
                "valid_to" => $request['valid_to'],
            ]);


            
            return GlobalFunction::response_function(Message::SURVEY_PERIOD_UPDATE);
        
    }
}
