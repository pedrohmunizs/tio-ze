<?php

namespace App\Providers;

use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Infrastructure\School\Repositories\EloquentSchoolRepository;
use App\Domain\School\Repositories\SchoolRepositoryInterface;
use App\Infrastructure\Child\Repositories\EloquentChildRepository;
use App\Domain\Child\Repositories\ChildRepositoryInterface;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Address\Repositories\EloquentAddressRepository;
use App\Infrastructure\User\Repositories\EloquentUserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        

        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        $this->app->bind(
            AddressRepositoryInterface::class,
            EloquentAddressRepository::class
        );

        $this->app->bind(
            ChildRepositoryInterface::class,
            EloquentChildRepository::class
        );
    
        
        $this->app->bind(
            SchoolRepositoryInterface::class,
            EloquentSchoolRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
