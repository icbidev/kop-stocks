<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\StoreMovementRequest;
use App\Models\Movement;
use App\Models\WeightUnits;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Products/Index', [
            'products' => Product::with(['movements', 'category', 'weight_unit'])->get(),
            'weight_units' => WeightUnits::all()
        ]);
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
            'minimum_quantity' => 'required|numeric',
            'weight_unit_id' => 'required|string',
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
            'weight_unit_id' => $weightUnit->id,
            'minimum_quantity' => $validated['minimum_quantity'],
        ]);
    
    
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
            'name' => 'required|string|max:255',
            'minimum_quantity' => 'required|numeric',
            'category_id' => 'required|integer|exists:categories,id',
        ]);
    
        $product = Product::findOrFail($id);
    
        $product->update($validated);
    
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
