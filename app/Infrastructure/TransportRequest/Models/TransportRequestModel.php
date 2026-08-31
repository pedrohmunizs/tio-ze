<?php

namespace App\Infrastructure\TransportRequest\Models;

use App\Infrastructure\Child\Models\ChildModel;
use App\Infrastructure\Provider\Models\ProviderModel;
use App\Infrastructure\Route\Models\RouteModel;
use Illuminate\Database\Eloquent\Model;

class TransportRequestModel extends Model
{
    protected $table = 'transport_requests';

    protected $fillable = [
        'message', 'status', 'fk_student', 'fk_route', 'fk_provider'
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

    public function provider()
    {
        return $this->belongsTo(ProviderModel::class, 'fk_provider');
    }

    public function route()
    {
        return $this->belongsTo(RouteModel::class, 'fk_route');
    }

    public function student()
    {
        return $this->belongsTo(ChildModel::class, 'fk_student');
    }
}