<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class StoreNameFilters extends QueryFilters
{
    protected array $columnSearch = [
        "province_id",
        "name",
        "address",
    ];

    protected array $relationSearch = [
        'province' => ['name'],
    ];
}
