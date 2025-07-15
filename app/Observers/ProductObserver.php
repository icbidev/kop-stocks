<?php

namespace App\Observers;

use App\Models\Delivery;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Notifications\OutOfStockNotification;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        if (!$product) {
            return;
        }

        // Recalculate product quantity if necessary here
        $currentQuantity = $product->quantity;

        if ($currentQuantity <= 10 && $currentQuantity >= 1) {
            $admin = User::where('email', 'chrisangeles30@gmail.com')->first();
            if ($admin) {
                $admin->notify(new LowStockNotification(collect([$product])));
            }
        }

        if ($currentQuantity == 0) {
            $admin = User::where('email', 'chrisangeles30@gmail.com')->first();
            if ($admin) {
                $admin->notify(new OutOfStockNotification($product));
            }
        }
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
