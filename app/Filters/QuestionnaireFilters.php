<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class QuestionnaireFilters extends QueryFilters
{
    protected array $allowedFilters = [
        "questionnaire",
    ];
    
    protected array $allowedSorts = [
        "updated_at",
        "created_at"
    ];

    protected array $columnSearch = [
        "questionnaire",
    ];
}
