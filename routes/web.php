<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\IdentifyTenant;

// 1. Guest Routes (Anyone can see these)
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'loginView'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// 2. Protected Routes (Only logged-in users)
Route::middleware(['auth'])->group(function () {
    
    // NOW we apply the IdentifyTenant middleware inside the auth group
    Route::middleware([IdentifyTenant::class])->group(function () {
        Route::get('/projects', [ProjectController::class, 'index']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});