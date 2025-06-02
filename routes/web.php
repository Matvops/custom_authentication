<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function() {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('auth');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'storeUser'])->name('register.store');
    Route::get('/register_confimartion/{token}', [AuthController::class, 'registerConfirmation'])->name('register.confirmation');
    Route::get('/reset_password', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('/reset_password', [AuthController::class, 'sendResetPasswordLink'])->name('password.send_link');
});

Route::middleware('auth')->group(function() {
    Route::get('/', [MainController::class, 'home'])->name('home');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'changePassword'])->name('password.update');
});