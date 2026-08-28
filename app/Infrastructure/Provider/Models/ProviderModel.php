<?php

namespace App\Infrastructure\Provider\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderModel extends Model
{
    use SoftDeletes;

    protected $table = 'providers';

    protected $fillable = [
        'name', 'phone', 'description', 'status', 'rating', 'is_autonomous', 'fk_user', 'fk_address'
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
        'approved_at',
    ];

    // Relacionamentos
    // public function relacionamento()
    // {
    //     return $this->belongsTo(OutroModel::class, 'fk_outro_id');
    // }
}