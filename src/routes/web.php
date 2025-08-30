<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController as WplController;

Route::middleware(['web'])
    ->prefix('wordpress-plus-laravel')
    ->name('wpl.')
    ->group(function (): void {
        Route::get('/', [WplController::class, 'index'])->name('index');
        Route::get('/create', [WplController::class, 'create'])->name('create');
        Route::post('/', [WplController::class, 'store'])->name('store');
        Route::get('/logs/{id}', [WplController::class, 'logs'])->whereNumber('id')->name('logs');

        Route::post('/delete', [WplController::class, 'delete'])->name('delete');
        Route::post('/generate-ssl', [WplController::class, 'generateSsl'])->name('generate-ssl');
});
