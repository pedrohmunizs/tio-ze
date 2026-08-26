<?php

namespace App\Infrastructure\Address\Models;

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
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->hasOne(UserModel::class, 'fk_address');
    }

    // public function children()
    // {
    //     return $this->hasOne(ChildModel::class, 'fk_address');
    // }

    // public function school()
    // {
    //     return $this->belongsTo(SchoolModel::class, 'fk_address');
    // }
}