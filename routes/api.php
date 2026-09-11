<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\User\UserController;

// Rotas públicas
Route::post('/v1/login', [AuthController::class, 'login']);
Route::post('/v1/register', [AuthController::class, 'register']);

// Rotas protegidas com Sanctum
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Rotas do User
    Route::apiResource('users', UserController::class);

    // Route::post('/register', [AuthController::class, 'register']);

    Route::group(['prefix' => 'children', 'namespace' => 'App\Http\Controllers\Api\V1\Child'], function(){
        Route::get('/',['uses' => 'ChildController@index', 'as' => 'children.index'] );
        Route::get('/{id}',['uses' => 'ChildController@show', 'as' => 'children.show'] );
        Route::get('/load',['uses' => 'ChildController@load', 'as' => 'children.load'] );
        Route::get('/create',['uses' => 'ChildController@create', 'as' => 'children.create'] );
        Route::post('/',['uses' => 'ChildController@store', 'as' => 'children.store'] );
        Route::put('/{id}',['uses' => 'ChildController@update', 'as' => 'children.update'] );
        Route::delete('/{id}',['uses' => 'ChildController@destroy', 'as' => 'children.destroy'] );
    });

    Route::group(['prefix' => 'schools', 'namespace' => 'App\Http\Controllers\Api\V1\School'], function(){
        Route::get('/',['uses' => 'SchoolController@index', 'as' => 'schools.index'] );
        Route::get('/{id}',['uses' => 'SchoolController@show', 'as' => 'schools.show'] );
        Route::get('/load',['uses' => 'SchoolController@load', 'as' => 'schools.load'] );
        Route::get('/create',['uses' => 'SchoolController@create', 'as' => 'schools.create'] );
        Route::post('/',['uses' => 'SchoolController@store', 'as' => 'schools.store'] );
        Route::put('/{id}',['uses' => 'SchoolController@update', 'as' => 'schools.update'] );
        Route::delete('/{id}',['uses' => 'SchoolController@destroy', 'as' => 'schools.destroy'] );
    });

    Route::controller(App\Http\Controllers\Api\V1\TransportRequest\TransportRequestController::class)->group(function () {
        Route::get('/transport-requests', 'index');
        Route::get('/transport-requests/{id}', 'show');
        Route::post('/transport-requests', 'store');
        Route::put('/transport-requests/{id}/response', 'respond');
        Route::delete('/transport-requests/{id}', 'destroy');
    });

    Route::group(['prefix' => 'routes', 'namespace' => 'App\Http\Controllers\Api\V1\Route'], function(){
        Route::get('/',['uses' => 'RouteController@index', 'as' => 'routes.index'] );
        Route::get('/{id}',['uses' => 'RouteController@show', 'as' => 'routes.show'] );
        Route::post('/',['uses' => 'RouteController@store', 'as' => 'routes.store'] );
        Route::put('/{id}/optimize',['uses' => 'RouteController@optimize', 'as' => 'routes.optimize'] );
        Route::delete('/{id}',['uses' => 'RouteController@destroy', 'as' => 'routes.destroy'] );
    });

    Route::group(['prefix' => 'vehicles', 'namespace' => 'App\Http\Controllers\Api\V1\Vehicle'], function(){
        Route::get('/',['uses' => 'VehicleController@index', 'as' => 'vehicles.index'] );
        Route::get('/{id}',['uses' => 'VehicleController@show', 'as' => 'vehicles.show'] );
        Route::post('/',['uses' => 'VehicleController@store', 'as' => 'vehicles.store'] );
        Route::put('/{id}/change-status',['uses' => 'VehicleController@changeStatus', 'as' => 'vehicles.change-status'] );
        Route::delete('/{id}',['uses' => 'VehicleController@destroy', 'as' => 'vehicles.destroy'] );
    });

    Route::group(['prefix' => 'vehicle-documents', 'namespace' => 'App\Http\Controllers\Api\V1\VehicleDocument'], function(){
        Route::get('/',['uses' => 'VehicleDocumentController@index', 'as' => 'vehicle-documents.index'] );
        Route::get('/{id}',['uses' => 'VehicleDocumentController@show', 'as' => 'vehicle-documents.show'] );
        Route::post('/',['uses' => 'VehicleDocumentController@store', 'as' => 'vehicle-documents.store'] );
        Route::put('/{id}/response',['uses' => 'VehicleDocumentController@respond', 'as' => 'vehicle-documents.respond'] );
        Route::delete('/{id}',['uses' => 'VehicleDocumentController@destroy', 'as' => 'vehicle-documents.destroy'] );
    });

    // DriverDocument Routes
    Route::group(['prefix' => 'driver-documents', 'namespace' => 'App\Http\Controllers\Api\V1\DriverDocument'], function(){
        Route::get('/',['uses' => 'DriverDocumentController@index', 'as' => 'driver-documents.index'] );
        Route::get('/{id}',['uses' => 'DriverDocumentController@show', 'as' => 'driver-documents.show'] );
        Route::post('/',['uses' => 'DriverDocumentController@store', 'as' => 'driver-documents.store'] );
        Route::put('/{id}/response',['uses' => 'DriverDocumentController@respond', 'as' => 'driver-documents.respond'] );
        Route::delete('/{id}',['uses' => 'DriverDocumentController@destroy', 'as' => 'driver-documents.destroy'] );
    });

    // Driver Routes
    Route::group(['prefix' => 'drivers', 'namespace' => 'App\Http\Controllers\Api\V1\Driver'], function(){
        Route::get('/',['uses' => 'DriverController@index', 'as' => 'drivers.index'] );
        Route::get('/{id}',['uses' => 'DriverController@show', 'as' => 'drivers.show'] );
        Route::post('/',['uses' => 'DriverController@store', 'as' => 'drivers.store'] );
        Route::put('/{id}/change-status',['uses' => 'DriverController@changeStatus', 'as' => 'drivers.change-status'] );
        Route::put('/{id}',['uses' => 'DriverController@update', 'as' => 'drivers.update'] );
        Route::delete('/{id}',['uses' => 'DriverController@destroy', 'as' => 'drivers.destroy'] );
    });
});

// Rota para CSRF (necessário para autenticação stateful)
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});
// School Routes
Route::prefix('v1')->group(function () {
    Route::controller(App\Http\Controllers\Api\V1\School\SchoolController::class)->group(function () {
        Route::get('/schools', 'index');
        Route::get('/schools/{id}', 'show');
        Route::post('/schools', 'store');
        Route::put('/schools/{id}', 'update');
        Route::delete('/schools/{id}', 'destroy');
    });
});
// Provider Routes
Route::prefix('v1')->group(function () {
    Route::controller(App\Http\Controllers\Api\V1\Provider\ProviderController::class)->group(function () {
        Route::get('/providers', 'index');
        Route::get('/providers/{id}', 'show');
        Route::post('/providers', 'store');
        Route::put('/providers/{id}', 'update');
        Route::delete('/providers/{id}', 'destroy');
    });
});
// Contract Routes
Route::prefix('v1')->group(function () {
    Route::controller(App\Http\Controllers\Api\V1\Contract\ContractController::class)->group(function () {
        Route::get('/contracts', 'index');
        Route::get('/contracts/{id}', 'show');
        Route::post('/contracts', 'store');
        Route::put('/contracts/{id}', 'update');
        Route::delete('/contracts/{id}', 'destroy');
    });
});
// Stop Routes
Route::prefix('v1')->group(function () {
    Route::controller(App\Http\Controllers\Api\V1\Stop\StopController::class)->group(function () {
        Route::get('/stops', 'index');
        Route::get('/stops/{id}', 'show');
        Route::post('/stops', 'store');
        Route::put('/stops/{id}', 'update');
        Route::delete('/stops/{id}', 'destroy');
    });
});
// StopChild Routes
Route::prefix('v1')->group(function () {
    Route::controller(App\Http\Controllers\Api\V1\StopChild\StopChildController::class)->group(function () {
        Route::get('/stopchildren', 'index');
        Route::get('/stopchildren/{id}', 'show');
        Route::post('/stopchildren', 'store');
        Route::put('/stopchildren/{id}', 'update');
        Route::delete('/stopchildren/{id}', 'destroy');
    });
});