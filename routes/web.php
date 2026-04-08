<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirect the landing page to projects if logged in
Route::get('/', function () {
    return auth()->check() ? redirect('/projects') : view('welcome');
});

// The Breeze Dashboard
Route::get('/dashboard', function () {
    return redirect()->route('projects.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// --- GATEKEEPER CORE ROUTES ---
Route::middleware('auth')->group(function () {
    // Projects
    Route::resource('projects', ProjectController::class);

    // Tasks (Fixed the name here to match your Blade component)
    Route::post('projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::patch('tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');

    // Comments
    Route::post('tasks/{task}/comments', [CommentController::class, 'store'])->name('tasks.comments.store');

    // Profile (Breeze Default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
