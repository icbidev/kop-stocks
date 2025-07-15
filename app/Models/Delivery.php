<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DeliveryItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Delivery extends Model
{

    use HasFactory, Auditable;

    protected $fillable = ['received_by', 'notes','product_id', 'quantity', 'weight_per_item','type'];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
}