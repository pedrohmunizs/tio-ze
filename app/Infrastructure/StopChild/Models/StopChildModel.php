<?php

namespace App\Infrastructure\StopChild\Models;

use Illuminate\Database\Eloquent\Model;

class StopChildModel extends Model
{
    protected $table = 'stop_children';

    protected $fillable = [
        'fk_stop', 'fk_child', 'stop_order',
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