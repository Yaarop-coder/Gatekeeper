<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check() ? redirect()->route('projects.index') : view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // --- Projects ---
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // --- Tasks ---
    // Store task under project
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('projects.tasks.store');

    // Main Update (Used by the Task Drawer for description/title)
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');

    // Status & Assignment Updates
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::patch('/tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');

    // Delete Task
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // --- Comments ---
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->name('comments.store');

    // --- Notifications ---
    Route::post('/notifications/read', function () {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    })->name('notifications.read');

    // --- Auth ---
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
