<?php

namespace App\Http\Controllers\Api;

use App\Models\Forms;
use App\Models\Option;
use App\Models\Answers;
use App\Models\Sections;
use App\Models\Questions;
use App\Response\Message;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionnaireRequest;
use App\Http\Resources\QuestionnaireResource;

class QuestionnaireController extends Controller
{
    
    public function index(Request $request)
    {   
        $status = $request->query('status');
        
        $Questionnaire = Forms::get();
        
        $is_empty = $Questionnaire->isEmpty();

        if ($is_empty) {
            return GlobalFunction::response_function(Message::NOT_FOUND, $Questionnaire);
        }
             QuestionnaireResource::collection($Questionnaire);
            return GlobalFunction::response_function(Message::QUESTIONNAIRE_DISPLAY, $Questionnaire);

    }

    public function store(Request $request)
    {   
        if (Forms::count()){
            return GlobalFunction::response_function(Message::INVALID_ACTION);
        }

        $questionnaire = Forms::create([
            "title" => $request['title'],
            "description" => $request['description'],
            "sections" => $request['sections'],
        ]);

        return GlobalFunction::response_function(Message::QUESTIONNAIRE_SAVE, $questionnaire);
        
    }

    public function update(QuestionnaireRequest $request, $id)
    {   
        $questionnaire = Forms::find($id);

        if (!$questionnaire) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

         // Update the existing form
        $questionnaire->update([
            "title" => $request['title'],
            "description" => $request['description'],
            "sections" => $request['sections'],
        ]);

        return GlobalFunction::response_function(Message::QUESTIONNAIRE_UPDATE, $questionnaire);
    }


}

