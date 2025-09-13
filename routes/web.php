<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectUpdateController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('projects', ProjectController::class)
    ->middleware(['auth', 'verified']);

Route::resource('projects.updates', ProjectUpdateController::class)
    ->shallow()
    ->middleware(['auth', 'verified'])
    ->except([ 'index', 'create', 'show']);

Route::resource('tags', TagController::class)
    ->except(['show'])
    ->middleware(['auth', 'verified']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
