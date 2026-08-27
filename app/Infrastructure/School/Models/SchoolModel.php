<?php

namespace App\Infrastructure\School\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolModel extends Model
{
    use SoftDeletes;

    protected $table = 'schools';

    protected $fillable = [
        'name', 'phone', 'photo', 'status', 'fk_address'
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
        'deleted_at',
    ];

    // Relacionamentos
    // public function relacionamento()
    // {
    //     return $this->belongsTo(OutroModel::class, 'fk_outro_id');
    // }
}