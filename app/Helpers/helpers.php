<?php

use App\Infrastructure\User\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Support\Str;

if (!function_exists('user')) {
    /**
     * Get the authenticated user
     * 
     * @return UserModel|null
     */
    function user(): ?UserModel
    {
        return auth()->user();
    }
}