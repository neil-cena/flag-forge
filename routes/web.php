<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeatureFlagController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');

    Route::post('/projects/{project}/flags', [FeatureFlagController::class, 'store'])->name('flags.store');
    Route::patch('/projects/{project}/flags/{featureFlag}', [FeatureFlagController::class, 'update'])->name('flags.update');
    Route::get('/projects/{project}/flags/{featureFlag}', [FeatureFlagController::class, 'show'])->name('flags.show');
});

require __DIR__.'/auth.php';
