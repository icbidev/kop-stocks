<?php


use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LowStocksController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\SupplierController;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\ApiController;
use App\Http\Controllers\ProductController;
Route::middleware(['auth', 'restrict.access'])->group(function () {

    Route::get('verify-email', EmailVerificationPromptController::class)
    ->name('manager.verification.notice');

Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('manager.verification.verify');

Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('manager.verification.send');

Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
    ->name('manager.password.confirm');

Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('manager.logout');


    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('manager.dashboard');

// Inventory CRUD
Route::resource('/inventory', InventoryController::class)->names([
    'index'   => 'manager.inventory.index',
    'create'  => 'manager.inventory.create',
    'store'   => 'manager.inventory.store',
    'show'    => 'manager.inventory.show',
    'edit'    => 'manager.inventory.edit',
    'update'  => 'manager.inventory.update',
    'destroy' => 'manager.inventory.destroy',
]);

// Low Stocks CRUD
Route::resource('/low-stocks', LowStocksController::class)->names([
    'index'   => 'manager.low-stocks.index',
    'create'  => 'manager.low-stocks.create',
    'store'   => 'manager.low-stocks.store',
    'show'    => 'manager.low-stocks.show',
    'edit'    => 'manager.low-stocks.edit',
    'update'  => 'manager.low-stocks.update',
    'destroy' => 'manager.low-stocks.destroy',
]);

// Supplier CRUD
Route::resource('/suppliers', SupplierController::class)->names([
    'index'   => 'manager.suppliers.index',
    'create'  => 'manager.suppliers.create',
    'store'   => 'manager.suppliers.store',
    'show'    => 'manager.suppliers.show',
    'edit'    => 'manager.suppliers.edit',
    'update'  => 'manager.suppliers.update',
    'destroy' => 'manager.suppliers.destroy',
]);

// Categories CRUD
Route::resource('categories', CategoryController::class)->names([
    'index'   => 'manager.categories.index',
    'create'  => 'manager.categories.create',
    'store'   => 'manager.categories.store',
    'show'    => 'manager.categories.show',
    'edit'    => 'manager.categories.edit',
    'update'  => 'manager.categories.update',
    'destroy' => 'manager.categories.destroy',
]);

    // Product CRUD
    Route::get('/products', [ProductController::class, 'index'])->name('manager.products');
    Route::get('/products/{id}/edit-product', [ProductController::class, 'edit'])->name('manager.products.edit');
    Route::put('/products/{id}/edit-product', [ProductController::class, 'update'])->name('manager.products.update');
    Route::post('/products/create-product', [ProductController::class, 'storeProduct'])->name('manager.products.storeProduct');
    Route::post('/products/create-category', [ProductController::class, 'createCategory'])->name('manager.products.createCategory');

    //Api
    Route::get('/api/category', [ApiController::class, 'apiCategory'])->middleware(['auth'])->name('manager.apiCategory.get');
    Route::get('/api/products', [ApiController::class, 'apiProduct'])->middleware(['auth'])->name('manager.apiProduct.get');
});