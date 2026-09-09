<?php

namespace App\Infrastructure\School\Models;

use App\Infrastructure\Address\Models\AddressModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolModel extends Model
{
    use SoftDeletes;

    protected $table = 'schools';

    protected $fillable = [
        'name', 'phone', 'photo', 'status', 'fk_address'
    ];

    protected $casts = [
        'status' => 'string'
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
}