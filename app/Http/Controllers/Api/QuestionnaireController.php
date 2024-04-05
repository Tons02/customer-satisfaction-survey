<?php

namespace App\Http\Controllers\Api;

use App\Response\Message;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\QuestionnaireRequest;
use App\Http\Resources\QuestionnaireResource;

class QuestionnaireController extends Controller
{
    use ApiResponse;
    
    public function index(Request $request)
    {   
        $status = $request->query('status');
        
        $Questionnaire = Questionnaire::
        when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
        ->orderBy('created_at', 'desc')
        ->useFilters()
        ->dynamicPaginate();
        
        $is_empty = $Questionnaire->isEmpty();

        if ($is_empty) {
            return GlobalFunction::response_function(Message::NOT_FOUND, $Questionnaire);
        }
            QuestionnaireResource::collection($Questionnaire);
            return GlobalFunction::response_function(Message::QUESTIONNAIRE_DISPLAY, $Questionnaire);

    }

    public function store(QuestionnaireRequest $request)
    {   

        if (Questionnaire::count()){
            return GlobalFunction::response_function(Message::INVALID_ACTION,);
        }

        $CreateQuestionnaire = Questionnaire::create([
            "questionnaire" => $request->questionnaire,
        ]);

        return GlobalFunction::response_function(Message::QUESTIONNAIRE_SAVE, $CreateQuestionnaire);
        
    }

    public function update(QuestionnaireRequest $request, $id)
    {   
        $QuestionnaireId = Questionnaire::find($id);

        if (!$QuestionnaireId) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        $QuestionnaireId->update([
            "questionnaire" => $request->questionnaire,
        ]);
        
        return GlobalFunction::response_function(Message::QUESTIONNAIRE_UPDATE, $QuestionnaireId);
    }

    public function archived(Request $request, $id)
    {
        $Questionnaire = Questionnaire::withTrashed()->find($id);
        // return $role
        if (!$Questionnaire) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        
        if ($Questionnaire->deleted_at) {
            $Questionnaire->update([
                'is_active' => 1
            ]);
            $Questionnaire->restore();
            return GlobalFunction::response_function(Message::RESTORE_STATUS, $Questionnaire);
        }

        // if (User::where('Questionnaire_id', $id)->exists()) {
        //     return GlobalFunction::invalid(Message::QUESTIONNAIRE_ALREADY_USE, $Questionnaire);
        // }

        if (!$Questionnaire->deleted_at) {
            $Questionnaire->update([
                'is_active' => 0
            ]);
            $Questionnaire->delete();
            return GlobalFunction::response_function(Message::ARCHIVE_STATUS, $Questionnaire);

        } 
    }
}
