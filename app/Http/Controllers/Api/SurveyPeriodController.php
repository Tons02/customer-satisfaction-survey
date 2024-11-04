<?php

namespace App\Http\Controllers\Api;

use App\Response\Message;
use App\Models\SurveyPeriod;
use Illuminate\Http\Request;
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

        if (!$survey_period) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        
        $survey_period->valid_from = $request['valid_from'];
        $survey_period->valid_to = $request['valid_to'];

        if (!$survey_period->isDirty()) {
            return GlobalFunction::response_function(Message::NO_CHANGES);
        }

        $survey_period->save();
        
        return GlobalFunction::response_function(Message::SURVEY_PERIOD_UPDATE);
        
    }
}
