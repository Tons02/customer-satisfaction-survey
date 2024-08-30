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
            $storeArray = explode(',', $store); // Convert the comma-separated string into an array

            $this->builder->whereHas('survey', function ($query) use ($storeArray) {
                $query->whereIn('store_id', $storeArray); // Use whereIn for multiple store IDs
            });
        }

        return $this; // Returning $this to allow method chaining
    }


}
