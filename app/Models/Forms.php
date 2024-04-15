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
        'is_active',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function sections()
    {
        return $this->belongsToMany(Sections::class, 'form_section',
        "form_id",
        "section_id",
        "id",
        "id"
    );
    }
    
}
