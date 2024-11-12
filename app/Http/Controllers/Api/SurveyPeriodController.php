<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Response\Message;
use App\Models\FormHistory;
use App\Models\SurveyAnswer;
use App\Models\SurveyPeriod;
use Illuminate\Http\Request;
use App\Models\ReceiptNumber;
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
            $deleted_unanswered_survey = SurveyAnswer::onlyTrashed()
            ->where('created_at', '<', $request['valid_to'])
            ->pluck('id'); // Pluck the IDs to use them in the next query
        
            // Delete the related form history records
            $delete_unanswered_survey_history = FormHistory::whereIn('survey_id', $deleted_unanswered_survey)
                ->delete();
            
            // Finally, delete the surveys themselves
            SurveyAnswer::whereIn('id', $deleted_unanswered_survey)->forceDelete();
        }

        //update lang or extend durations
        $survey_period->update([
            "valid_from" => $request['valid_from'],
            "valid_to" => $request['valid_to'],
        ]);


        
        return GlobalFunction::response_function(Message::SURVEY_PERIOD_UPDATE);
        
    }
}
