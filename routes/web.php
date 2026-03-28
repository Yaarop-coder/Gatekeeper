<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\LoginController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Use the [Controller, 'method'] array syntax to fix the red highlights
Route::post('/login', [LoginController::class, 'login']);

// Authenticated Routes
Route::middleware(['web', 'auth'])->group(function () {
    
    // Projects Dashboard
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});