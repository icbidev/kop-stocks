<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OutOfStockNotification extends Notification
{
    use Queueable;
    protected $product;
    /**
     * Create a new notification instance.
     */
    public function __construct($product)
    {
        $this->product = $product;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database'];
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Your '.$this->product->name.'is out of stock',
            'products' => [
                [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'quantity' => $this->product->quantity,
                ]
            ],
        ];
        
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
}
