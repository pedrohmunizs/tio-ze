<?php

namespace App\Providers;

use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Infrastructure\Vehicle\Repositories\EloquentVehicleRepository;
use App\Domain\Vehicle\Repositories\VehicleRepositoryInterface;
use App\Infrastructure\StopChild\Repositories\EloquentStopChildRepository;
use App\Domain\StopChild\Repositories\StopChildRepositoryInterface;
use App\Infrastructure\Stop\Repositories\EloquentStopRepository;
use App\Domain\Stop\Repositories\StopRepositoryInterface;
use App\Infrastructure\Contract\Repositories\EloquentContractRepository;
use App\Domain\Contract\Repositories\ContractRepositoryInterface;
use App\Infrastructure\TransportRequest\Repositories\EloquentTransportRequestRepository;
use App\Domain\TransportRequest\Repositories\TransportRequestRepositoryInterface;
use App\Infrastructure\Route\Repositories\EloquentRouteRepository;
use App\Domain\Route\Repositories\RouteRepositoryInterface;
use App\Infrastructure\Driver\Repositories\EloquentDriverRepository;
use App\Domain\Driver\Repositories\DriverRepositoryInterface;
use App\Infrastructure\Provider\Repositories\EloquentProviderRepository;
use App\Domain\Provider\Repositories\ProviderRepositoryInterface;
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

        $this->app->bind(
            ProviderRepositoryInterface::class,
            EloquentProviderRepository::class
        );

        $this->app->bind(
            DriverRepositoryInterface::class,
            EloquentDriverRepository::class
        );

        $this->app->bind(
            RouteRepositoryInterface::class,
            EloquentRouteRepository::class
        );

        $this->app->bind(
            TransportRequestRepositoryInterface::class,
            EloquentTransportRequestRepository::class
        );
    
        
        $this->app->bind(
            ContractRepositoryInterface::class,
            EloquentContractRepository::class
        );
    
        
        $this->app->bind(
            StopRepositoryInterface::class,
            EloquentStopRepository::class
        );
    
        
        $this->app->bind(
            StopChildRepositoryInterface::class,
            EloquentStopChildRepository::class
        );
    
        
        $this->app->bind(
            VehicleRepositoryInterface::class,
            EloquentVehicleRepository::class
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
