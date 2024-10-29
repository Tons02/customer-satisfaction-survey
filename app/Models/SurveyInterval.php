<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyInterval extends Model
{
    use HasFactory;

    protected $fillable = [
        'days',
        'is_active',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];
}
