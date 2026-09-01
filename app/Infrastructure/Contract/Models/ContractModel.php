<?php

namespace App\Infrastructure\Contract\Models;

use Illuminate\Database\Eloquent\Model;

class ContractModel extends Model
{
    protected $table = 'contracts';

    protected $fillable = [
        'start_date', 'end_date', 'status', 'fk_student', 'fk_route', 'fk_provider', 'fk_transport_request'
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