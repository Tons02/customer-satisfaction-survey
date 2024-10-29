<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'valid_from',
        'valid_to',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];
}
