<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectUpdateController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Health & version endpoint (no auth, useful for deployment checks)
Route::get('/api/version', function () {
    return response()->json([
        'version' => config('app.version'),
        'environment' => config('app.env'),
    ]);
});

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

use App\Http\Controllers\DashboardController;

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('projects', ProjectController::class)
    ->middleware(['auth', 'verified']);

Route::resource('projects.updates', ProjectUpdateController::class)
    ->shallow()
    ->middleware(['auth', 'verified'])
    ->except([ 'index', 'create', 'show']);

Route::resource('tags', TagController::class)
    ->except(['show'])
    ->middleware(['auth', 'verified']);

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('actions/clear-cache', [AdminDashboardController::class, 'clearCache'])->name('actions.clear-cache');
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::get('logs', [AdminAuditLogController::class, 'index'])->name('logs.index');
    });

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
