<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class ReceiptNumberFilters extends QueryFilters
{
    protected array $allowedSorts = [
        "updated_at",
        "created_at"
    ];

    protected array $columnSearch = [
        "receipt_number",
        "contact_details",
        "store_id",
        "is_valid",
        "is_used",
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
