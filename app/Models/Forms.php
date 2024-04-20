<?php

namespace App\Models;

use App\Models\Sections;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Forms extends Model
{
    use HasFactory, softDeletes;
    protected $fillable = [
        'title',
        'description',
        'sections',
        'is_active',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];

    protected $casts = [
        'sections' => 'json',
        'is_active' => 'boolean'
    ];

    
    public function getDescriptionAttribute($value){
        return $value ?? "";
    }

}
