<?php

namespace App\Observers;

use App\Models\Delivery;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Notifications\OutOfStockNotification;

class DeliveryObserver
{
    /**
     * Handle the Delivery "created" event.
     */
    public function created(Delivery $delivery)
    {
        
        $product = $delivery->product;

        if (!$product) {
            return;
        }

        // Recalculate product quantity if necessary here
        $currentQuantity = $product->quantity -= $delivery->quantity;

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
     * Handle the Delivery "updated" event.
     */
    public function updated(Delivery $delivery): void
    {
        //
    }

    /**
     * Handle the Delivery "deleted" event.
     */
    public function deleted(Delivery $delivery): void
    {
        //
    }

    /**
     * Handle the Delivery "restored" event.
     */
    public function restored(Delivery $delivery): void
    {
        //
    }

    /**
     * Handle the Delivery "force deleted" event.
     */
    public function forceDeleted(Delivery $delivery): void
    {
        //
    }
}
