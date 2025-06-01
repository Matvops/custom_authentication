<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function() {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('auth');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'storeUser'])->name('register.store');

    Route::get('/register_confimartion/{token}', [AuthController::class, 'registerConfirmation'])->name('register.confirmation');
});

Route::middleware('auth')->group(function() {
    Route::get('/', function() {
        echo "bão";
    })->name('home');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});