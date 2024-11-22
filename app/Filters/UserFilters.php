<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class UserFilters extends QueryFilters
{
    protected array $allowedFilters = [
        "id_prefix",
        "id_no",
        "first_name",
        "middle_name",
        "last_name",
        "contact_details",
        "username",
        "created_at", 
    ];
    
    protected array $allowedSorts = [
        "updated_at",
        "created_at"
    ];

    protected array $relationSearch = [
        'role' => ['name'],
    ];

    protected array $columnSearch = [
        "id_prefix",
        "id_no",
        "first_name",
        "middle_name",
        "last_name",
        "contact_details",
        "username",
        "created_at", 
    ];


    public function user_type($user_type)
    {
        if ($user_type !== null) {
            $this->builder->where('user_type', $user_type);
        }
    
        return $this; // Returning $this to allow method chaining
    }
}
