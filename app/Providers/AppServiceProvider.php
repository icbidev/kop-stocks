<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Delivery;
use App\Models\Product;
use App\Observers\ProductObserver;
use App\Observers\DeliveryObserver;
use Auth;
use Illuminate\Support\Facades\Route;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Inertia::share([
            'flash' => function () {
                return [
                    'success' => session('success'),
                    'error' => session('error'),
                ];
            },
 
        ]);
        Inertia::share([
            'notifications' => fn () => auth()->check()
                ? auth()->user()->unreadNotifications
                : [],
        ]);
        

        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    
        Route::middleware(['web', 'restrict.access'])
            ->prefix('admin')
            ->group(base_path('routes/admin.php'));
    
        Route::middleware(['web', 'restrict.access'])
            ->prefix('cashier')
            ->group(base_path('routes/cashier.php'));
    
        Route::middleware(['web', 'restrict.access'])
            ->prefix('manager')
            ->group(base_path('routes/manager.php'));

        Delivery::observe(DeliveryObserver::class);
    }
}
