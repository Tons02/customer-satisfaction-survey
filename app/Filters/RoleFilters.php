<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class RoleFilters extends QueryFilters
{
    protected array $allowedFilters = [
        "name",
        "access_permission",
    ];
    
    protected array $allowedSorts = [
        "updated_at",
        "created_at"
    ];

    protected array $columnSearch = [
        "name",
        "access_permission",
    ];
}
