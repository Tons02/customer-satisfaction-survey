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
}
