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
        $store = $request->query('store');

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
        
        // if ($type === "count") {
        //     $result = [];
        //     $stores = [];
            
        //     // Group by store and then by question
        //     foreach ($QuestionAnswers as $qa) {
        //         $answer = (is_null($qa->answer) || $qa->answer === '') ? "No Answer" : $qa->answer;
        //         $store_name = $qa->survey->store->name;
        //         $question = $qa->question;
        
        //         // Ensure the store key exists
        //         if (!isset($stores[$store_name])) {
        //             $stores[$store_name] = [
        //                 'store_name' => $store_name,
        //                 'questions' => []
        //             ];
        //         }
        
        //         // Ensure the question key exists within the store
        //         if (!isset($stores[$store_name]['questions'][$question])) {
        //             $stores[$store_name]['questions'][$question] = [
        //                 'question' => $question,
        //                 'answers' => []
        //             ];
        //         }
        
        //         // Find if the answer already exists in the answers array
        //         $answerFound = false;
        //         foreach ($stores[$store_name]['questions'][$question]['answers'] as &$existingAnswer) {
        //             if ($existingAnswer['answer'] === $answer) {
        //                 $existingAnswer['count'] += $qa->answer_count;
        //                 $answerFound = true;
        //                 break;
        //             }
        //         }
        
        //         // If the answer was not found, add it to the array
        //         if (!$answerFound) {
        //             $stores[$store_name]['questions'][$question]['answers'][] = [
        //                 'answer' => $answer,
        //                 'count' => $qa->answer_count
        //             ];
        //         }
        //     }
        
        //     // Convert the associative array to an indexed array
        //     $result = [];
        //     foreach ($stores as $store) {
        //         $store['questions'] = array_values($store['questions']); // Convert questions to indexed array
        //         $result[] = $store;
        //     }
        
        //     // Wrap the result array in a 'data' key
        //     $responseData = [
        //         'data' => $result
        //     ];
        
        //     return GlobalFunction::response_function(Message::QUESTION_ANSWER_DISPLAY, $responseData);
        // }

        if ($type === "count") {
            $result = [];
            $stores = [];
        
            // Group by store and then by question
            foreach ($QuestionAnswers as $qa) {
                $answer = (is_null($qa->answer) || $qa->answer === '') ? "No Answer" : $qa->answer;
                
                // If $store is provided, use the actual store name, otherwise use "All stores"\
                 if ($store != null) {
                $store_name = $qa->survey->store->name; // Use the actual store name
                } else {
                    $store_name = "All store"; // Use "All stores" if no specific store is provided
                }
                
                $question = $qa->question;
        
                // Ensure the store key exists
                if (!isset($stores[$store_name])) {
                    $stores[$store_name] = [
                        'store_name' => $store_name,
                        'questions' => []
                    ];
                }
        
                // Ensure the question key exists within the store
                if (!isset($stores[$store_name]['questions'][$question])) {
                    $stores[$store_name]['questions'][$question] = [
                        'question' => $question,
                        'answers' => []
                    ];
                }
        
                // Find if the answer already exists in the answers array
                $answerFound = false;
                foreach ($stores[$store_name]['questions'][$question]['answers'] as &$existingAnswer) {
                    if ($existingAnswer['answer'] === $answer) {
                        $existingAnswer['count'] += $qa->answer_count;
                        $answerFound = true;
                        break;
                    }
                }
        
                // If the answer was not found, add it to the array
                if (!$answerFound) {
                    $stores[$store_name]['questions'][$question]['answers'][] = [
                        'answer' => $answer,
                        'count' => $qa->answer_count
                    ];
                }
            }
        
            // Convert the associative array to an indexed array
            $result = [];
            foreach ($stores as $store) {
                $store['questions'] = array_values($store['questions']); // Convert questions to indexed array
                $result[] = $store;
            }
        
            // Wrap the result array in a 'data' key
            $responseData = [
                'data' => $result
            ];
        
            return GlobalFunction::response_function(Message::QUESTION_ANSWER_DISPLAY, $responseData);
        } else {
            // Handle other cases if needed
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

        $response = [
                'data' => $groupedData
        ];

        return GlobalFunction::response_function(Message::QUESTION_ANSWER_DISPLAY, $response);
    }
}
