<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class LowStockNotification extends Notification
{
    use Queueable;

    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function via($notifiable)
    {
        return ['database']; // ✅ ensure database channel is used
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Some products are low in stock.',
            'products' => $this->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $product->quantity,
                ];
            }),
        ];
    }
}
