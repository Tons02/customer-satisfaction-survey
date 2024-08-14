<?php

namespace App\Models;

use App\Filters\ProvinceFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Province extends Model
{
    use HasFactory, softDeletes, Filterable;

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];

    
    protected string $default_filters = ProvinceFilters::class;
}
