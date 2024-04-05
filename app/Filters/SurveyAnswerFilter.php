<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class SurveyAnswerFilter extends QueryFilters
{
    
    protected array $allowedFilters = [
        "id",
        "user_id",
        "questionnaire_answer",
    ];
    
    protected array $allowedSorts = [
        "updated_at",
        "created_at"
    ];

    protected array $relationSearch = [
        'user' => [ 
        "id",
        "first_name",
        "middle_name",
        "last_name",
        "suffix",
        "gender",
        "age",
        "username"],
    ];

    protected array $columnSearch = [
        "id",
        "user_id",
        "questionnaire_answer",
    ];
}
