<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Http\Requests\StoreMovementRequest;
use App\Models\Movement;
use App\Models\WeightUnits;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['movements', 'category', 'suppliers', 'weight_unit'])
        ->get()
        ->map(function ($product) {
            $product->supplier_ids = $product->suppliers->pluck('id');
            return $product;
        });
        return Inertia::render('Products/Index', [
            'products' => $products,
            'category' => Category::with('products')->get(),
            'weight_units' => WeightUnits::all(),
            'supplier' => Supplier::all(),
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back();
    }

    public function create()
    {
        return Inertia::render('Products/Create', [
            'categories' => Category::all(),
        ]);
    }

    public function createCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $product = Category::create([
            'name' => $validated['name'],
        ]);
    
        return back()->with('success', 'Category created successfully.');
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'supplier_ids' => 'required|array',
            'supplier_ids.*' => 'exists:suppliers,id',
            'minimum_quantity' => 'required|numeric',
            'standard_order' => 'required|numeric',
            'weight_unit_id' => 'required|string|exists:weight_units,name',
            'category_id' => 'required|integer|exists:categories,id',
        ]);
        $name = $request->input('weight_unit_id'); // e.g., 'kg'

        $weightUnit = WeightUnits::where('name', $name)->first();


        if (!$weightUnit) {
            return back()->withErrors(['weight_unit_id' => 'The selected weight unit is invalid.']);
        }
        
        $product = Product::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'quantity' => $validated['quantity'],
            'standard_order' => $validated['standard_order'],
            'weight_unit_id' => $weightUnit->id,
            'minimum_quantity' => $validated['minimum_quantity'],
        ]);
        $supplierIds = $validated['supplier_ids'] ?? [];
        $product->suppliers()->sync($validated['supplier_ids'] ?? []);
        $existingSupplierIds = $product->suppliers()->pluck('suppliers.id')->sort()->values()->toArray();
        $newSupplierIds = collect($supplierIds)->sort()->values()->toArray();
        
        if ($existingSupplierIds !== $newSupplierIds) {
            $product->syncSuppliersWithAudit($newSupplierIds);
        }

        return back()->with('success', 'Product created successfully.');
    }

    

    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => $product->load('category'),
            'categories' => Category::all()
        ]);
    }

    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
            'name' => 'required|string',
            'minimum_quantity' => 'nullable|numeric',
            'standard_order' => 'nullable|numeric',
            'weight_unit_id' => 'required|exists:weight_units,id',
            'supplier_ids' => 'nullable|array',
            'supplier_ids.*' => 'exists:suppliers,id',
        ]);
    
   
            $product = Product::findOrFail($id);
            $product->update($validated);
            $supplierIds = $validated['supplier_ids'] ?? [];
            $product->suppliers()->sync($validated['supplier_ids'] ?? []);
            $existingSupplierIds = $product->suppliers()->pluck('suppliers.id')->sort()->values()->toArray();
            $newSupplierIds = collect($supplierIds)->sort()->values()->toArray();
            
            if ($existingSupplierIds !== $newSupplierIds) {
                $product->syncSuppliersWithAudit($newSupplierIds);
            }





        return back()->with('success', 'Product updated successfully.');
    }
    
    

    public function addQuantity(Product $product, StoreMovementRequest $request)
    {
        $product->movements()->create([
            'type' => 'in',
            'quantity' => $request->quantity,
        ]);

        return redirect()->back()->with('success', 'Quantity added!');
    }
}
