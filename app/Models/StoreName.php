<?php

namespace App\Models;

use App\Filters\StoreNameFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreName extends Model
{
    use HasFactory, softDeletes, Filterable;

    protected $fillable = [
        'province_id',
        'name',
        'address',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];

    
    protected string $default_filters = StoreNameFilters::class;
}
