<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Category extends Model
{
    use HasFactory,Auditable;

    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function category()
    {
    return $this->belongsTo(Category::class);
    }

}
