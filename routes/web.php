<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\FrutaController;
use App\Http\Controllers\ProcesoController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Protected Routes (Session Auth Manual)
Route::middleware(['auth.manual'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Reading History
    Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');

    // Fruits CRUD
    Route::prefix('frutas')->name('frutas.')->group(function () {
        Route::get('/', [FrutaController::class, 'index'])->name('index');
        Route::get('/crear', [FrutaController::class, 'create'])->name('create');
        Route::post('/', [FrutaController::class, 'store'])->name('store');
        Route::get('/{id}/editar', [FrutaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [FrutaController::class, 'update'])->name('update');
        Route::delete('/{id}', [FrutaController::class, 'destroy'])->name('destroy');
    });

    // Dehydration Processes CRUD/Flow
    Route::prefix('procesos')->name('procesos.')->group(function () {
        Route::get('/', [ProcesoController::class, 'index'])->name('index');
        Route::get('/iniciar', [ProcesoController::class, 'create'])->name('create');
        Route::post('/', [ProcesoController::class, 'store'])->name('store');
        Route::get('/{id}/finalizar', [ProcesoController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProcesoController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProcesoController::class, 'destroy'])->name('destroy');
    });

    // Logout
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});
