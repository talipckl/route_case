<?php

use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CustomRateLimit;

Route::get('/', [LocationController::class, 'index']);

Route::controller(LocationController::class)->group(function () {
    Route::prefix('location')->group(function () {
        Route::get('/list', 'index')
            ->middleware(['throttle:10,1'])
            ->name('location.index');
            
        Route::get('/create', 'create')
            ->name('location.create');
            
        Route::get('/{id}/edit', 'edit')
            ->name('location.edit');
            
        Route::post('/store', 'store')
            ->name('location.store');
            
        Route::put('/update/{id}', 'update')
            ->name('location.update');
            
        Route::delete('/{id}/destroy', 'destroy')
            ->name('location.destroy');
            
        Route::get('/{id}/show', 'show')
            ->name('location.show');
            
        Route::get('/calculate-route', 'calculateRoute')
            ->middleware([CustomRateLimit::class])
            ->name('location.calculateRoute');
    });
}); 