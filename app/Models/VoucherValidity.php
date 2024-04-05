<?php

namespace App\Models;

use App\Filters\VoucherValidityFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherValidity extends Model
{
    use HasFactory, softDeletes, Filterable;

    protected $fillable = [
        'name',
        'duration',
        'is_active',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];

    protected string $default_filters = VoucherValidityFilter::class;

    protected $casts = [
        'duration' => 'integer',
        'is_active' => 'boolean'
    ];
}
