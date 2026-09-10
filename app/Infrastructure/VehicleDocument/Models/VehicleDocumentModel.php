<?php

namespace App\Infrastructure\VehicleDocument\Models;

use App\Infrastructure\Vehicle\Models\VehicleModel;
use Illuminate\Database\Eloquent\Model;

class VehicleDocumentModel extends Model
{
    protected $table = 'vehicle_documents';

    protected $fillable = [
        'file_path', 'fk_vehicle', 'status', 'type'
    ];

    protected $casts = [
        'status' => 'string',
        'type' => 'string',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function vehicle()
    {
        return $this->belongsTo(VehicleModel::class, 'fk_vehicle');
    }
}