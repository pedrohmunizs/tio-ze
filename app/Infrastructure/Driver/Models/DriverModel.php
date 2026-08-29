<?php

namespace App\Infrastructure\Driver\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverModel extends Model
{
    use SoftDeletes;

    protected $table = 'drivers';

    protected $fillable = [
        'license_number' ,
        'license_category',
        'license_valid_until',
        'fk_user',
        'fk_provider',
        'is_autonomous',
    ];

    protected $casts = [
        'status' => 'string'
    ];

    protected $hidden = [
        // Adicione os campos ocultos aqui
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // Relacionamentos
    // public function relacionamento()
    // {
    //     return $this->belongsTo(OutroModel::class, 'fk_outro_id');
    // }
}