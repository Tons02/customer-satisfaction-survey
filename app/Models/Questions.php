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
        'is_active',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
    
    public function options()
    {
        return $this->belongsToMany(Option::class, 'question_option',
        "question_id",
        "option_id",
        "id",
        "id"
    );
}
}
