<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\Product;
use Inertia\Inertia;
class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Inertia::render('Delivery/Index', [
            'deliveries' => Delivery::with('product')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Deliveries/Create', [
            'products' => Product::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'received_by' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'weight_per_item' => 'required|numeric|min:0',
            'type' => 'required|string|in:in,out',
        ]);
    
        $product = Product::find($validated['product_id']);
    
        if (!$product) {
            return back()->withErrors(['product_id' => 'Product not found.']);
        }
    
        // For "out" type, check if enough quantity
        if ($validated['type'] === 'out' && $product->quantity < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Insufficient stock for this product.']);
        }
    
        // Proceed to create delivery
        $delivery = Delivery::create($validated);
    
        // Adjust quantity based on type
        if ($validated['type'] === 'in') {
            $product->quantity += $validated['quantity'];
        } elseif ($validated['type'] === 'out') {
            $product->quantity -= $validated['quantity'];
        }
    
        $product->save();
    
        return redirect()->route('delivery.index')->with('success', 'Delivery Created.');
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Delivery $delivery)
    {
        return Inertia::render('Deliveries/Edit', [
            'delivery' => $delivery,
            'products' => Product::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'received_by' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'weight_per_item' => 'required|numeric|min:0',
            'type' => 'required|string',
        ]);



        $delivery->update($validated);

        return redirect()->route('delivery.index')->with('success', 'Delivery updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $delivery,$id)
    {
        $delivery->where('id',$id)->delete();

        return back()->with('success', 'Delivery deleted successfully.');
    }
}
