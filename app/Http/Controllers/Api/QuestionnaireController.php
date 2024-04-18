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
use App\Http\Resources\QuestionnaireResource;

class QuestionnaireController extends Controller
{
    
    public function index(Request $request)
    {   
        $status = $request->query('status');
        
        $Questionnaire = Forms::
        with('sections.questions')
        ->when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })

        ->orderBy('created_at', 'desc')
        ->get();
        
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
            "title" => $request->form['title'],
            "description" => $request->form['description'],
        ]);

        // Loop through sections
        foreach ($request->section as $sectionData) {
            // Create section
            $section = Sections::create([
                "section" => $sectionData['section'],
                "name" => $sectionData['name'],
                "description" => $sectionData['description'],
                "next_section" => $sectionData['next_section'],
            ]);

             $questionnaire->sections()->attach($section->id);

            // Loop through questions
            foreach ($sectionData['questions'] as $questionData) {
                // Create question
               $question = Questions::create([
                    "question" => $questionData['question'],
                    "description" => $questionData['description'],
                    "type" => $questionData['type'],
                    "required" => $questionData['required'],
                    "options" => $questionData['options']
                ]);
                
                $section->questions()->attach($question->id);

            }
        }

        return GlobalFunction::response_function(Message::QUESTIONNAIRE_SAVE, $questionnaire);
        
    }

    public function update(Request $request, $id)
    {   
        $questionnaire = Forms::find($id);

        if (!$questionnaire) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

         // Update the existing form
        $questionnaire->update([
            "title" => $request->form['title'],
            "description" => $request->form['description'],
        ]);

        // Loop through sections
        foreach ($request->section as $sectionData) {
            $section = Sections::updateOrCreate(
                // Find or create section by id
                ['id' => $sectionData['id']], 
                [
                    "section" => $sectionData['section'],
                    "name" => $sectionData['name'],
                    "description" => $sectionData['description'],
                    "next_section" => $sectionData['next_section'],
                ]
            );

            $questionnaire->sections()->syncWithoutDetaching($section->id);

            // Loop through questions
            foreach ($sectionData['questions'] as $questionData) {
        
                $question = Questions::updateOrCreate(
                    // Find or create question by id
                    ['id' => $questionData['id']],
                    [
                        "question" => $questionData['question'],
                        "description" => $questionData['description'],
                        "type" => $questionData['type'],
                        "required" => $questionData['required'],
                        "options" => $questionData['options']
                    ]
            );

            $section->questions()->syncWithoutDetaching($question->id);
           
        }

        }
       
        return GlobalFunction::response_function(Message::QUESTIONNAIRE_UPDATE, $questionnaire);
    }


}

