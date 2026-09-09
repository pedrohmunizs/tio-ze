<?php

namespace App\Infrastructure\Vehicle\Models;

use App\Infrastructure\Provider\Models\ProviderModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleModel extends Model
{
    use SoftDeletes;

    protected $table = 'vehicles';

    protected $fillable = [
        'brand', 'model', 'plate', 'year', 'capacity', 'photo', 'fk_provider', 'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function provider()
    {
        return $this->belongsTo(ProviderModel::class, 'fk_provider');
    }
}