<?php

namespace App\Infrastructure\Stop\Models;

use App\Infrastructure\Address\Models\AddressModel;
use App\Infrastructure\Child\Models\ChildModel;
use App\Infrastructure\StopChild\Models\StopChildModel;
use Illuminate\Database\Eloquent\Model;

class StopModel extends Model
{
    protected $table = 'stops';

    protected $fillable = [
        'fk_route', 'fk_address', 'type', 'stop_order'
    ];

    protected $casts = [
        'type' => 'string'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function address()
    {
        return $this->belongsTo(AddressModel::class, 'fk_address');
    }

    public function stopChildren()
    {
        return $this->hasMany(StopChildModel::class, 'fk_stop');
    }

    public function student()
    {
        return $this->hasOneThrough(ChildModel::class, StopChildModel::class, 'fk_stop', 'id', 'id', 'fk_child');
    }

    public function children()
    {
        return $this->hasManyThrough(ChildModel::class, StopChildModel::class, 'fk_stop', 'id', 'id', 'fk_child');
    }

    public function child()
    {
        return $this->hasOneThrough(ChildModel::class, StopChildModel::class, 'fk_stop', 'id', 'id', 'fk_child');
    }
}