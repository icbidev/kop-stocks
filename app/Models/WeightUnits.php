<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Traits\Auditable;

class WeightUnits extends Model
{

 use Auditable;
    protected $fillable = ['name'];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
