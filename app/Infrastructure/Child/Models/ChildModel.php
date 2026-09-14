<?php

namespace App\Infrastructure\Child\Models;

use App\Infrastructure\Address\Models\AddressModel;
use App\Infrastructure\Route\Models\RouteModel;
use App\Infrastructure\School\Models\SchoolModel;
use App\Infrastructure\Stop\Models\StopModel;
use App\Infrastructure\StopChild\Models\StopChildModel;
use App\Infrastructure\TransportRequest\Models\TransportRequestModel;
use App\Infrastructure\User\Models\UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChildModel extends Model
{
    use SoftDeletes;

    protected $table = 'children';

    protected $fillable = [
        'name', 'phone', 'status', 'grade', 'fk_parent', 'fk_address', 'fk_school'
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

    public function parent()
    {
        return $this->belongsTo(UserModel::class, 'fk_parent');
    }

    public function school()
    {
        return $this->belongsTo(SchoolModel::class, 'fk_school');
    }

    public function stopChildren()
    {
        return $this->hasMany(StopChildModel::class, 'fk_child');
    }

    public function stops()
    {
        return $this->belongsToMany(StopModel::class, 'stop_children', 'fk_child', 'fk_stop')->withPivot('stop_order')->withTimestamps();
    }

    public function routes()
    {
        return $this->hasManyThrough(RouteModel::class, StopModel::class, 'fk_route', 'id', 'id', 'id');
    }
}