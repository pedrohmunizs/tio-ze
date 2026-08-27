<?php

namespace App\Infrastructure\Child\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChildModel extends Model
{
    use SoftDeletes;

    protected $table = 'children';

    protected $fillable = [
        'name', 'phone', 'status', 'grade', 'fk_parent', 'fk_address', 'fk_school'
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