<?php

use App\Http\Controllers\Api\SensorApiController;
use App\Http\Controllers\Api\MetricasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ComandoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AlertaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider.
|
*/

// ===== AUTENTICACIÓN =====
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// Public API endpoints for ESP32 and Kotlin Mobile App
Route::post('/readings', [SensorApiController::class, 'store']);
Route::get('/metrics', [MetricasController::class, 'getMetrics']);
Route::get('/alerts', [MetricasController::class, 'getAlerts']);
// ===== COMANDOS PARA ESP32 (Nuevo) =====
Route::post('/comandos', [ComandoController::class, 'store']);
// ===== LECTURAS DE SENSORES =====
Route::get('/readings/latest', [App\Http\Controllers\Api\SensorApiController::class, 'getLatestReading']);
Route::get('/readings', [App\Http\Controllers\Api\SensorApiController::class, 'getReadings']);
// Endpoint para que el ESP32 consulte comandos pendientes
Route::get('/comandos/pendientes', [App\Http\Controllers\Api\ComandoController::class, 'getPendientes']);
// Endpoint para que el ESP32 marque un comando como ejecutado
Route::put('/comandos/{id}/ejecutado', [App\Http\Controllers\Api\ComandoController::class, 'marcarEjecutado']);
// Marcar comando como ejecutado
Route::post('/comandos/{id}/ejecutado', [App\Http\Controllers\Api\ComandoController::class, 'marcarEjecutado']);
// Marcar alerta como atendida
Route::post('/alerts/{id}/atender', [App\Http\Controllers\Api\AlertaController::class, 'marcarAtendida']);
// ===== ALERTAS =====
Route::get('/alerts', [App\Http\Controllers\Api\MetricasController::class, 'getAlerts']);