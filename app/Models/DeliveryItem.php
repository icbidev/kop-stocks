<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
class DeliveryItem extends Model
{
    protected $fillable = ['product_id', 'quantity', 'weight_per_item','type'];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalWeightAttribute()
    {
        return $this->quantity * $this->weight_per_item;
    }
}
