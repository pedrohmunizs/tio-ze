<?php

namespace App\Infrastructure\Address\Models;

use App\Infrastructure\Child\Models\ChildModel;
use App\Infrastructure\School\Models\SchoolModel;
use App\Infrastructure\User\Models\UserModel;
use Illuminate\Database\Eloquent\Model;

class AddressModel extends Model
{
    protected $table = 'addresses';

    protected $fillable = [
        'zip_code',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'latitude',
        'longitude',
        'location_type',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'location_type' => 'string'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->hasOne(UserModel::class, 'fk_address');
    }

    public function children()
    {
        return $this->hasOne(ChildModel::class, 'fk_address');
    }

    public function school()
    {
        return $this->belongsTo(SchoolModel::class, 'fk_address');
    }
}