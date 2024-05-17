<?php

namespace App\Models;

use App\Models\User;
use App\Filters\SurveyAnswerFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SurveyAnswer extends Model
{
    use HasFactory, softDeletes, Filterable;
    
    protected $fillable = [ 
        'entry_code',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'mobile_number',
        'mobile_number_verified',
        'gender',
        'age',
        'questionnaire_answer',
        'voucher_code',
        'valid_until',
        'next_voucher_date',
        'claim',
        'done',
        'is_active',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];

    protected string $default_filters = SurveyAnswerFilter::class;

    protected $casts = [
        'mobile_number_verified' => 'boolean',
        'questionnaire_answer' => 'json',
        'done' => 'boolean',
        'is_active' => 'boolean'
    ];
    
}
