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

    Route::controller(App\Http\Controllers\Api\V1\Route\RouteController::class)->group(function () {
        Route::get('/routes', 'index');
        Route::get('/routes/{id}', 'show');
        Route::post('/routes', 'store');
        Route::put('/routes/{id}', 'update');
        Route::delete('/routes/{id}', 'destroy');
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
// Driver Routes
Route::prefix('v1')->group(function () {
    Route::controller(App\Http\Controllers\Api\V1\Driver\DriverController::class)->group(function () {
        Route::get('/drivers', 'index');
        Route::get('/drivers/{id}', 'show');
        Route::post('/drivers', 'store');
        Route::put('/drivers/{id}', 'update');
        Route::delete('/drivers/{id}', 'destroy');
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