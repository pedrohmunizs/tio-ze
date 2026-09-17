<?php

namespace App\Infrastructure\TripStudent\Models;

use App\Infrastructure\Child\Models\ChildModel;
use App\Infrastructure\Trip\Models\TripModel;
use Illuminate\Database\Eloquent\Model;

class TripStudentModel extends Model
{
    protected $table = 'trip_students';

    protected $fillable = [
        'fk_trip', 'fk_student', 'pickup_time', 'dropoff_time', 'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'pickup_time',
        'dropoff_time',
    ];

    public function trip()
    {
        return $this->belongsTo(TripModel::class, 'fk_trip');
    }

    public function student()
    {
        return $this->belongsTo(ChildModel::class, 'fk_student');
    }
}