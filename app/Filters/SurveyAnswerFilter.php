<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;
use Illuminate\Database\Eloquent\Builder;

class SurveyAnswerFilter extends QueryFilters
{
    
    protected array $allowedSorts = [
        "updated_at",
        "created_at"
    ];

    protected array $columnSearch = [
        "id",
        "entry_code",
        "first_name",
        "middle_name",
        "last_name",
        "suffix",
        "mobile_number",
        "gender",
        "birthday",
        "voucher_code",
        "valid_until",
        "next_voucher_date",
    ];

    
    // public function claim($claim) {
    //     $this->builder->when($claim !== null, function($query) use ($claim) {
    //         $query->where('claim', $claim);
    //     });
    // }

}
