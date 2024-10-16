<?php

namespace App\Models;

use App\Filters\TriggerSetUpFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class TriggerSetUp extends Model
{
    use HasFactory, softDeletes, Filterable;
        protected $guarded = [
            
        ];

        protected $hidden = [
            "updated_at", 
            "deleted_at"
        ];

        protected string $default_filters = TriggerSetUpFilters::class;


}
