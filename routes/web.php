<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ProfileController;

// Home route (redirects to dashboard)
Route::get('/', function () {
    return redirect('/dashboard');
})->name('home'); // Fixed: name goes on the route

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('lang/{lang}', function($lang) {
    if (in_array($lang, ['en', 'kh'])) {
        session(['locale' => $lang]);
    }
    return redirect()->back();
})->name('lang.switch');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

require __DIR__.'/auth.php';
require __DIR__.'/setups.php';
require __DIR__.'/classes.php';
require __DIR__.'/form.php';
