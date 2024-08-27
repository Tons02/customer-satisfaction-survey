<?php

namespace App\Http\Controllers\Api;

use App\Response\Message;
use Illuminate\Http\Request;
use App\Models\QuestionAnswer;
use App\Functions\GlobalFunction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Resources\QuestionAnswerResource;

class QuestionAnswerController extends Controller
{
    use ApiResponse;
    
    public function index(Request $request){
        $type = $request->query('type');
        
        $from_date = $request->query('from_date') ?? '2023-06-11 13:38:07';
        $to_date = $request->query('to_date') ?? '2055-06-11 13:38:07';
        $reports = $request->query('reports') ?? 'updated_at';

        $QuestionAnswers = QuestionAnswer::
         with('survey.store')
         ->when($reports === 'updated_at', function($query) use ($from_date, $to_date) {
            $query->where('updated_at', '>=', $from_date)
            ->where('updated_at', '<=', $to_date);
        })
        ->when($type === "count", function ($query) {
            $query->select('question', 'answer', 'survey_id', DB::raw('count(*) as answer_count'))
                  ->groupBy('question', 'answer', 'survey_id');
        })
        ->when($type === "excel", function ($query) {
            $query->select('survey_id', 'question', 'answer')
            ->groupBy('survey_id', 'question', 'answer');
        })
        ->useFilters()
        ->dynamicPaginate(); // count here per question name base on survey_id
        
        $is_empty = $QuestionAnswers->isEmpty();

        if ($is_empty) {
            return GlobalFunction::response_function(Message::NOT_FOUND, $QuestionAnswers);
        }
        if ($type === "count") {
            $result = [];
            $questions = [];
        
            // Group by question
        foreach ($QuestionAnswers as $qa) {
            $answer = (is_null($qa->answer) || $qa->answer === '') ? "No Answer" : $qa->answer;
            $store_name = $qa->survey->store->name; 

            if (!isset($questions[$qa->question])) {
                $questions[$qa->question] = [];
            }
            $questions[$qa->question][] = [
                'store_name' => $store_name,
                'answer' => $answer,
                'count' => $qa->answer_count
            ];
        }

        // Convert grouped questions to the desired array format
        foreach ($questions as $question => $answers) {
            $result[] = [
                'question' => $question,
                'answers' => $answers
            ];
        }
        return GlobalFunction::response_function(Message::QUESTION_ANSWER_DISPLAY, $result);

        }

        //excel
        QuestionAnswerResource::collection($QuestionAnswers); 

       $groupedData = $QuestionAnswers->groupBy('survey_id')->map(function ($group) {
            return [
                'survey_id' => $group->first()->survey_id,
                'store_name' => $group->first()->survey->store->name ?? 'Store',
                'name' => [
                    'id' => $group->first()->survey->id,
                    'name' => $group->first()->survey->first_name . ' ' . $group->first()->survey->last_name,
                ],
                'questions' => $group->map(function ($item) {
                    return [
                        'question' => $item->question,
                        'answer' => $item->answer,
                    ];
                })->values() // Convert the collection to an array without keys
            ];
        })->values();

        return GlobalFunction::response_function(Message::QUESTION_ANSWER_DISPLAY, $groupedData);
    }
}
