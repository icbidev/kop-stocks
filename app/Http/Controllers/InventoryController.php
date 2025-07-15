<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Inertia\Inertia;
use App\Models\WeightUnits;
use DB;
use Illuminate\Support\Carbon;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['movements', 'category', 'suppliers', 'weight_unit'])
            ->get()
            ->map(function ($product) {
                $product->supplier_ids = $product->suppliers->pluck('id');
                return $product;
            });
    
        return Inertia::render('Inventory/Index', [
            'products' => $products,
            'category' => Category::with('products')->get(),
            'weight_units' => WeightUnits::all(),
            'supplier' => Supplier::all(),
        ]);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'supplier_ids' => 'array',
            'supplier_ids.*' => 'exists:suppliers,id',
            'quantity' => 'required|numeric',
            'minimum_quantity' => 'nullable|numeric',
            'standard_order' => 'nullable|numeric',
            'weight_unit_id' => 'required|exists:weight_units,id',
        ]);
        $product = Product::findOrFail($request->id);
        // ✅ Update product fields
        $product->update([
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'minimum_quantity' => $validated['minimum_quantity'],
            'standard_order' => $validated['standard_order'],
            'weight_unit_id' => $validated['weight_unit_id'],
        ]);


        $supplierIds = $validated['supplier_ids'] ?? [];

        $existingSupplierIds = $product->suppliers()->pluck('suppliers.id')->sort()->values()->toArray();
        $newSupplierIds = collect($supplierIds)->sort()->values()->toArray();
        
        if ($existingSupplierIds !== $newSupplierIds) {
            $product->syncSuppliersWithAudit($newSupplierIds);
        }
        
        
    
        return back();
    }
    
    
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
