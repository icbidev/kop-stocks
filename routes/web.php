<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\NotificationController;


// Home
Route::get('/', function () {
    return Inertia::render('Welcome');
})->middleware(['guest'])->name('home');

// Dashboard (requires auth)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->middleware(['auth', 'verified'])->name('notifications.destroy');
// Group routes that need authentication
Route::middleware(['auth', 'verified'])->group(function () {
    




});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
