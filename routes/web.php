<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Models\Task;
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
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::get('/projects/{project}/export', [ProjectController::class, 'exportPDF'])
    ->name('projects.export')
    ->middleware(['auth', 'can:view,project']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::patch('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::patch('tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');
    Route::get('/tasks/{task}/comments', function (Task $task) {
        return $task->comments()->with('user')->latest()->get();
    });
    // Comments
    Route::post('tasks/{task}/comments', [CommentController::class, 'store'])->name('tasks.comments.store');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Profile (Breeze Default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

require __DIR__.'/auth.php';
