<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;
use Essa\APIToolKit\Traits\DateFilter;
use Essa\APIToolKit\Traits\TimeFilter;
use Illuminate\Database\Eloquent\Builder;

class QuestionAnswerFilters extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [];

    public function store($store)
    {
        if ($store !== null) {
            $this->builder->whereHas('survey', function ($query) use ($store) {
                $query->where('store_id', $store); // Assuming 'name' is the column in the stores table
            });
        }

        return $this; // Returning $this to allow method chaining
    }

}
