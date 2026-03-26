<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterTenantController;
use App\Models\User;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Middleware\IdentifyTenant;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test-tenant', function () {
    return User::all();
});
//public
Route::post('/register-tenant', RegisterTenantController::class);
Route::post('/login', LoginController::class);


//requiers token

Route::middleware(['auth:sanctum', IdentifyTenant::class])->group(function () {
    // Project Routes
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects', [ProjectController::class, 'store']);
});