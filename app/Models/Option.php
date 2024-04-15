<?php

namespace App\Models;

use App\Models\Questions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Option extends Model
{
    use HasFactory, softDeletes;
    protected $fillable = [
        'option',
        'next_section',
        'is_active',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];

    protected $casts = [
        'option' => 'json',
        'is_active' => 'boolean'
    ];

    public function options()
    {
        return $this->belongsToMany(Questions::class, 'question_option',
        "question_id",
        "option_id",
        "id",
        "id"
    );
    }
    
}
