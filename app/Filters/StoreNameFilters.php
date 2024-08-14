<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class StoreNameFilters extends QueryFilters
{
    protected array $allowedFilters = [
        "province_id",
    ];

    protected array $columnSearch = [
        "province_id",
        "name",
        "address",
    ];

    protected array $allowedIncludes = ['province'];

    protected array $relationSearch = [
        'province' => ['name'],
    ];

    public function province($province)
    {
        $this->builder->where('province_id', $province);
    }
}
