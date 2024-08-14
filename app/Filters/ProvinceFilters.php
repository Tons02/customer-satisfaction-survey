<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class ProvinceFilters extends QueryFilters
{
    protected array $columnSearch = [
        "name",
    ];
}
