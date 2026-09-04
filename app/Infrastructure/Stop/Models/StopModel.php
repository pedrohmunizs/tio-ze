<?php

namespace App\Infrastructure\Stop\Models;

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

    protected $hidden = [
        // Adicione os campos ocultos aqui
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relacionamentos
    // public function relacionamento()
    // {
    //     return $this->belongsTo(OutroModel::class, 'fk_outro_id');
    // }
}