<?php

namespace App\Models;

use App\Models\Option;
use App\Models\Answers;
use App\Models\Sections;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Questions extends Model
{
    use HasFactory, softDeletes;
    protected $fillable = [
        'question',
        'description',
        'type',
        'required',
        'options',
        'is_active',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];

    protected $casts = [
        'options' => 'json',
        'required' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function getQuestionAttribute($value){
        return $value ?? "";
    }

    public function getDescriptionAttribute($value){
        return $value ?? "";
    }
    
}
