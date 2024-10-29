<?php

namespace App\Http\Controllers\Api;

use App\Response\Message;
use Illuminate\Http\Request;
use App\Models\SurveyInterval;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use App\Http\Requests\SurveyIntervalRequest;

class SurveyIntervalController extends Controller
{
    public function index(Request $request){
        $status = $request->query('status');
        
        $SurveyInterval = SurveyInterval::
        first();
        
        if (is_null($SurveyInterval)) {
            return GlobalFunction::response_function(Message::NOT_FOUND, null);
        }
        
        return GlobalFunction::response_function(Message::SURVEY_INTERVAL_DISPLAY, $SurveyInterval);
    }   

    public function store(SurveyIntervalRequest $request) {

        if (SurveyInterval::count()){
            return GlobalFunction::response_function(Message::ALREADY_EXIST);
        }

        $create_survey_interval = SurveyInterval::create([
            "days" => $request->days,
        ]);

        return GlobalFunction::response_function(Message::SURVEY_INTERVAL_SAVE);
    }

    public function update(SurveyIntervalRequest $request, $id) {

        $survey_interval = SurveyInterval::find($id);

        if (!$survey_interval) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        
        $survey_interval->days = $request['days'];

        if (!$survey_interval->isDirty()) {
            return GlobalFunction::response_function(Message::NO_CHANGES);
        }

        $survey_interval->save();
        
        return GlobalFunction::response_function(Message::TRIGGER_UPDATE);

    }
}
