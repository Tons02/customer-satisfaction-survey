<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'survey_id',
        'security_code',
        'mobile_number',
        'title',
        'description',
        'sections',
        'is_active',
        'status',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];

    protected $casts = [
        'sections' => 'json',
        'is_active' => 'boolean',
        'status' => 'boolean'
    ];

    
    public function getDescriptionAttribute($value){
        return $value ?? "";
    }
}
