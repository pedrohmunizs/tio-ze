<?php

namespace App\Application\User\DTOs;

use Illuminate\Http\Request;
use App\Domain\User\Enums\UserStatus;

class UpdateUserStatusData
{
    public function __construct(
        public readonly string $status,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            status: $request->input('status'),
        );
    }

    public function validate(): array
    {
        return [
            'status' => 'required|in:' . implode(',', UserStatus::toArray()),
        ];
    }

    public function getStatus(): UserStatus
    {
        return UserStatus::from($this->status);
    }
}