<?php

namespace App\Application\Route\DTOs;

use Illuminate\Http\Request;

class CreateRouteData
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly string $going_time,
        public readonly string $returning_time,
        public readonly array $days_of_week,
        public readonly int $school_id,
        public readonly ?int $driver_id = null,
        public readonly string $status = 'active',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            price: (float) $request->input('price'),
            going_time: $request->input('going_time'),
            returning_time: $request->input('returning_time'),
            days_of_week: $request->input('days_of_week', []),
            school_id: (int) $request->input('fk_school'),
            driver_id: $request->input('fk_driver') ? (int) $request->input('fk_driver') : null,
            status: $request->input('status', 'active'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'going_time' => $this->going_time,
            'returning_time' => $this->returning_time,
            'days_of_week' => is_array($this->days_of_week) ? implode(',', $this->days_of_week) : $this->days_of_week,
            'fk_school' => $this->school_id,
            'fk_driver' => $this->driver_id,
            'status' => $this->status,
        ];
    }

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'going_time' => 'required|date_format:H:i',
            'returning_time' => 'required|date_format:H:i|after:going_time',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'in:MON,TUE,WED,THU,FRI,SAT,SUN',
            'fk_school' => 'required|exists:schools,id',
            'fk_driver' => 'nullable|exists:drivers,id',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome da rota é obrigatório',
            'price.required' => 'O preço é obrigatório',
            'price.numeric' => 'O preço deve ser um número',
            'going_time.required' => 'O horário de ida é obrigatório',
            'returning_time.required' => 'O horário de volta é obrigatório',
            'returning_time.after' => 'O horário de volta deve ser após o horário de ida',
            'days_of_week.required' => 'Selecione pelo menos um dia da semana',
            'fk_school.exists' => 'Escola não encontrada',
            'fk_driver.exists' => 'Motorista não encontrado',
        ];
    }
}