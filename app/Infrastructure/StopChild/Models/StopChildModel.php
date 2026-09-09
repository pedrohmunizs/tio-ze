<?php

namespace App\Infrastructure\StopChild\Models;

use App\Infrastructure\Child\Models\ChildModel;
use App\Infrastructure\Stop\Models\StopModel;
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

    public function stop()
    {
        return $this->belongsTo(StopModel::class, 'fk_stop');
    }

    public function child()
    {
        return $this->belongsTo(ChildModel::class, 'fk_child');
    }
}