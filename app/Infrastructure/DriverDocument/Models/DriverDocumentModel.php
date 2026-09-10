<?php

namespace App\Infrastructure\DriverDocument\Models;

use App\Infrastructure\Driver\Models\DriverModel;
use Illuminate\Database\Eloquent\Model;

class DriverDocumentModel extends Model
{
    protected $table = 'driver_documents';

    protected $fillable = [
        'file_path', 'fk_driver', 'status', 'type', 'valid_until'
    ];

    protected $casts = [
        'status' => 'string',
        'type' => 'string',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function driver()
    {
        return $this->belongsTo(DriverModel::class, 'fk_driver');
    }
}