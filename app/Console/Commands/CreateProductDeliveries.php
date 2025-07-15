<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Delivery;

class CreateProductDeliveries extends Command
{
    protected $signature = 'deliveries:create';

    protected $description = 'Create a delivery for each product and get the latest delivery';

    public function handle()
    {
        $products = Product::latest()->get();

        foreach ($products as $product) {
            // Create a new delivery for each product
            $lastDelivery = Delivery::where('product_id', $product->id)
            ->orderByDesc('created_at')
            ->first();
            
            $delivery = Delivery::create([
                'product_id' => $product->id,
                'quantity' => $product->quantity,
                'weight_per_item' => $lastDelivery->weight_per_item,
                'type' => 'end',
                'received_by' => 'system',
                'notes' => 'End of day record',
            ]);
        
            // Get the last delivery for this product (order by created_at desc)

        
            // You can log or do something with $lastDelivery
            info("Last delivery for product {$product->name} (ID {$product->id}): Delivery ID {$lastDelivery->id}");
        }

        return Command::SUCCESS;
    }
}
