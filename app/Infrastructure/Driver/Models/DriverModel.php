<?php

namespace App\Infrastructure\Driver\Models;

use App\Infrastructure\Route\Models\RouteModel;
use App\Infrastructure\User\Models\UserModel;
use Illuminate\Database\Eloquent\Model;

class DriverModel extends Model
{
    protected $table = 'drivers';

    protected $fillable = [
        'license_number' ,
        'license_category',
        'license_valid_until',
        'fk_user',
        'fk_provider',
        'is_autonomous',
        'status',
    ];

    protected $casts = [
        'status' => 'string'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'license_valid_until',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'fk_user');
    }

    public function routes()
    {
        return $this->hasMany(RouteModel::class, 'fk_driver');
    }
}