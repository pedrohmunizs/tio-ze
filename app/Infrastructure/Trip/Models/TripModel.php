<?php

namespace App\Infrastructure\Trip\Models;

use App\Infrastructure\Driver\Models\DriverModel;
use App\Infrastructure\Route\Models\RouteModel;
use App\Infrastructure\Vehicle\Models\VehicleModel;
use Illuminate\Database\Eloquent\Model;

class TripModel extends Model
{
    protected $table = 'trips';

    protected $fillable = [
        'fk_route', 'fk_vehicle', 'fk_driver', 'date', 'type', 'status', 'started_at', 'completed_at'
    ];

    protected $casts = [
        'status' => 'string',
        'type' => 'string',
    ];

    protected $dates = [
        'started_at',
        'completed_at',
        'created_at',
        'updated_at',
    ];

    public function route()
    {
        return $this->belongsTo(RouteModel::class, 'fk_route');
    }

    public function vehicle()
    {
        return $this->belongsTo(VehicleModel::class, 'fk_vehicle');
    }

    public function driver()
    {
        return $this->belongsTo(DriverModel::class, 'fk_driver');
    }
}