<?php

use App\Http\Controllers\Api\SensorApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FrutaApiController;
use App\Http\Controllers\Api\ProcesoApiController;
use App\Http\Controllers\Api\DispositivoApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ===== RUTAS PÚBLICAS (Sin autenticación) =====
Route::post('/login', [AuthController::class, 'login']);

// ===== RUTAS PROTEGIDAS (Requieren autenticación) =====
Route::middleware('auth:sanctum')->group(function () {
    
    // Datos del usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user()->load('rol');
    });
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // ----- FRUTAS -----
    Route::get('/frutas', [FrutaApiController::class, 'index']);
    Route::get('/frutas/{id}', [FrutaApiController::class, 'show']);
    Route::post('/frutas', [FrutaApiController::class, 'store']);
    Route::put('/frutas/{id}', [FrutaApiController::class, 'update']);
    Route::delete('/frutas/{id}', [FrutaApiController::class, 'destroy']);
    
    // ----- PROCESOS/CARGAS -----
    Route::get('/cargas', [ProcesoApiController::class, 'index']);
    Route::get('/cargas/{id}', [ProcesoApiController::class, 'show']);
    Route::post('/cargas', [ProcesoApiController::class, 'store']);
    Route::put('/cargas/{id}', [ProcesoApiController::class, 'update']);
    Route::delete('/cargas/{id}', [ProcesoApiController::class, 'destroy']);
    
    // ----- SENSORES -----
    Route::get('/sensores', [SensorApiController::class, 'index']);
    Route::get('/sensores/{id}', [SensorApiController::class, 'show']);
    
    // ----- LECTURAS -----
    Route::get('/readings/latest', [SensorApiController::class, 'latestReading']);
    Route::get('/readings', [SensorApiController::class, 'readings']);
    Route::post('/readings', [SensorApiController::class, 'store']);
    
    // ----- DISPOSITIVOS -----
    Route::get('/dispositivos', [DispositivoApiController::class, 'index']);
    Route::post('/comandos', [DispositivoApiController::class, 'sendCommand']);
});