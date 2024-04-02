<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Filters\UserFilters;
use Laravel\Sanctum\HasApiTokens;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, softDeletes, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'mobile_number',
        'gender',
        'age',
        'username',
        'role_id',
        'is_active',
        'created_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'role_id' => 'integer',
    ];

    protected string $default_filters = UserFilters::class;

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id')->withTrashed();
    }
}
