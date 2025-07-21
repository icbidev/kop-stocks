<?php


use Illuminate\Support\Facades\Route;


/*
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
    // Inventory CRUD
    Route::resource('/inventory', InventoryController::class);
    // Low Stocks CRUD
    Route::resource('/low-stocks', LowStocksController::class);
    // Weight Units CRUD
    Route::resource('/weight-units', WeightUnitsController::class);
    // Supplier CRUD
    Route::resource('/suppliers', SupplierController::class);
    // Categories CRUD
    Route::resource('categories', CategoryController::class);
    // Delivery CRUD
    Route::resource('/delivery', DeliveryController::class);
    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.logs');


    Route::get('/delivery', [DeliveryController::class, 'index'])->name('delivery.index');
    Route::post('/delivery/create', [DeliveryController::class, 'store'])->name('products.store');
    Route::put('/delivery/{id}', [DeliveryController::class, 'update'])->name('products.update');
    Route::delete('/delivery/delete/{id}', [DeliveryController::class, 'destroy'])->name('products.delete');
    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/{id}/edit-product', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}/edit-product', [ProductController::class, 'update'])->name('products.update');
    Route::post('/products/create-product', [ProductController::class, 'storeProduct'])->name('products.storeProduct');
    Route::post('/products/create-category', [ProductController::class, 'createCategory'])->name('products.createCategory');

    // Add/Subtract Quantity to/from a Product
    Route::post('/products/{product}/add', [ProductMovementController::class, 'add'])->name('products.add');
    Route::post('/products/{product}/subtract', [ProductMovementController::class, 'subtract'])->name('products.subtract');

    // Daily Report View
    Route::get('/reports', [ReportController::class, 'index'])->middleware(['auth'])->name('reports.index');

    //Api
    Route::get('/api/category', [ApiController::class, 'apiCategory'])->middleware(['auth'])->name('apiCategory.get');
    Route::get('/api/products', [ApiController::class, 'apiProduct'])->middleware(['auth'])->name('apiProduct.get');
    Route::get('/api/delivery', [ApiController::class, 'apiDelivery'])->middleware(['auth'])->name('apiDelivery.get');
});
 */