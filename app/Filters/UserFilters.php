<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class UserFilters extends QueryFilters
{
    protected array $allowedFilters = [
        "id",
        "first_name",
        "middle_name",
        "last_name",
        "suffix",
        "gender",
        "age",
        "username",
    ];
    
    protected array $allowedSorts = [
        "updated_at",
        "created_at"
    ];

    protected array $relationSearch = [
        'role' => ['name'],
    ];

    protected array $columnSearch = [
        "id",
        "first_name",
        "middle_name",
        "last_name",
        "suffix",
        "gender",
        "age",
        "username",
    ];
}
