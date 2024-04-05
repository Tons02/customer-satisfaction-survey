<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class VoucherValidityFilter extends QueryFilters
{
    protected array $allowedFilters = [
        "name",
        "duration",
    ];
    
    protected array $allowedSorts = [
        "updated_at",
        "created_at"
    ];

    protected array $columnSearch = [
        "name",
        "duration",
    ];
}
