<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Models\Product;
class Supplier extends Model
{
 use Auditable;
    protected $fillable = [
        'name',
    ];
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
    



}
