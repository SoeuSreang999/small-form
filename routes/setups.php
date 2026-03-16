<?php

use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('setups')->as('setups.')->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('users.index');
        Route::get('/create', [UsersController::class, 'create'])->name('users.create');
        Route::get('/edit/{id}', [UsersController::class, 'edit'])->name('users.edit');
        Route::post('/edit/{id}', [UsersController::class, 'update'])->name('users.update');
        Route::delete('/delete/{id}', [UsersController::class, 'destroy'])->name('users.destroy');
    });
    
    Route::prefix('roles')->group(function () {
        // Route::get('/', [RoleController::class, 'index'])->name('setups.roles');
        // ...
    });
});
