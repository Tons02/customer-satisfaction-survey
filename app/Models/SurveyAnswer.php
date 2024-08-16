<?php

namespace App\Models;

use App\Models\User;
use App\Models\StoreName;
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
        'store_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'mobile_number',
        'mobile_number_verified',
        'gender',
        'birthday',
        'questionnaire_answer',
        'voucher_code',
        'valid_until',
        'next_voucher_date',
        'claim',
        'claim_by_user_id',
        'submit_date',
        'is_active',
    ];

    protected $hidden = [
        // "updated_at", 
        "deleted_at"
    ];

    protected string $default_filters = SurveyAnswerFilter::class;

    protected $casts = [
        'mobile_number_verified' => 'boolean',
        'questionnaire_answer' => 'json',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'claim_by_user_id')->withTrashed();
    }

    

    public function store()
    {
        return $this->belongsTo(StoreName::class, 'store_id')->withTrashed();
    }
    
}
