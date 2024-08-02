<?php

namespace App\Models;

use App\Models\SurveyAnswer;
use App\Filters\QuestionAnswerFilters;
use Illuminate\Database\Eloquent\Model;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionAnswer extends Model
{
    use HasFactory, softDeletes, Filterable;

    protected $fillable = [
        'survey_id',
        'question_type',
        'question',
        'answer',
    ];

    protected $casts = [
        'answer' => 'json',
    ];

    protected string $default_filters = QuestionAnswerFilters::class;

    public function survey()
    {
        return $this->belongsTo(SurveyAnswer::class, 'survey_id');
    }
}
