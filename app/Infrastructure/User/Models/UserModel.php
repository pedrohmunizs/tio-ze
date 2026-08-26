<?php

namespace App\Infrastructure\User\Models;

use App\Infrastructure\Address\Models\AddressModel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class UserModel extends Authenticatable
{
    use SoftDeletes, HasRoles, HasApiTokens;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'phone',
        'password',
        'status',
        'fk_address',
    ];

    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function address()
    {
        return $this->belongsTo(AddressModel::class, 'fk_address');
    }

    // public function children()
    // {
    //     return $this->hasMany(ChildModel::class, 'fk_parent');
    // }

    // public function provider()
    // {
    //     return $this->hasOne(ProviderModel::class, 'fk_user');
    // }

    // public function driver()
    // {
    //     return $this->hasOne(DriverModel::class, 'fk_user');
    // }

    // public function deviceTokens()
    // {
    //     return $this->hasMany(DeviceTokenModel::class, 'fk_user');
    // }

    // public function notifications()
    // {
    //     return $this->hasMany(NotificationModel::class, 'fk_user');
    // }
}