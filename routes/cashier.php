<?php


use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\ApiController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LowStocksController;
Route::middleware(['auth', 'restrict.access'])->group(function () {


    Route::get('verify-email', EmailVerificationPromptController::class)
    ->name('cashier.verification.notice');

Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('cashier.verification.verify');

Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('cashier.verification.send');

Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
    ->name('cashier.password.confirm');

Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('cashier.logout');

// Dashboard
 Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
 })->middleware(['auth', 'verified'])->name('cashier.dashboard');

// Inventory CRUD
Route::resource('/inventory', InventoryController::class)->names([
    'index'   => 'cashier.inventory.index',
    'create'  => 'cashier.inventory.create',
    'store'   => 'cashier.inventory.store',
    'show'    => 'cashier.inventory.show',
    'edit'    => 'cashier.inventory.edit',
    'update'  => 'cashier.inventory.update',
    'destroy' => 'cashier.inventory.destroy',
]);

// Low Stocks CRUD
Route::resource('/low-stocks', LowStocksController::class)->names([
    'index'   => 'cashier.low-stocks.index',
    'create'  => 'cashier.low-stocks.create',
    'store'   => 'cashier.low-stocks.store',
    'show'    => 'cashier.low-stocks.show',
    'edit'    => 'cashier.low-stocks.edit',
    'update'  => 'cashier.low-stocks.update',
    'destroy' => 'cashier.low-stocks.destroy',
]);
    //Api
    Route::get('/api/category', [ApiController::class, 'apiCategory'])->middleware(['auth'])->name('cashier.apiCategory.get');
    Route::get('/api/products', [ApiController::class, 'apiProduct'])->middleware(['auth'])->name('cashier.apiProduct.get');

});