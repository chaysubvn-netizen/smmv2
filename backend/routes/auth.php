<?php

use App\Http\Controllers\Auth\AppAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/install', [AppAuthController::class, 'install'])->name('auth.install');
Route::post('/install', [AppAuthController::class, 'DoInstall'])->name('auth.install.post');

Route::get('/login', [AppAuthController::class, 'login'])->name('auth.login');
Route::get('/register', [AppAuthController::class, 'register'])->name('auth.register');

Route::post('/login', [AppAuthController::class, 'DoLogin'])->name('auth.login.post');
Route::post('/register', [AppAuthController::class, 'DoRegister'])->name('auth.register.post');

// Google OAuth Routes
Route::get('/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
