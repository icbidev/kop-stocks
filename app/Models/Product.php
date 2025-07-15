<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Category;
use App\Models\DeliveryItem;
use App\Models\Delivery;
use App\Models\Supplier;
use App\Models\WeightUnits;
use App\Models\ProductMovement;
use App\Traits\Auditable;

class Product extends Model
{
    use HasFactory , Auditable;

    protected $fillable = [
        'name',
        'category_id',
        'supplier_id',
        'weight_unit_id',
        'description',
        'quantity',
        'standard_order',
        'minimum_quantity'
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function syncSuppliersWithAudit(array $supplierIds)
    {
        $oldSuppliers = $this->suppliers()->get()->sortBy('id')->values()->toArray();
        $newSuppliers = \App\Models\Supplier::whereIn('id', $supplierIds)->get()->sortBy('id')->values()->toArray();
        
    
        // Only sync if they actually differ
        if ($oldSuppliers !== $newSuppliers) {
            $timestamp = now();
            $syncData = [];

            foreach ($supplierIds as $id) {
                // Make sure $id is a scalar (int or string)
                $id = is_array($id) ? ($id['id'] ?? null) : $id;
            
                if ($id !== null) {
                    $syncData[$id] = [
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
            }
            
    
            $this->suppliers()->sync($syncData);
    
            // ✅ Fire custom audit entry
            \App\Models\AuditLog::create([
                'model_type' => self::class,
                'model_id' => $this->id,
                'event' => 'sync-suppliers',
                'old_values' => ['supplier_ids' => $oldSuppliers],
                'new_values' => ['supplier_ids' => $newSuppliers],
                'user_id' => auth()->id(),
            ]);
        }
    }
    
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier')->withTimestamps();
    }

    public function weight_unit()
    {
        return $this->belongsTo(WeightUnits::class);
    }
    

    public function movements() {
        return $this->hasMany(ProductMovement::class);
    }

    public function getCurrentQuantityAttribute() {
        $in = $this->movements()->where('type', 'in')->sum('quantity');
        $out = $this->movements()->where('type', 'out')->sum('quantity');
        return $this->quantity + $in - $out;
    }

    public function deliveries()
{
    return $this->hasManyThrough(DeliveryItem::class, Delivery::class);
}

public function getDeliveredQuantityAttribute()
{
    return $this->hasMany(DeliveryItem::class)->sum('quantity');
}

}
