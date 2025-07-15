<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Delivery;
use App\Models\Product;
use App\Observers\ProductObserver;
use App\Observers\DeliveryObserver;

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

        Delivery::observe(DeliveryObserver::class);
    }
}
