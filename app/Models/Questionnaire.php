<?php

namespace App\Models;

use App\Filters\QuestionnaireFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Questionnaire extends Model
{
    use HasFactory, softDeletes, Filterable;
    protected $fillable = [
        'questionnaire',
        'is_active',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];

    protected string $default_filters = QuestionnaireFilters::class;

    protected $casts = [
        'questionnaire' => 'json',
        'is_active' => 'boolean'
    ];
}
