<?php

namespace App\Models;

use App\Models\Forms;
use App\Models\Questions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sections extends Model
{
    use HasFactory, softDeletes;
    protected $fillable = [
        'section',
        'name',
        'description',
        'next_section',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function questions()
    {
        return $this->belongsToMany(Questions::class, 'section_question',
        "section_id",
        "question_id",
        "id",
        "id");
    }
    
    
}
