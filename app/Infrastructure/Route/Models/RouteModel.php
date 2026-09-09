<?php

namespace App\Infrastructure\Route\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Infrastructure\School\Models\SchoolModel;
use App\Infrastructure\Provider\Models\ProviderModel;
use App\Infrastructure\Stop\Models\StopModel;
use App\Infrastructure\Trip\Models\TripModel;
use App\Infrastructure\Contract\Models\ContractModel;
use App\Infrastructure\Driver\Models\DriverModel;
use App\Infrastructure\TransportRequest\Models\TransportRequestModel;

class RouteModel extends Model
{
    use SoftDeletes;

    protected $table = 'routes';

    protected $fillable = [
        'fk_school',
        'fk_provider',
        'fk_driver',
        'name',
        'price',
        'going_time',
        'returning_time',
        'days_of_week',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'going_time' => 'string',
        'returning_time' => 'string',
        'days_of_week' => 'string',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function school()
    {
        return $this->belongsTo(SchoolModel::class, 'fk_school');
    }

    public function provider()
    {
        return $this->belongsTo(ProviderModel::class, 'fk_provider');
    }

    public function driver()
    {
        return $this->belongsTo(DriverModel::class, 'fk_driver');
    }

    // /**
    //  * Pontos de parada da rota
    //  */
    // public function stops()
    // {
    //     return $this->hasMany(StopModel::class, 'fk_route');
    // }

    // /**
    //  * Viagens geradas a partir desta rota
    //  */
    // public function trips()
    // {
    //     return $this->hasMany(TripModel::class, 'fk_route');
    // }

    // /**
    //  * Contratos vinculados a esta rota
    //  */
    // public function contracts()
    // {
    //     return $this->hasMany(ContractModel::class, 'fk_route');
    // }

    // /**
    //  * Solicitações de transporte para esta rota
    //  */
    // public function transportRequests()
    // {
    //     return $this->hasMany(TransportRequestModel::class, 'fk_route');
    // }

    // 👇 SCOPES (consultas comuns)

    /**
     * Scope para rotas ativas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para rotas inativas
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope para rotas de um provider específico
     */
    public function scopeByProvider($query, int $providerId)
    {
        return $query->where('fk_provider', $providerId);
    }

    /**
     * Scope para rotas de uma escola específica
     */
    public function scopeBySchool($query, int $schoolId)
    {
        return $query->where('fk_school', $schoolId);
    }

    /**
     * Scope para rotas com dias específicos
     */
    public function scopeByDay($query, string $day)
    {
        return $query->where('days_of_week', 'LIKE', "%{$day}%");
    }

    // 👇 ACCESSORS (para formatar dados ao recuperar)

    /**
     * Formata o preço
     */
    public function getPriceFormattedAttribute(): string
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }

    /**
     * Retorna os dias da semana como array
     */
    public function getDaysOfWeekArrayAttribute(): array
    {
        return explode(',', $this->days_of_week);
    }

    /**
     * Retorna os dias da semana com nomes
     */
    public function getDaysOfWeekLabelsAttribute(): array
    {
        $daysMap = [
            'MON' => 'Segunda',
            'TUE' => 'Terça',
            'WED' => 'Quarta',
            'THU' => 'Quinta',
            'FRI' => 'Sexta',
            'SAT' => 'Sábado',
            'SUN' => 'Domingo',
        ];

        $days = explode(',', $this->days_of_week);
        return array_map(fn($day) => $daysMap[$day] ?? $day, $days);
    }

    /**
     * Status com label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'Ativa' : 'Inativa';
    }

    // 👇 MUTATORS (para formatar dados ao salvar)

    /**
     * Garante que days_of_week seja sempre uma string
     */
    public function setDaysOfWeekAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['days_of_week'] = implode(',', $value);
        } else {
            $this->attributes['days_of_week'] = $value;
        }
    }

    /**
     * Garante que price seja float
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = (float) $value;
    }
}