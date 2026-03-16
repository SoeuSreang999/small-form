<?php

use App\Http\Controllers\Classes\SubjectsController;
use App\Http\Controllers\Classes\ClassesController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('classes')->as('classes.')->group(function () {
    Route::prefix('subject')->as('subject.')->group(function () {
        Route::get('/', [SubjectsController::class, 'index'])->name('index');
        Route::get('/create', [SubjectsController::class, 'create'])->name('create');
        Route::post('/store', [SubjectsController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [SubjectsController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [SubjectsController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [SubjectsController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('class')->as('class.')->group(function () {
        Route::get('/', [ClassesController::class, 'index'])->name('index');
        Route::get('/create', [ClassesController::class, 'create'])->name('create');
        Route::post('/store', [ClassesController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ClassesController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [ClassesController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ClassesController::class, 'destroy'])->name('destroy');
    });
});