<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;
use Illuminate\Database\Eloquent\Builder;

class SurveyAnswerFilter extends QueryFilters
{
    protected array $allowedFilters = [
        "store_id",
    ];

    protected array $allowedSorts = [
        "updated_at",
        "created_at"
    ];

    protected array $columnSearch = [
        "id",
        "receipt_number",
        "first_name",
        "middle_name",
        "last_name",
        "suffix",
        "mobile_number",
        "gender",
        "birthday",
        "voucher_code",
        "valid_until",
    ];

    
    public function store($store)
{
    if ($store !== null && is_string($store) && $store !== '') {
        // Convert the comma-separated string into an array
        $storeArray = explode(',', $store);
        
        // Use whereIn to filter results based on the array
        $this->builder->whereIn('store_id', $storeArray);
    }

    return $this; 
}

    
}
