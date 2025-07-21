<?php


use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\NotificationController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\ApiController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LowStocksController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WeightUnitsController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ReportController;

use App\Http\Controllers\AuditLogController;
Route::middleware(['auth', 'restrict.access'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('admin.dashboard');

    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->middleware(['auth', 'verified'])->name('admin.notifications.destroy');

    Route::get('verify-email', EmailVerificationPromptController::class)
    ->name('admin.verification.notice');

Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('admin.verification.verify');

Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('admin.verification.send');

Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
    ->name('admin.password.confirm');

Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('admin.logout');

// Roles CRUD
Route::resource('/roles', RolesController::class)->names([
    'index'   => 'admin.roles.index',
    'create'  => 'admin.roles.create',
    'store'   => 'admin.roles.store',
    'edit'    => 'admin.roles.edit',
    'update'  => 'admin.roles.update',
    'destroy' => 'admin.roles.destroy',
]);


// Users CRUD
Route::resource('/users', UsersController::class)->names([
        'index'   => 'admin.users.index',
        'create'  => 'admin.users.create',
        'store'   => 'admin.users.store',
        'edit'    => 'admin.users.edit',
        'update'  => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
]);

// Inventory CRUD
Route::resource('/inventory', InventoryController::class)->names([
    'index'   => 'admin.inventory.index',
    'create'  => 'admin.inventory.create',
    'store'   => 'admin.inventory.store',
    'show'    => 'admin.inventory.show',
    'edit'    => 'admin.inventory.edit',
    'update'  => 'admin.inventory.update',
    'destroy' => 'admin.inventory.destroy',
]);

// Low Stocks CRUD
Route::resource('/low-stocks', LowStocksController::class)->names([
    'index'   => 'admin.low-stocks.index',
    'create'  => 'admin.low-stocks.create',
    'store'   => 'admin.low-stocks.store',
    'show'    => 'admin.low-stocks.show',
    'edit'    => 'admin.low-stocks.edit',
    'update'  => 'admin.low-stocks.update',
    'destroy' => 'admin.low-stocks.destroy',
]);

// Weight Units CRUD
Route::resource('/weight-units', WeightUnitsController::class)->names([
    'index'   => 'admin.weight-units.index',
    'create'  => 'admin.weight-units.create',
    'store'   => 'admin.weight-units.store',
    'show'    => 'admin.weight-units.show',
    'edit'    => 'admin.weight-units.edit',
    'update'  => 'admin.weight-units.update',
    'destroy' => 'admin.weight-units.destroy',
]);

// Supplier CRUD
Route::resource('/suppliers', SupplierController::class)->names([
    'index'   => 'admin.suppliers.index',
    'create'  => 'admin.suppliers.create',
    'store'   => 'admin.suppliers.store',
    'show'    => 'admin.suppliers.show',
    'edit'    => 'admin.suppliers.edit',
    'update'  => 'admin.suppliers.update',
    'destroy' => 'admin.suppliers.destroy',
]);

// Categories CRUD
Route::resource('categories', CategoryController::class)->names([
    'index'   => 'admin.categories.index',
    'create'  => 'admin.categories.create',
    'store'   => 'admin.categories.store',
    'show'    => 'admin.categories.show',
    'edit'    => 'admin.categories.edit',
    'update'  => 'admin.categories.update',
    'destroy' => 'admin.categories.destroy',
]);

// Delivery CRUD
Route::resource('/delivery', DeliveryController::class)->names([
    'index'   => 'admin.delivery.index',
    'create'  => 'admin.delivery.create',
    'store'   => 'admin.delivery.store',
    'show'    => 'admin.delivery.show',
    'edit'    => 'admin.delivery.edit',
    'update'  => 'admin.delivery.update',
    'destroy' => 'admin.delivery.destroy',
]);

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit.logs');

/*
    Route::get('/delivery', [DeliveryController::class, 'index'])->name('admin.delivery.index');
    Route::post('/delivery/create', [DeliveryController::class, 'store'])->name('admin.products.store');
    Route::put('/delivery/{id}', [DeliveryController::class, 'update'])->name('admin.products.update');
    Route::delete('/delivery/delete/{id}', [DeliveryController::class, 'destroy'])->name('admin.products.delete');
     */
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products');
    Route::get('/products/{id}/edit-product', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}/edit-product', [ProductController::class, 'update'])->name('admin.products.update');
    Route::post('/products/create-product', [ProductController::class, 'storeProduct'])->name('admin.products.storeProduct');
    Route::post('/products/create-category', [ProductController::class, 'createCategory'])->name('admin.products.createCategory');


    // Daily Report View
    Route::get('/reports', [ReportController::class, 'index'])->middleware(['auth'])->name('admin.reports.index');

    //Api
    Route::get('/api/category', [ApiController::class, 'apiCategory'])->middleware(['auth'])->name('admin.apiCategory.get');
    Route::get('/api/products', [ApiController::class, 'apiProduct'])->middleware(['auth'])->name('admin.apiProduct.get');
    Route::get('/api/delivery', [ApiController::class, 'apiDelivery'])->middleware(['auth'])->name('admin.apiDelivery.get');
});