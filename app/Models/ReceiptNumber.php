<?php

namespace App\Models;

use App\Filters\ReceiptNumberFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiptNumber extends Model
{
    use HasFactory, softDeletes, Filterable;
    protected $fillable = [
        'receipt_number',
        'contact_details',
        'store_id',
        'is_valid',
        'is_used',
        'expiration_date',
    ];

    protected $hidden = [
        "updated_at", 
        "deleted_at"
    ];

    protected string $default_filters = ReceiptNumberFilters::class;

    protected $casts = [
        'is_valid' => 'boolean',
        'is_used' => 'boolean',
        'is_active' => 'boolean'
    ];
}
