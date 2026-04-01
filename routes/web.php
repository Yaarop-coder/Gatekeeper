<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
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

    // Dashboard / Projects
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Tasks (Nested under projects)
    // This matches route('projects.tasks.store', $project) in your Blade file
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('projects.tasks.store');

    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Notifications
    Route::post('/notifications/read', function () {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    })->name('notifications.read');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::post('/tasks/{task}/comments', [App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
});
